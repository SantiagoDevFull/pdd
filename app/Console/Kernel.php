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
        \App\Console\Commands\CreditHistorySave::class,
        \App\Console\Commands\EnviarNotificacionesBlDriver::class,
        \App\Console\Commands\EnviarNotificacionesBlVehiculos::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('credit_history:save')->dailyAt('23:50');
        $schedule->command('notificaciones:choferes')->dailyAt('7:00');
        $schedule->command('notificaciones:vehiculos')->dailyAt('7:30');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
