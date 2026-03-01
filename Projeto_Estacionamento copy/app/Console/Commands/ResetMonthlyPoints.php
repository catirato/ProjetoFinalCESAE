<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PointsService;

class ResetMonthlyPoints extends Command
{
    protected $signature = 'points:reset';
    protected $description = 'Reset mensal de pontos';

    public function handle()
    {
        PointsService::resetMonthlyPoints();

        $this->info('Reset mensal de pontos concluído');
    }
}