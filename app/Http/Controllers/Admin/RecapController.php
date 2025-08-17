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
        // Kartu ringkasan (global)
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

        // Rekap prioritas (batasi 1 minggu ke depan)
        $rekap = $this->buildGreedyRekap(/* eventId = null (global) */)->take(50);

        // NOTE: sesuaikan dengan path blade milikmu
        return view('admin.rekap.index', compact(
            'totalPerformer','prioritasUtama','kapasitasHariIni','totalKapasitas','avgDurasi','rekap'
        ));
    }

    /** Skor prioritas pelanggan: darurat > keluarga > normal */
    private function customerPriorityScore(Booking $b): float
    {
        if ($b->priority === 'darurat') return 1.00; // tertinggi
        if ($b->is_family)              return 0.70; // menengah
        return 0.00;                                  // normal
    }

    /**
     * Bangun rekap prioritas per booking, diringkas per event (template requirements).
     * $eventId null = global; isi angka = khusus event.
     * Hanya ambil booking HARI INI s/d +7 hari (1 minggu terdekat).
     */
    private function buildGreedyRekap(?int $eventId = null): Collection
    {
        $start = today()->toDateString();
        $end   = today()->addDays(7)->toDateString();

        $bookings = Booking::query()
            ->when($eventId, fn($q) => $q->where('event_id', $eventId))
            ->whereBetween('date', [$start, $end])
            // PASTIKAN nama kolom ini sesuai skema kamu.
            // Jika tabelmu pakai `code`, ganti jadi 'code' di array select di bawah.
            // Jika pakai `booking_code`, pakai itu. Di sini saya pakai 'code' + fallback.
            ->get(['id','event_id','booking_code','client_name','date','created_at','priority','is_family']);

        if ($bookings->isEmpty()) return collect();

        $reqs = PerformerRequirement::query()
            ->when($eventId, fn($q) => $q->where('event_id', $eventId))
            ->get(['event_id','performer_role_id','quantity']);

        if ($reqs->isEmpty()) return collect();

        $roleNames = PerformerRole::pluck('name','id');

        $minDate    = $bookings->min('date');       $maxDate    = $bookings->max('date');
        $minCreated = $bookings->min('created_at'); $maxCreated = $bookings->max('created_at');

        // value = total orang; complexity = jumlah peran
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

            // skor komponen
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

            // bobot default
            $priority = (0.40*$deadlineScore) + (0.25*$valueScore) + (0.20*$complexityScore)
                      + (0.10*$customerScore) + (0.05*$timeScore);

            // bentuk list peran & status (untuk bullet di view)
            $pairs = $eventReqs->map(fn($r)=>[
                'role' => $roleNames[$r->performer_role_id] ?? ('Peran #'.$r->performer_role_id),
                'qty'  => (int)$r->quantity,
            ])->sortBy('role')->values();

            $rolesList  = $pairs->map(fn($p)=>"{$p['role']} ({$p['qty']})")->all();
            $statusList = $pairs->map(fn($p)=>$p['qty'].' '.$p['role'])->all();

            $customerLabel = $b->priority === 'darurat' ? 'Darurat' : ($b->is_family ? 'Keluarga' : 'Normal');

            $rows->push((object)[
                // jika kolom kamu bernama booking_code, ganti di sini:
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
