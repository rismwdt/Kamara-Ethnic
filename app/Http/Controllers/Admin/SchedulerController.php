<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SchedulerController extends Controller
{
    protected $scheduler;

    public function __construct(ScheduleValidator $scheduler)
    {
        $this->scheduler = $scheduler;
    }

    public function run()
    {
        $this->scheduler->schedulePendingBookings();
        return redirect()->back()->with('success', 'Greedy scheduling berhasil dijalankan!');
    }

    public function index()
    {
        $bookings = Booking::with('performers')->get();
        return view('admin.scheduler', compact('bookings'));
    }
}
