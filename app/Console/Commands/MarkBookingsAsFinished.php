<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MarkBookingsAsFinished extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:mark-bookings-as-finished';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $updated = \App\Models\Booking::where('status', 'diterima')
            ->whereDate('date', '<', now())
            ->update(['status' => 'selesai']);

        $this->info("Berhasil menandai {$updated} pesanan sebagai 'selesai'.");
    }
}
