<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * @var string[]
     */
    protected $commands = [
        'App\Console\Commands\SendEmail',
        'App\Console\Commands\SeedConfig',
        'App\Console\Commands\CreateUser',
        'App\Console\Commands\SendByCampaign',
        'App\Console\Commands\SendByBirthdayCampaign',
        'App\Console\Commands\SendNotOpenByScenarioCampaign',
        'App\Console\Commands\UpdateStatusOnContact',
        'App\Console\Commands\UpdateStatisticsOfUserPayment',
        'App\Console\Commands\AutoDeleteDraftArticleOfEditor',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule->command('Send:Email')->everyMinute();
//        $schedule->command('send:campaign')->dailyAt('8:00');
        $schedule->command('send:birthday-campaign')->dailyAt('8:00');
        $schedule->command('send:not-open-campaign')->everyFiveMinutes();
        $schedule->command('check:platform-expired')->dailyAt('5:00');
        $schedule->command('update:recervers')->everyFiveMinutes();
        $schedule->command('update:statistics-user-payment')->dailyAt('23:00');
        $schedule->command('delete:draft-articles')->everyFiveMinutes();
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
