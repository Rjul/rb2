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
        // Newsletter hebdomadaire : chaque vendredi à 8h (heure de Paris).
        // Nécessite le cron système : * * * * * php /chemin/rb2/artisan schedule:run
        $schedule->command('newsletter:send-weekly')
            ->fridays()
            ->at('08:00')
            ->timezone('Europe/Paris')
            ->onOneServer()
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
