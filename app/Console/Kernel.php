<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\CheckPendingPayments::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Check pending payments every 10 seconds
        $schedule->command('payments:check')
            ->everyTenSeconds()
            ->withoutOverlapping()
            ->runInBackground();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
