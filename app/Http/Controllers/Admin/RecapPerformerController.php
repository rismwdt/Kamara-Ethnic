<?php

namespace App\Http\Controllers\Admin;

use App\Models\Booking;
use App\Models\Performer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PerformerRequirement;
use Carbon\Carbon;

class RecapPerformerController extends Controller
{
    public function index()
    {
        // Ambil semua performer
        $performers = Performer::all();

        // Total performer
        $totalPerformer = $performers->count();

        // Misal prioritas utama adalah performer dengan priority_score tertinggi
        $prioritasUtama = $performers->max('priority_score');

        // Kapasitas (contoh: sum durasi penampilan hari ini vs total kapasitas)
        $kapasitasHariIni = $performers->where('date', now()->toDateString())->sum('duration');
        $totalKapasitas = $performers->sum('duration');

        // Rata-rata durasi
        $avgDurasi = $performers->avg('duration');

        // Hitung skor greedy untuk setiap performer
        $rekap = $performers->map(function ($p) {
            $deadlineScore = $p->deadline_score * 0.4;
            $valueScore = $p->value_score * 0.25;
            $complexityScore = $p->complexity_score * 0.2;
            $customerScore = $p->customer_score * 0.1;
            $timeScore = $p->time_score * 0.05;

            $p->priority_score = $deadlineScore + $valueScore + $complexityScore + $customerScore + $timeScore;

            // Tentukan status (misal: jika sudah dijadwalkan atau belum)
            $p->status = $p->priority_score >= 50 ? 'Prioritas' : 'Pending';

            return $p;
        })->sortByDesc('priority_score'); // Urut dari yang paling tinggi

        return view('admin.rekap-pengisi-acara', compact(
            'totalPerformer',
            'prioritasUtama',
            'kapasitasHariIni',
            'totalKapasitas',
            'avgDurasi',
            'rekap'
        ));
    }
}
