<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SchedulerService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ValidatorController extends Controller
{
    public function __construct(private SchedulerService $schedulerService) {}

    public function cekJadwal(Request $request)
    {
        // Normalisasi jam dari H:i:s → H:i (agar lolos validasi)
        $start = substr((string)$request->input('start_time', ''), 0, 5);
        $end   = substr((string)$request->input('end_time', ''), 0, 5);
        $request->merge(['start_time' => $start, 'end_time' => $end]);

        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'event_id'   => 'required|exists:events,id',
            'date'       => 'required|date_format:Y-m-d',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'location'   => 'nullable|string|max:500',
            'latitude'   => 'nullable|numeric|between:-90,90',
            'longitude'  => 'nullable|numeric|between:-180,180',
            'assign'     => 'nullable|boolean',
        ]);

        // Konversi "" → null agar tidak menimpa data existing
        $loc = ($validated['location']  ?? null);
        $lat = ($validated['latitude']  ?? null);
        $lng = ($validated['longitude'] ?? null);
        $loc = ($loc !== null && trim($loc) === '') ? null : $loc;
        $lat = ($lat === '' ? null : $lat);
        $lng = ($lng === '' ? null : $lng);

        $assign = $request->boolean('assign');

        try {
    $result = $this->schedulerService->checkAvailabilityAndMaybeAssignToExisting(
        (int)$validated['booking_id'],
        (int)$validated['event_id'],
        $validated['date'],
        $validated['start_time'],
        $validated['end_time'],
        $loc,
        $lat !== null ? (float)$lat : null,
        $lng !== null ? (float)$lng : null,
        $assign
    );

    // tambahkan flag ok agar konsisten di frontend
    $result['ok'] = $result['available'] ?? false;

    // pakai status 200 untuk keduanya (sesuai permintaanmu)
    return response()->json($result, ($result['available'] ?? false) ? 200 : 200);

} catch (ValidationException $e) {
    return response()->json([
        'ok'     => false,
        'errors' => $e->errors(),
    ], 422);
} catch (\Throwable $e) {
    return response()->json([
        'ok'      => false,
        'message' => 'Terjadi kesalahan saat memeriksa jadwal.',
        'error'   => config('app.debug') ? $e->getMessage() : null,
    ], 500);
}
    }
}
