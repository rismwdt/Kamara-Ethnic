<?php

namespace App\Http\Controllers\Admin;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil semua bookings dengan relasi ke event
        $bookings = Booking::with('event')->get();

        // Total semua pendapatan dari event terkait
        $totalPendapatan = $bookings->sum(function ($booking) {
            return $booking->event->price ?? 0;
        });

        // Pendapatan bulan ini
        $pendapatanBulanIni = $bookings->whereBetween('date', [
            now()->startOfMonth(), now()->endOfMonth()
        ])->sum(function ($booking) {
            return $booking->event->price ?? 0;
        });

        // Jumlah total jadwal
        $totalJadwal = $bookings->count();

        // Jumlah jadwal bulan ini
        $jadwalBulanIni = $bookings->whereBetween('date', [
            now()->startOfMonth(), now()->endOfMonth()
        ])->count();

        // Jumlah klien unik berdasarkan email
        $jumlahKlien = $bookings->unique('email')->count();

        // Email unik bulan ini
        $jumlahKlienBulanIni = Booking::whereMonth('date', now()->month)
            ->distinct('email')
            ->count('email');

        // Tanggal minggu ini (Senin - Minggu)
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek()->toDateString();
        $today = now()->toDateString();  

        $jadwalMingguIni = $bookings
            ->whereBetween('date', [$today, $endOfWeek])
            ->sortBy(function ($booking) {
                return $booking->date . ' ' . $booking->start_time;
            })
            ->take(8)
            ->values();

        // Tanggal di bulan ini yang punya acara
        $tanggalDenganAcara = Booking::whereMonth('date', now()->month)
            ->selectRaw('date, COUNT(*) as jumlah')
            ->groupBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [
                    \Carbon\Carbon::parse($item->date)->format('Y-m-d') => $item->jumlah,
                ];
            });

        return view('dashboard', compact(
            'totalPendapatan',
            'pendapatanBulanIni',
            'totalJadwal',
            'jadwalBulanIni',
            'jumlahKlien',
            'jumlahKlienBulanIni',
            'jadwalMingguIni',
            'tanggalDenganAcara'
        ));
    }
}
