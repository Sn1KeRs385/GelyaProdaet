<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('telescope:prune --hours=168')->daily();
//        $schedule->command('ozon:exports-check')->everyMinute();
//        $schedule->command('ozon:products-create')->everyMinute();
//        $schedule->command('ozon:export')->hourly()->runInBackground();
//        $schedule->command('ozon:update-stocks')->dailyAt('00:00')->runInBackground();
//        $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
