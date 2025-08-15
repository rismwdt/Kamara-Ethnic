<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scheduler:run';

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
        $scheduler = new \App\Services\SchedulerService(new \App\Services\ScheduleValidator());
        $scheduler->assignPerformers();
        $this->info('Scheduler selesai dijalankan.');
    }
}
