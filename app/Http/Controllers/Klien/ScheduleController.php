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
    public function checkSchedule(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'date' => 'required|date|after_or_equal:' . now()->addDays(3)->format('Y-m-d'),
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location_detail' => 'required|string',
        ]);

        $startTime = Carbon::createFromFormat('H:i', $request->start_time)->format('H:i:s');
        $endTime = Carbon::createFromFormat('H:i', $request->end_time)->format('H:i:s');
        $date = Carbon::parse($request->date);

        \Log::info("Cek jadwal | Date: {$request->date}, Start: $startTime, End: $endTime");

        $optimizer = new ScheduleOptimizer();

        if (!$optimizer->canAcceptInDay($date)) {
            return response()->json([
                'available' => false,
                'message' => 'Kuota acara pada hari tersebut sudah penuh (maksimal 5 acara).'
            ]);
        }

        $isAvailable = $optimizer->isAvailable(
            $date,
            $startTime,
            $endTime,
            $request->location_detail
        );

        $event = Event::find($request->event_id);

        return response()->json([
            'available' => $isAvailable,
            'message'   => $isAvailable ? 'Tersedia' : 'Jadwal sudah penuh atau bentrok.',
            'price'     => $event?->price,
            'dp'        => $event ? ceil($event->price * 0.5) : null,
        ]);
    }
}
