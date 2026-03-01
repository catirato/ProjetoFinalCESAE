<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reserva;
use App\Models\Utilizador;
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

                PointsService::penalizeNoShow($reserva->utilizador, $reserva->id);

                NotificationService::notifyUser($reserva->utilizador->id, 'Não compareceu à reserva');

                $admin = Utilizador::where('role', 'ADMIN')->first();
                if ($admin) {
                    NotificationService::notifyUser($admin->id, 'Reserva não compareceu');
                }

                NotificationService::notifyListaEspera($reserva->data, $reserva->lugar_id);
            }
        }
    }
}

//Cronjob regra 10h30
