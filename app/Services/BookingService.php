<?php

namespace App\Services;

use App\Models\Performer;
use Carbon\Carbon;

class BookingService
{
    public static function checkPerformerConflict($performerId, $currentBooking)
    {
        $performer = Performer::find($performerId);

        foreach ($performer->bookings as $booking) {
            if ($booking->id === $currentBooking->id) continue; 

            if ($booking->date === $currentBooking->date &&
                $booking->start_time === $currentBooking->start_time) {
                return $performer;
            }
        }

        return false;
    }
}
