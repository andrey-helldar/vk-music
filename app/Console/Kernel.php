<?php

namespace VKMUSIC\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use VKMUSIC\Console\Commands\Clearing;
use VKMUSIC\Console\Commands\DeleteFiles;
use VKMUSIC\Console\Commands\ProcessingInfoVk;
use VKMUSIC\Console\Commands\RequestVk;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        RequestVk::class,
        ProcessingInfoVk::class,
        DeleteFiles::class,
        Clearing::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('vk:request')->everyMinute();
        $schedule->command('vk:processing')->everyMinute();
//        $schedule->command('vk:files-delete')->everyMinute();
//        $schedule->command('vk:clear')->dailyAt('00:00');
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
