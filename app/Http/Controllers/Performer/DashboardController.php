<?php

namespace App\Http\Controllers\Performer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        Carbon::setLocale(config('app.locale', 'id'));

        $user = auth()->user();
        $performerId = optional($user->performer)->id;

        if (!$performerId) {
            return redirect()->route('welcome')
                ->with('error', 'Akun belum ditautkan ke profil performer.');
        }

        $candidates = ['booking_performer', 'booking_performers', 'booking_permormers'];
        $pivot = collect($candidates)->first(fn($t) => Schema::hasTable($t));
        if (!$pivot) {
            abort(500, 'Tabel pivot booking_performer(s) tidak ditemukan.');
        }

        $now = Carbon::now();

        $startOfWeek = $now->copy()->startOfWeek(Carbon::MONDAY)->toDateString();
        $endOfWeek   = $now->copy()->endOfWeek(Carbon::SUNDAY)->toDateString();

        $base = Booking::query()
            ->select('bookings.*')
            ->join("$pivot as bp", 'bp.booking_id', '=', 'bookings.id')
            ->where('bp.performer_id', $performerId);

        $totalAcara = (clone $base)->distinct('bookings.id')->count('bookings.id');

        $totalAcaraMingguIni = (clone $base)
            ->whereBetween('bookings.date', [$startOfWeek, $endOfWeek])
            ->distinct('bookings.id')->count('bookings.id');

        $jadwalMingguIni = (clone $base)
            ->with('event')
            ->whereBetween('bookings.date', [$startOfWeek, $endOfWeek])
            ->orderBy('bookings.date')->orderBy('bookings.start_time')
            ->get();

        $rangeStart = $now->copy()->startOfMonth()->subMonths(6)->toDateString();
        $rangeEnd   = $now->copy()->endOfMonth()->addMonths(6)->toDateString();

        $rows = DB::table('bookings')
            ->join("$pivot as bp", 'bp.booking_id', '=', 'bookings.id')
            ->where('bp.performer_id', $performerId)
            ->whereBetween('bookings.date', [$rangeStart, $rangeEnd])
            ->groupBy('bookings.date')
            ->selectRaw('bookings.date as d, COUNT(DISTINCT bookings.id) as total')
            ->pluck('total', 'd')
            ->toArray();

        $countsByMonth = [];
        foreach ($rows as $date => $count) {
            $ym = Carbon::parse($date)->format('Y-m');
            $countsByMonth[$ym][$date] = (int) $count;
        }

        $weekLabelStart = $now->copy()->startOfWeek(Carbon::MONDAY)->translatedFormat('d M');
        $weekLabelEnd   = $now->copy()->endOfWeek(Carbon::SUNDAY)->translatedFormat('d M Y');

        return view('performer.dashboard', [
            'totalAcara'           => $totalAcara,
            'totalAcaraMingguIni'  => $totalAcaraMingguIni,
            'jadwalMingguIni'      => $jadwalMingguIni,
            'weekLabelStart'       => $weekLabelStart,
            'weekLabelEnd'         => $weekLabelEnd,
            'countsByMonth'        => $countsByMonth,
        ]);
    }
}
