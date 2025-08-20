<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Performer;
use App\Models\PerformerRequirement;
use App\Models\PerformerRole;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        // ==== Bagian dashboard lama (kartu ringkas) ====
        $bookings = Booking::with(['event','performers'])->get();

        $totalPendapatan = $bookings->sum(fn ($b) => $b->event->price ?? 0);

        $pendapatanBulanIni = $bookings
            ->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum(fn ($b) => $b->event->price ?? 0);

        $totalJadwal = $bookings->count();

        $jadwalBulanIni = $bookings
            ->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $jumlahKlien = $bookings->unique('email')->count();

        $jumlahKlienBulanIni = Booking::whereMonth('date', now()->month)
            ->distinct('email')
            ->count('email');

        $today     = now()->toDateString();
        $endOfWeek = now()->endOfWeek()->toDateString();

        $jadwalMingguIni = $bookings
            ->whereBetween('date', [$today, $endOfWeek])
            ->sortBy(fn ($b) => $b->date.' '.$b->start_time)
            ->take(5)
            ->values();

        $tanggalDenganAcara = Booking::whereMonth('date', now()->month)
            ->selectRaw('date, COUNT(*) as jumlah')
            ->groupBy('date')
            ->get()
            ->mapWithKeys(fn ($row) => [Carbon::parse($row->date)->format('Y-m-d') => $row->jumlah]);

        // ==== Card tambahan (kapasitas, performer, durasi) ====
        $totalPerformer   = Performer::count();

        $maxEventsPerDay  = 5; // kebijakan
        $kapasitasHariIni = Booking::whereDate('date', today())
            ->whereIn('status', ['tertunda','diterima'])
            ->count();
        $totalKapasitas   = $maxEventsPerDay;

        $avgDurasi = round(
            $bookings->filter(fn($b) => $b->start_time && $b->end_time)->avg(function ($b) {
                $s = Carbon::parse($b->start_time);
                $e = Carbon::parse($b->end_time);
                return $s->diffInMinutes($e) / 60;
            }) ?? 0,
            2
        );

        // ==== Rekap prioritas TOP 5 (logika sama seperti OptimasiJadwalController) ====
        // Bobot default (boleh disetel)
        $weightsPct = ['deadline'=>40, 'value'=>25, 'complexity'=>20, 'customer'=>10, 'time'=>5];
        $sum = max(1, array_sum($weightsPct));
        $w   = array_map(fn($v)=>$v/$sum, $weightsPct);

        // Rentang 7 hari ke depan (bisa diubah)
        $from = today()->toDateString();
        $to   = today()->addDays(7)->toDateString();

        $rekapFull = $this->buildGreedyRecap($from, $to, $w);
        $rekap     = $rekapFull->take(5)->values();

        $prioritasUtama = $rekap->first()->kode ?? '-';

        return view('dashboard', compact(
            'totalPendapatan',
            'pendapatanBulanIni',
            'totalJadwal',
            'jadwalBulanIni',
            'jumlahKlien',
            'jumlahKlienBulanIni',
            'jadwalMingguIni',
            'tanggalDenganAcara',
            'totalPerformer',
            'prioritasUtama',
            'kapasitasHariIni',
            'totalKapasitas',
            'avgDurasi',
            'rekap'
        ));
    }

    // ================= helper rekap prioritas (copy of "terbaik") =================

    private function customerPriorityScore(Booking $b): float
    {
        if ($b->priority === 'darurat') return 1.00;
        if ($b->is_family)              return 0.70;
        return 0.00;
    }

    private function buildGreedyRecap(string $from, string $to, array $w): Collection
    {
        $bookings = Booking::whereBetween('date', [$from, $to])
            ->get(['id','event_id','booking_code','client_name','date','created_at','priority','is_family']);

        if ($bookings->isEmpty()) return collect();

        $reqs = PerformerRequirement::whereIn('event_id', $bookings->pluck('event_id'))
            ->get(['event_id','performer_role_id','quantity']);
        if ($reqs->isEmpty()) return collect();

        $roleNames = PerformerRole::pluck('name', 'id');

        $minDate    = $bookings->min('date');
        $maxDate    = $bookings->max('date');
        $minCreated = $bookings->min('created_at');
        $maxCreated = $bookings->max('created_at');

        $valuePerEvent = $reqs->groupBy('event_id')->map(fn($rows) => (int)$rows->sum('quantity'));
        $kindsPerEvent = $reqs->groupBy('event_id')->map(fn($rows) => (int)$rows->count());

        $minValue = $valuePerEvent->min() ?: 0;  $maxValue = $valuePerEvent->max() ?: 0;
        $minKinds = $kindsPerEvent->min() ?: 0;  $maxKinds = $kindsPerEvent->max() ?: 0;

        $norm = function (float $x, float $min, float $max): float {
            if ($max <= $min) return 0.5;
            return ($x - $min) / ($max - $min);
        };

        // jumlah performer yang SUDAH mengunci slot per booking per role
        $assignedByBooking = DB::table('booking_performers as bp')
            ->join('performers as p', 'p.id', '=', 'bp.performer_id')
            ->whereIn('bp.booking_id', $bookings->pluck('id'))
            ->whereIn('bp.confirmation_status', ['tertunda','dikonfirmasi'])
            ->groupBy('bp.booking_id','p.performer_role_id')
            ->select('bp.booking_id', 'p.performer_role_id as role_id', DB::raw('COUNT(*) as total'))
            ->get()
            ->groupBy('booking_id')
            ->map(fn($rows) => $rows->pluck('total','role_id'));

        $rows = collect();

        foreach ($bookings as $b) {
            $eventReqs = $reqs->where('event_id', $b->event_id);
            if ($eventReqs->isEmpty()) continue;

            // skor dasar
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
            $complexityScore = 1 - $norm((float)($kindsPerEvent[$b->event_id] ?? 0), (float)$minKinds, (float)$maxKinds);
            $customerScore   = $this->customerPriorityScore($b);

            $priorityRaw = ($w['deadline']   * $deadlineScore)
                         + ($w['value']      * $valueScore)
                         + ($w['complexity'] * $complexityScore)
                         + ($w['customer']   * $customerScore)
                         + ($w['time']       * $timeScore);

            // kebutuhan peran + status pemenuhan
            $pairs = $eventReqs->map(fn($r) => [
                'role_id' => (int)$r->performer_role_id,
                'role'    => $roleNames[$r->performer_role_id] ?? ('Peran #'.$r->performer_role_id),
                'qty'     => (int)$r->quantity,
            ])->sortBy('role')->values();

            $rolesList = $pairs->map(fn($p) => "{$p['role']} ({$p['qty']})")->all();

            $assignedForThis = $assignedByBooking[$b->id] ?? collect();
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
                    $missing[] = sprintf('%d %s (butuh %d, tersedia %d)', $gap, $p['role'], $need, $have);
                }
            }

            // redam skor berdasar gap
            $gapRatio = ($totalRequired > 0) ? ($gapQty / $totalRequired) : 0.0;
            if ($gapRatio <= 0) {
                $priorityEffective = 0.0; // semua terpenuhi → nol
            } else {
                $factor = 0.35 + 0.65 * $gapRatio; // makin kecil gap → makin turun
                $priorityEffective = $priorityRaw * $factor;
            }

            $customerLabel = $b->priority === 'darurat'
                ? 'Darurat'
                : ($b->is_family ? 'Keluarga' : 'Normal');

            $rows->push((object)[
                'kode'               => ($b->booking_code ?? ('BK-'.$b->id)),
                'klien'              => $b->client_name,
                'roles_list'         => $rolesList,
                'deadline'           => $b->date,
                'value'              => $valuePerEvent[$b->event_id] ?? 0, // total kebutuhan orang
                'complexity'         => $kindsPerEvent[$b->event_id] ?? 0, // banyaknya jenis peran
                'customer'           => $customerLabel,
                'time'               => Carbon::parse($b->created_at)->format('Y-m-d'),
                'priority_score'     => number_format($priorityEffective, 3),
                'priority_score_raw' => (float)$priorityEffective,
                'status_ok'          => ($gapQty === 0),
                'status_list'        => $missing,
                'booking_id'         => $b->id,
                'event_id'           => $b->event_id,
            ]);
        }

        return $rows->sortByDesc('priority_score_raw')->values();
    }
}
