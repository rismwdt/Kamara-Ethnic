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
        $bookings = Booking::with('event')->get();

        $totalPendapatan = $bookings->sum(fn ($b) => $b->event->price ?? 0);

        $pendapatanBulanIni = $bookings
            ->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum(fn ($b) => $b->event->price ?? 0);

        $totalJadwal = $bookings->count();

        $jadwalBulanIni = $bookings
            ->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $jumlahKlien = $bookings->unique('email')->count();

        $jumlahKlienBulanIni = Booking::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->distinct('email')
            ->count('email');

        $today     = now()->toDateString();
        $endOfWeek = now()->endOfWeek()->toDateString();

        $jadwalMingguIni = $bookings
            ->whereBetween('date', [$today, $endOfWeek])
            ->sortBy(fn ($b) => $b->date.' '.$b->start_time)
            ->take(5)
            ->values();

        $tanggalDenganAcara = Booking::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->selectRaw('date, COUNT(*) as jumlah')
            ->groupBy('date')
            ->get()
            ->mapWithKeys(fn ($row) => [Carbon::parse($row->date)->format('Y-m-d') => $row->jumlah]);

        $now = now();
        $rangeStart = $now->copy()->startOfMonth()->subMonths(6)->toDateString();
        $rangeEnd   = $now->copy()->endOfMonth()->addMonths(6)->toDateString();

        $rows = Booking::whereBetween('date', [$rangeStart, $rangeEnd])
            ->selectRaw('date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $countsByMonth = [];
        foreach ($rows as $date => $count) {
            $ym = Carbon::parse($date)->format('Y-m'); // kunci bulan
            $d  = Carbon::parse($date)->format('Y-m-d'); // kunci tanggal
            $countsByMonth[$ym][$d] = (int) $count;
        }

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

        $weightsPct = ['deadline'=>40, 'value'=>25, 'complexity'=>20, 'customer'=>10, 'time'=>5];
        $sum = max(1, array_sum($weightsPct));
        $w   = array_map(fn($v)=>$v/$sum, $weightsPct);

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
            'rekap',
            'countsByMonth'
        ));
    }

    private function customerPriorityScore(Booking $b): float
    {
        $priority = $b->getAttribute('priority');
        $isFamily = (bool) $b->getAttribute('is_family');

        if ($priority === 'darurat') return 1.00;
        if ($isFamily)               return 0.70;
        return 0.00;
    }

    private function buildGreedyRecap(string $from, string $to, array $w): Collection
    {
        $bookings = Booking::whereBetween('date', [$from, $to])
            ->get(['id','event_id','booking_code','client_name','date','created_at']);

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

        $assignedByBooking = collect();

        $rows = collect();

        foreach ($bookings as $b) {
            $eventReqs = $reqs->where('event_id', $b->event_id);
            if ($eventReqs->isEmpty()) continue;

            $deadlineScore   = 1 - $this->normTimestamp($b->date, $minDate, $maxDate);
            $timeScore       = $this->normTimestamp($b->created_at, $minCreated, $maxCreated);
            $valueScore      = 1 * $this->normNumber($valuePerEvent[$b->event_id] ?? 0, $minValue, $maxValue);
            $complexityScore = 1 - $this->normNumber($kindsPerEvent[$b->event_id] ?? 0, $minKinds, $maxKinds);
            $customerScore   = $this->customerPriorityScore($b);

            $priorityRaw = ($w['deadline']   * $deadlineScore)
                         + ($w['value']      * $valueScore)
                         + ($w['complexity'] * $complexityScore)
                         + ($w['customer']   * $customerScore)
                         + ($w['time']       * $timeScore);

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

            $gapRatio = ($totalRequired > 0) ? ($gapQty / $totalRequired) : 0.0;
            $priorityEffective = $gapRatio <= 0 ? 0.0 : $priorityRaw * (0.35 + 0.65 * $gapRatio);

            $priority = $b->getAttribute('priority');
            $isFamily = (bool) $b->getAttribute('is_family');

            $customerLabel = $priority === 'darurat'
                ? 'Darurat'
                : ($isFamily ? 'Keluarga' : 'Normal');

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
            ]);
        }

        return $rows->sortByDesc('priority_score_raw')->values();
    }

    private function normTimestamp($ts, $min, $max): float
    {
        $x   = (float) Carbon::parse($ts)->timestamp;
        $min = (float) Carbon::parse($min)->timestamp;
        $max = (float) Carbon::parse($max)->timestamp;
        if ($max <= $min) return 0.5;
        return ($x - $min) / ($max - $min);
    }

    private function normNumber(float $x, float $min, float $max): float
    {
        if ($max <= $min) return 0.5;
        return ($x - $min) / ($max - $min);
    }
}
