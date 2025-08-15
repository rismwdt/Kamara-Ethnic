<?php

namespace App\Observers;

use App\Models\Booking;
use App\Services\GreedyScheduler;

class BookingObserver
{
    protected $scheduler;

    public function __construct(GreedyScheduler $scheduler)
    {
        $this->scheduler = $scheduler;
    }

    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking)
    {
        // Jalankan greedy scheduling otomatis
        $this->scheduler->schedulePendingBookings();
    }
}
