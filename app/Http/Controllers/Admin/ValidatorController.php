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
        // Normalisasi jam dari H:i:s → H:i
        $start = substr((string)$request->input('start_time', ''), 0, 5);
        $end   = substr((string)$request->input('end_time', ''), 0, 5);
        $request->merge(['start_time' => $start, 'end_time' => $end]);

        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'event_id'   => 'required|exists:events,id',
            'date'       => 'required|date_format:Y-m-d',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'location'   => 'nullable|string|max:500',   // frontend kirim "location"
            'latitude'   => 'nullable|numeric|between:-90,90',
            'longitude'  => 'nullable|numeric|between:-180,180',
            'assign'     => 'nullable|boolean',
        ]);

        // Jadikan "" → null agar tidak menimpa kolom existing
        $loc = $validated['location']  ?? null;
        $lat = $validated['latitude']  ?? null;
        $lng = $validated['longitude'] ?? null;

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

            return response()->json($result);
        } catch (ValidationException $e) {
            // Mis. dari ensureValidWindow()
            return response()->json([
                'ok'     => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            // Log kalau perlu: \Log::error($e);
            return response()->json([
                'ok'      => false,
                'message' => 'Terjadi kesalahan saat memeriksa jadwal.',
            ], 500);
        }
    }
}
