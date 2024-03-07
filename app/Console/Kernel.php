<?php

namespace App\Console;

use App\Console\Commands\OverdueNotice;
use App\Console\Commands\UpdateDept;
use App\Console\Commands\UpdateUser;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        UpdateDept::class,
        UpdateUser::class,
        OverdueNotice::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
