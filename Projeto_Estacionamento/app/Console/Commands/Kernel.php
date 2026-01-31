<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\CheckReservations::class,
        \App\Console\Commands\ResetMonthlyPoints::class, 
    ];

    protected function schedule(Schedule $schedule)
    {
        // Regra das 10h30
        $schedule->command('check:reservations')->dailyAt('10:31');

        // Reset mensal de pontos (último dia do mês)
        $schedule->command('points:reset')->monthlyOn(28, '23:59');
        //  28 funciona SEMPRE (Laravel ajusta para último dia)
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
