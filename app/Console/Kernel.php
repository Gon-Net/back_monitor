<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')->everySecond();
        $schedule->command('app:migrate-mems')->daily()->at("12:30");
    }
    /*
    protected $commands = [
        \App\Console\Commands\MigrateMems::class,
    ];
    */
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'\Commands\MigrateMems::class');
        
        require base_path('routes/web.php');
    }
}