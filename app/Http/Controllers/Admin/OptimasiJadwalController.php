<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PerformerRequirement;
use App\Models\PerformerRole;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OptimasiJadwalController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->query('from', today()->toDateString());
        $to   = $request->query('to',   today()->addDays(7)->toDateString());

        // bobot dalam persen → dinormalisasi jadi 0..1
        $weightsPct = [
            'deadline'   => (float)$request->query('w_deadline',   40),
            'value'      => (float)$request->query('w_value',      25),
            'complexity' => (float)$request->query('w_complexity', 20),
            'customer'   => (float)$request->query('w_customer',   10),
            'time'       => (float)$request->query('w_time',        5),
        ];
        $sum = max(1, array_sum($weightsPct));
        $w = array_map(fn($v) => $v / $sum, $weightsPct);

        $rekap = $this->buildGreedy($from, $to, $w)->take(100);

        return view('admin.optimasi-jadwal.index', compact('from', 'to', 'weightsPct', 'rekap'));
    }

    private function customerPriorityScore(Booking $b): float
    {
        if ($b->priority === 'darurat') return 1.00;
        if ($b->is_family)              return 0.70;
        return 0.00;
    }

    /**
     * Bangun data rekap + skor prioritas + status pemenuhan performer.
     */
    private function buildGreedy(string $from, string $to, array $w): Collection
    {
        $bookings = Booking::whereBetween('date', [$from, $to])
            ->get(['id','event_id','booking_code','client_name','date','created_at','priority','is_family','nuance','notes']);

        if ($bookings->isEmpty()) return collect();

        // Kebutuhan per event (per peran)
        $reqs = PerformerRequirement::whereIn('event_id', $bookings->pluck('event_id'))
            ->get(['event_id','performer_role_id','quantity']);
        if ($reqs->isEmpty()) return collect();

        // Nama peran
        $roleNames = PerformerRole::pluck('name', 'id');

        // Range untuk normalisasi skor
        $minDate    = $bookings->min('date');
        $maxDate    = $bookings->max('date');
        $minCreated = $bookings->min('created_at');
        $maxCreated = $bookings->max('created_at');

        // Value: total kebutuhan (kuantitas) per event
        $valuePerEvent = $reqs->groupBy('event_id')->map(fn($rows) => (int)$rows->sum('quantity'));
        // Complexity: jumlah macam peran per event
        $kindsPerEvent = $reqs->groupBy('event_id')->map(fn($rows) => (int)$rows->count());

        $minValue = $valuePerEvent->min() ?: 0;  $maxValue = $valuePerEvent->max() ?: 0;
        $minKinds = $kindsPerEvent->min() ?: 0;  $maxKinds = $kindsPerEvent->max() ?: 0;

        $norm = function (float $x, float $min, float $max): float {
            if ($max <= $min) return 0.5;
            return ($x - $min) / ($max - $min);
        };

        // ==== Hitung performer yang SUDAH di-assign per booking per role (hanya yang mengunci slot) ====
        $assignedByBooking = DB::table('booking_performers as bp')
            ->join('performers as p', 'p.id', '=', 'bp.performer_id')
            ->whereIn('bp.booking_id', $bookings->pluck('id'))
            ->whereIn('bp.confirmation_status', ['tertunda','dikonfirmasi'])
            ->groupBy('bp.booking_id','p.performer_role_id')
            ->select('bp.booking_id', 'p.performer_role_id as role_id', DB::raw('COUNT(*) as total'))
            ->get()
            ->groupBy('booking_id')                              // => [booking_id => collection(rows)]
            ->map(fn($rows) => $rows->pluck('total','role_id')); // => [role_id => total]

        $rows = collect();

        foreach ($bookings as $b) {
            $eventReqs = $reqs->where('event_id', $b->event_id);
            if ($eventReqs->isEmpty()) continue;

            // ===== skor prioritas dasar =====
            $deadlineScore   = 1 - $norm(
                (float) Carbon::parse($b->date)->timestamp,
                (float) Carbon::parse($minDate)->timestamp,
                (float) Carbon::parse($maxDate)->timestamp
            );
            $timeScore       = $norm(
                (float) Carbon::parse($b->created_at)->timestamp,
                (float) Carbon::parse($minCreated)->timestamp,
                (float) Carbon::parse($maxCreated)->timestamp
            );
            $valueScore      = $norm((float)($valuePerEvent[$b->event_id] ?? 0), (float)$minValue, (float)$maxValue);
            // catatan: semakin banyak macam peran → complexity makin tinggi → skor diturunkan (1 - norm)
            $complexityScore = 1 - $norm((float)($kindsPerEvent[$b->event_id] ?? 0), (float)$minKinds, (float)$maxKinds);
            $customerScore   = $this->customerPriorityScore($b);

            $priorityRaw = ($w['deadline']   * $deadlineScore)
                         + ($w['value']      * $valueScore)
                         + ($w['complexity'] * $complexityScore)
                         + ($w['customer']   * $customerScore)
                         + ($w['time']       * $timeScore);

            // ===== daftar kebutuhan (roles_list) =====
            $pairs = $eventReqs->map(fn($r) => [
                'role_id' => (int)$r->performer_role_id,
                'role'    => $roleNames[$r->performer_role_id] ?? ('Peran #'.$r->performer_role_id),
                'qty'     => (int)$r->quantity,
            ])->sortBy('role')->values();

            $rolesList = $pairs->map(fn($p) => "{$p['role']} ({$p['qty']})")->all();

            // ===== status pemenuhan: cek yang sudah assigned per role =====
            $assignedForThis = $assignedByBooking[$b->id] ?? collect(); // [role_id => total]
            $missing = [];

            $totalRequired = 0;
            $gapQty = 0;

            foreach ($pairs as $p) {
                $need = (int)$p['qty'];
                $have = (int)($assignedForThis[$p['role_id']] ?? 0);

                $totalRequired += $need;
                if ($have < $need) {
                    $gap = $need - $have;
                    $gapQty += $gap;

                    $missing[] = sprintf(
                        '%d %s (butuh %d, tersedia %d)',
                        $gap,
                        $p['role'],
                        $need,
                        $have
                    );
                }
            }

            // Dampening prioritas berdasarkan tingkat kekurangan
            // - Semua terpenuhi => skor efektif = 0
            // - Jika belum terpenuhi => faktor = 0.35 + 0.65 * gap_ratio
            $gapRatio = ($totalRequired > 0) ? ($gapQty / $totalRequired) : 0.0;
            if ($gapRatio <= 0) {
                $priorityEffective = 0.0;
            } else {
                $factor = 0.35 + 0.65 * $gapRatio;
                $priorityEffective = $priorityRaw * $factor;
            }

            // label customer
            $customerLabel = $b->priority === 'darurat'
                ? 'Darurat'
                : ($b->is_family ? 'Keluarga' : 'Normal');

            $rows->push((object)[
                'kode'                => ($b->booking_code ?? ('BK-'.$b->id)),
                'klien'               => $b->client_name,
                'roles_list'          => $rolesList,
                'deadline'            => $b->date,
                'value'               => $valuePerEvent[$b->event_id] ?? 0,
                'complexity'          => $kindsPerEvent[$b->event_id] ?? 0,
                'customer'            => $customerLabel,
                'time'                => Carbon::parse($b->created_at)->format('Y-m-d'),

                // Skor tampil (3 desimal) & skor untuk sorting (float)
                'priority_score'      => number_format($priorityEffective, 3),
                'priority_score_raw'  => (float)$priorityEffective,

                // Status pemenuhan
                'status_ok'           => ($gapQty === 0),
                'status_list'         => $missing,

                // opsional: info tambahan
                'booking_id'          => $b->id,
                'event_id'            => $b->event_id,
                'gap_qty'             => $gapQty,
                'total_required'      => $totalRequired,
                'gap_ratio'           => $gapRatio,
            ]);
        }

        // sort by nilai float agar benar
        return $rows->sortByDesc('priority_score_raw')->values();
    }
}
