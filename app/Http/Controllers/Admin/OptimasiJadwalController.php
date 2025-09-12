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

        // bobot (%) -> dinormalisasi
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
     * Bangun data rekap + skor prioritas + status pemenuhan.
     * Status menampilkan: "Role — butuh N, ditetapkan H, tersedia A".
     */
    private function buildGreedy(string $from, string $to, array $w): Collection
    {
        // Booking pada rentang tanggal
        $bookings = Booking::whereBetween('date', [$from, $to])
            ->get(['id','event_id','booking_code','client_name','date','created_at','priority','is_family','nuance','notes']);

        if ($bookings->isEmpty()) return collect();

        // Kebutuhan per event (role & qty)
        $reqs = PerformerRequirement::whereIn('event_id', $bookings->pluck('event_id'))
            ->get(['event_id','performer_role_id','quantity']);
        if ($reqs->isEmpty()) return collect();

        // Nama role
        $roleNames = PerformerRole::pluck('name', 'id');

        // ----- Komponen skor -----
        $minDate    = $bookings->min('date');
        $maxDate    = $bookings->max('date');
        $minCreated = $bookings->min('created_at');
        $maxCreated = $bookings->max('created_at');

        $valuePerEvent = $reqs->groupBy('event_id')->map(fn($rows) => (int)$rows->sum('quantity')); // total kebutuhan
        $kindsPerEvent = $reqs->groupBy('event_id')->map(fn($rows) => (int)$rows->count());          // macam role

        $minValue = $valuePerEvent->min() ?: 0;  $maxValue = $valuePerEvent->max() ?: 0;
        $minKinds = $kindsPerEvent->min() ?: 0;  $maxKinds = $kindsPerEvent->max() ?: 0;

        $norm = function (float $x, float $min, float $max): float {
            if ($max <= $min) return 0.5;
            return ($x - $min) / ($max - $min);
        };

        // ----- Kapasitas performer per role (total) -----
        $totalPerRole = DB::table('performers')
            ->select('performer_role_id', DB::raw('COUNT(*) as total'))
            ->groupBy('performer_role_id')
            ->pluck('total', 'performer_role_id'); // [role_id => total]

        // ----- Performer yang sibuk per TANGGAL & ROLE (tertunda/dikonfirmasi) -----
        // Catatan: level tanggal (belum memasukkan overlap jam + buffer).
        $busyByDateRole = DB::table('booking_performers as bp')
            ->join('performers as p', 'p.id', '=', 'bp.performer_id')
            ->join('bookings  as b', 'b.id', '=', 'bp.booking_id')
            ->whereIn('bp.confirmation_status', ['tertunda','dikonfirmasi'])
            ->whereBetween('b.date', [$from, $to])
            ->groupBy('b.date','p.performer_role_id')
            ->select('b.date as d', 'p.performer_role_id as role_id', DB::raw('COUNT(*) as total'))
            ->get()
            ->groupBy('d')                                        // [date => rows]
            ->map(fn($rows) => $rows->pluck('total','role_id'));  // [role_id => total]

        // ----- Yang SUDAH ditetapkan per booking & role (progress) -----
        $assignedByBooking = DB::table('booking_performers as bp')
            ->join('performers as p', 'p.id', '=', 'bp.performer_id')
            ->whereIn('bp.booking_id', $bookings->pluck('id'))
            ->whereIn('bp.confirmation_status', ['tertunda','dikonfirmasi'])
            ->groupBy('bp.booking_id','p.performer_role_id')
            ->select('bp.booking_id', 'p.performer_role_id as role_id', DB::raw('COUNT(*) as total'))
            ->get()
            ->groupBy('booking_id')
            ->map(fn($rows) => $rows->pluck('total','role_id')); // [booking_id => [role_id => total]]

        $rows = collect();

        foreach ($bookings as $b) {
            $eventReqs = $reqs->where('event_id', $b->event_id);
            if ($eventReqs->isEmpty()) continue;

            // ----- Skor prioritas -----
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
            // complexity makin tinggi -> skor diturunkan (1 - norm)
            $complexityScore = 1 - $norm((float)($kindsPerEvent[$b->event_id] ?? 0), (float)$minKinds, (float)$maxKinds);
            $customerScore   = $this->customerPriorityScore($b);

            $priorityRaw = ($w['deadline']   * $deadlineScore)
                         + ($w['value']      * $valueScore)
                         + ($w['complexity'] * $complexityScore)
                         + ($w['customer']   * $customerScore)
                         + ($w['time']       * $timeScore);

            // ----- Daftar role & qty pada event ini -----
            $pairs = $eventReqs->map(fn($r) => [
                'role_id' => (int)$r->performer_role_id,
                'role'    => $roleNames[$r->performer_role_id] ?? ('Peran #'.$r->performer_role_id),
                'qty'     => (int)$r->quantity,
            ])->sortBy('role')->values();

            $rolesList = $pairs->map(fn($p) => "{$p['role']} ({$p['qty']})")->all();

            // Progress yg sudah ditetapkan
            $assignedForThis = $assignedByBooking[$b->id] ?? collect(); // [role_id => total]

            // Buat key string tanggal (hindari Carbon sebagai offset)
            $dateKey = $b->date instanceof Carbon
                ? $b->date->toDateString()
                : (string) Carbon::parse($b->date)->toDateString();

            // Map "role_id => total busy" untuk tanggal booking ini
            $busyMapForThisDate = $busyByDateRole->get($dateKey, collect());

            // ----- Status per role -----
            $missing = [];
            $totalRequired = 0;
            $gapQty = 0;

            foreach ($pairs as $p) {
                $need = (int)$p['qty'];
                $have = (int)($assignedForThis[$p['role_id']] ?? 0);

                // performer yang sibuk pada tanggal tsb untuk role ini
                $busyCount = (int) $busyMapForThisDate->get($p['role_id'], 0);

                // kurangi dengan yang SUDAH ditetapkan di booking ini agar tidak double-count
                $busyCount = max(0, $busyCount - $have);

                $totalCapacity = (int) ($totalPerRole[$p['role_id']] ?? 0);

                // kandidat tersedia pada tanggal tsb (tanpa cek menit/buffer)
                $avail = max(0, $totalCapacity - $busyCount);

                $totalRequired += $need;

                if ($have < $need) {
                    $gap = $need - $have;
                    $gapQty += $gap;

                    $missing[] = sprintf(
                        '%s — butuh %d, ditetapkan %d, tersedia %d',
                        $p['role'], $need, $have, $avail
                    );
                }
            }

            // Dampening prioritas berdasar gap terpenuhi (opsional)
            $gapRatio = ($totalRequired > 0) ? ($gapQty / $totalRequired) : 0.0;
            $priorityEffective = $gapRatio <= 0
                ? 0.0
                : ($priorityRaw * (0.35 + 0.65 * $gapRatio));

            $customerLabel = $b->priority === 'darurat'
                ? 'Darurat'
                : ($b->is_family ? 'Keluarga' : 'Normal');

            $rows->push((object)[
                'kode'               => ($b->booking_code ?? ('BK-'.$b->id)),
                'klien'              => $b->client_name,
                'roles_list'         => $rolesList,
                'deadline'           => $b->date,
                'value'              => $valuePerEvent[$b->event_id] ?? 0,
                'complexity'         => $kindsPerEvent[$b->event_id] ?? 0,
                'customer'           => $customerLabel,
                'time'               => Carbon::parse($b->created_at)->format('Y-m-d'),

                'priority_score'     => number_format($priorityEffective, 3),
                'priority_score_raw' => (float)$priorityEffective,

                'status_ok'          => ($gapQty === 0),
                'status_list'        => $missing,

                'booking_id'         => $b->id,
                'event_id'           => $b->event_id,
                'gap_qty'            => $gapQty,
                'total_required'     => $totalRequired,
                'gap_ratio'          => $gapRatio,
            ]);
        }

        return $rows->sortByDesc('priority_score_raw')->values();
    }
}
