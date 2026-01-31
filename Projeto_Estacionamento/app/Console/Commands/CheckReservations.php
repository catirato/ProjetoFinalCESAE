<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reserva;
use App\Services\PointsService;
use App\Services\NotificationService;

class CheckReservations extends Command
{
    protected $signature = 'check:reservations';
    protected $description = 'Verifica reservas não validadas até 10h30';

    public function handle()
    {
        $reservas = Reserva::where('data', today())->where('estado','ATIVA')->get();

        foreach ($reservas as $reserva) {
            if (!$reserva->validada_por) {
                $reserva->estado = 'NAO_COMPARECEU';
                $reserva->save();

                PointsService::penalizeNoShow($reserva->utilizador);

                NotificationService::notifyUser($reserva->utilizador->id, 'Não compareceu à reserva');

                $admin = Utilizador::where('role', 'ADMIN')->first();
$adminId = $admin->id;

                NotificationService::notifyUser($adminId, 'Reserva não compareceu');

                NotificationService::notifyListaEspera($reserva->lugar_id);
            }
        }
    }
}

//Cronjob regra 10h30