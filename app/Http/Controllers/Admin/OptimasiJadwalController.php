<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PerformerRequirement;
use App\Models\PerformerRole;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class OptimasiJadwalController extends Controller
{
    public function index(Request $request)
{
    $from = $request->query('from', today()->toDateString());
    $to   = $request->query('to',   today()->addDays(7)->toDateString());

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

    private function buildGreedy(string $from, string $to, array $w): Collection
    {
        $bookings = Booking::whereBetween('date', [$from, $to])
            ->get(['id','event_id','booking_code','client_name','date','created_at','priority','is_family','nuance','notes']);

        if ($bookings->isEmpty()) return collect();

        $reqs = PerformerRequirement::whereIn('event_id', $bookings->pluck('event_id'))
            ->get(['event_id','performer_role_id','quantity']);
        if ($reqs->isEmpty()) return collect();

        $roleNames = PerformerRole::pluck('name', 'id');

        $minDate = $bookings->min('date');       $maxDate = $bookings->max('date');
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

            $priority = ($w['deadline']   * $deadlineScore)
                      + ($w['value']      * $valueScore)
                      + ($w['complexity'] * $complexityScore)
                      + ($w['customer']   * $customerScore)
                      + ($w['time']       * $timeScore);

            $pairs = $eventReqs->map(fn($r)=>[
    'role' => $roleNames[$r->performer_role_id] ?? ('Peran #'.$r->performer_role_id),
    'qty'  => (int)$r->quantity,
])->sortBy('role')->values();

$rolesList  = $pairs->map(fn($p)=>"{$p['role']} ({$p['qty']})")->all();
$statusList = $pairs->map(fn($p)=>$p['qty'].' '.$p['role'])->all();

$customerScore = $b->priority === 'darurat' ? 1.0 : ($b->is_family ? 0.7 : 0.0);
$customerLabel = $b->priority === 'darurat' ? 'Darurat' : ($b->is_family ? 'Keluarga' : 'Normal');

$rows->push((object)[
    'kode'           => ($b->booking_code ?? ('BK-'.$b->id)),
    'klien'          => $b->client_name,
    'roles_list'     => $rolesList,
    'deadline'       => $b->date,
    'value'          => $valuePerEvent[$b->event_id] ?? 0,
    'complexity'     => $kindsPerEvent[$b->event_id] ?? 0,
    'customer'       => $customerLabel,
    'time'           => \Carbon\Carbon::parse($b->created_at)->format('Y-m-d'),
    'priority_score' => number_format($priority, 3),
    'status_list'    => $statusList,
]);
        }

        return $rows->sortByDesc('priority_score')->values();
    }
}
