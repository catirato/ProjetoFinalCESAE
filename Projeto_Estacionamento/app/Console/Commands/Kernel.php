<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        // Aqui registamos o comando que fizeste
        \App\Console\Commands\CheckReservations::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('check:reservations')->dailyAt('10:31');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}


//Schedule da regra das 10h30