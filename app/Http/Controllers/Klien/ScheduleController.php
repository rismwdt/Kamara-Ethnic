<?php

namespace App\Http\Controllers\Klien;

use Carbon\Carbon;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Services\ScheduleOptimizer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    protected $optimizer;

    public function __construct(ScheduleOptimizer $optimizer)
    {
        $this->optimizer = $optimizer;
    }

    public function checkSchedule(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location_detail' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            // 'priority' => 'required|integer',
        ]);

        $date = $request->date;
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        $locationDetail = $request->location_detail;
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        // $priority = $request->priority;

        $available = $this->optimizer->checkScheduleAvailability(
            $date,
            $startTime,
            $endTime,
            $latitude,
            $longitude,
            // $priority
        );

        return response()->json([
            'available' => $available['available'],
            'message' => $available['message'],
        ]);

        $eventDate = Carbon::parse($request->date);
        $daysDiff = now()->diffInDays($eventDate, false);

        if ($daysDiff < 3) {
            $priority = 'darurat';
            // Munculkan pesan ke user (hanya di frontend):
            // "Pemesanan sebaiknya dilakukan minimal H-3 sebelum acara. Jika kondisi Anda darurat dan tidak memiliki opsi lain, apakah tetap ingin melanjutkan?"
        } else {
            $priority = 'normal';
        }

        $booking = Booking::create([
            // field lain...
            'priority' => $priority
        ]);

    }
}
