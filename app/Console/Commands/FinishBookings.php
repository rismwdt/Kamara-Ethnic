<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class FinishBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:finish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis mengubah status pesanan menjadi selesai jika tanggalnya sudah lewat';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \Log::info('bookings:finish command dijalankan pada ' . now());

        $updated = Booking::where('status', 'diterima')
            ->where(function($query) {
                $query->whereDate('date', '<', now()->toDateString())
                      ->orWhere(function($q) {
                          $q->whereDate('date', now()->toDateString())
                            ->whereTime('end_time', '<', now()->toTimeString());
                      });
            })
            ->update(['status' => 'selesai']);

        $this->info("{$updated} pesanan berhasil diperbarui menjadi selesai.");
    }
}
