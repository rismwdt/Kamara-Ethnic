<?php

namespace App\Http\Controllers\Klien;

use Illuminate\Http\Request;
use App\Services\ScheduleOptimizer;
use App\Http\Controllers\Controller;

class ScheduleController extends Controller
{
    public function __construct(private ScheduleOptimizer $optimizer) {}

    public function checkSchedule(Request $request)
    {
        // VALIDASI INPUT
        $request->validate([
            'event_id'        => 'required|exists:events,id',
            'date'            => 'required|date|after_or_equal:today',
            'start_time'      => 'required|date_format:H:i',   // Optimizer bisa H:i atau H:i:s
            'end_time'        => 'required|date_format:H:i|after:start_time',
            'location_detail' => 'required|string',
            'latitude'        => 'required|numeric',
            'longitude'       => 'required|numeric',
        ]);

        try {
            $result = $this->optimizer->checkScheduleAvailability(
                $request->input('date'),
                $request->input('start_time'),
                $request->input('end_time'),
                (float) $request->input('latitude'),
                (float) $request->input('longitude'),
            );

            // STRUKTUR RESPON UNTUK JS
            return response()->json([
                'available' => (bool) ($result['available'] ?? false),
                'message'   => (string) ($result['message'] ?? 'Tidak diketahui'),
            ]);

        } catch (\Throwable $e) {
            // Saat debug: kirim pesan error biar gampang dilacak di console
            return response()->json([
                'available' => false,
                'message'   => 'Terjadi kesalahan saat memeriksa jadwal.',
                'error'     => app()->hasDebugModeEnabled() ? $e->getMessage() : null,
            ], 500);
        }
    }
}
