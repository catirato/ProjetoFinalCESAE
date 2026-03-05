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

    protected function schedule(Schedule $schedule): void
    {
        // Verificação diária de faltas de comparência
        $schedule->command('check:reservations')->dailyAt('13:01');

        // Reset mensal de pontos (último dia do mês)
        $schedule->command('points:reset')->lastDayOfMonth('23:59');
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
