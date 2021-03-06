<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\ScrapeLoLEsports::class,
        Commands\ScrapeDDragon::class,
        Commands\LeaderboardFlush::class,
        Commands\LeaderboardsSetup::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call('App\Http\Controllers\Schedule\PollingController@poll')->everyMinute();
        $schedule->call('App\Http\Controllers\Schedule\GradingController@resetWeekly')->weekly();
        $schedule->call('App\Http\Controllers\Schedule\GradingController@resetMonthly')->monthly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
