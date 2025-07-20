<?php

namespace App\Http\Controllers\Klien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ScheduleOptimizer;

class ScheduleController extends Controller
{
    public function checkSchedule(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'date' => 'required|date|after_or_equal:' . now()->addDays(3)->format('Y-m-d'), // ✅ H-2
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'required|string|max:255',
        ]);

        $optimizer = new ScheduleOptimizer();

        if (!$optimizer->canAcceptInWeek($request->date)) {
            return response()->json([
                'available' => false,
                'message' => 'Kuota acara dalam minggu tersebut sudah penuh (maksimal 5).'
            ]);
        }

        $isAvailable = $optimizer->isAvailable(
            $request->date,
            $request->start_time,
            $request->end_time,
            $request->location
        );

        return response()->json([
            'available' => $isAvailable,
            'message' => $isAvailable ? 'Tersedia' : 'Jadwal sudah penuh atau bentrok.'
        ]);
    }
}
