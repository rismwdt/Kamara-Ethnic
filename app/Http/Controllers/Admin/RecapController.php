<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Performer;
use App\Models\PerformerRequirement;
use App\Models\PerformerRole;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecapController extends Controller
{
    public function index(Request $request)
    {
        $totalPerformer   = Performer::where('is_active', 1)->count();

        $prioritasUtama   = PerformerRequirement::join('performer_roles as rr','rr.id','=','performer_requirements.performer_role_id')
                                ->select('rr.name', DB::raw('SUM(performer_requirements.quantity) as total'))
                                ->groupBy('rr.name')
                                ->orderByDesc('total')
                                ->value('rr.name') ?? '-';

        $kapasitasHariIni = PerformerRequirement::join('bookings as b','b.event_id','=','performer_requirements.event_id')
                                ->whereDate('b.date', today())
                                ->sum('performer_requirements.quantity');

        $totalKapasitas   = $totalPerformer;

        $avgDurasi        = Booking::whereDate('date', today())
                                ->selectRaw('AVG(TIME_TO_SEC(TIMEDIFF(end_time,start_time)))/3600 as h')
                                ->value('h');
        $avgDurasi        = $avgDurasi ? round($avgDurasi, 1) : 0;

        $rekap = $this->buildGreedyRekap(/* eventId = null (global) */)->take(50);

        return view('admin.rekap.index', compact(
            'totalPerformer','prioritasUtama','kapasitasHariIni','totalKapasitas','avgDurasi','rekap'
        ));
    }

    private function customerPriorityScore(Booking $b): float
    {
        if ($b->priority === 'darurat') return 1.00;
        if ($b->is_family)              return 0.70;
        return 0.00;
    }

    private function buildGreedyRekap(?int $eventId = null): Collection
    {
        $start = today()->toDateString();
        $end   = today()->addDays(7)->toDateString();

        $bookings = Booking::query()
            ->when($eventId, fn($q) => $q->where('event_id', $eventId))
            ->whereBetween('date', [$start, $end])
            ->get(['id','event_id','booking_code','client_name','date','created_at','priority','is_family']);

        if ($bookings->isEmpty()) return collect();

        $reqs = PerformerRequirement::query()
            ->when($eventId, fn($q) => $q->where('event_id', $eventId))
            ->get(['event_id','performer_role_id','quantity']);

        if ($reqs->isEmpty()) return collect();

        $roleNames = PerformerRole::pluck('name','id');

        $minDate    = $bookings->min('date');       $maxDate    = $bookings->max('date');
        $minCreated = $bookings->min('created_at'); $maxCreated = $bookings->max('created_at');

        $valuePerEvent = $reqs->groupBy('event_id')->map(fn($rows)=>(int)$rows->sum('quantity'));
        $kindsPerEvent = $reqs->groupBy('event_id')->map(fn($rows)=>(int)$rows->count());

        $minValue = $valuePerEvent->min() ?: 0;   $maxValue = $valuePerEvent->max() ?: 0;
        $minKinds = $kindsPerEvent->min() ?: 0;   $maxKinds = $kindsPerEvent->max() ?: 0;

        $norm = function (float $x, float $min, float $max): float {
            if ($max <= $min) return 0.5;
            return ($x - $min) / ($max - $min);
        };

        $rows = collect();

        foreach ($bookings as $b) {
            $eventReqs = $reqs->where('event_id', $b->event_id);
            if ($eventReqs->isEmpty()) continue;

            $deadlineScore = 1 - $norm(
                (float) Carbon::parse($b->date)->timestamp,
                (float) Carbon::parse($minDate)->timestamp,
                (float) Carbon::parse($maxDate)->timestamp
            );
            $timeScore = $norm(
                (float) Carbon::parse($b->created_at)->timestamp,
                (float) Carbon::parse($minCreated)->timestamp,
                (float) Carbon::parse($maxCreated)->timestamp
            );
            $valueScore = $norm((float)($valuePerEvent[$b->event_id] ?? 0), (float)$minValue, (float)$maxValue);
            $complexityScore = 1 - $norm((float)($kindsPerEvent[$b->event_id] ?? 0), (float)$minKinds, (float)$maxKinds);
            $customerScore = $this->customerPriorityScore($b);

            $priority = (0.40*$deadlineScore) + (0.25*$valueScore) + (0.20*$complexityScore)
                      + (0.10*$customerScore) + (0.05*$timeScore);

            $pairs = $eventReqs->map(fn($r)=>[
                'role' => $roleNames[$r->performer_role_id] ?? ('Peran #'.$r->performer_role_id),
                'qty'  => (int)$r->quantity,
            ])->sortBy('role')->values();

            $rolesList  = $pairs->map(fn($p)=>"{$p['role']} ({$p['qty']})")->all();
            $statusList = $pairs->map(fn($p)=>$p['qty'].' '.$p['role'])->all();

            $customerLabel = $b->priority === 'darurat' ? 'Darurat' : ($b->is_family ? 'Keluarga' : 'Normal');

            $rows->push((object)[
                'kode'           => ($b->booking_code ?? ('BK-'.$b->id)),
                'klien'          => $b->client_name,
                'roles_list'     => $rolesList,
                'deadline'       => $b->date,
                'value'          => $valuePerEvent[$b->event_id] ?? 0,
                'complexity'     => $kindsPerEvent[$b->event_id] ?? 0,
                'customer'       => $customerLabel,
                'time'           => Carbon::parse($b->created_at)->format('Y-m-d'),
                'priority_score' => number_format($priority, 3),
                'status_list'    => $statusList,
            ]);
        }

        return $rows->sortByDesc('priority_score')->values();
    }
}
