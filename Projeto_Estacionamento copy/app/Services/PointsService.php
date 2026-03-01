<?php

namespace App\Services;

use App\Models\MovimentoPontos;
use App\Models\Utilizador;

class PointsService
{
    public static function deductReserva(Utilizador $user)
    {
        $user->pontos -= 3;
        $user->save();

        MovimentoPontos::create([
            'utilizador_id' => $user->id,
            'tipo' => 'RESERVA',
            'pontos' => -3
        ]);
    }

    public static function penalizeCancelamento(Utilizador $user)
    {
        $user->pontos -= 2;
        $user->save();

        MovimentoPontos::create([
            'utilizador_id' => $user->id,
            'tipo' => 'CANCELAMENTO',
            'pontos' => -2
        ]);
    }

    public static function penalizeNoShow(Utilizador $user, ?int $reservaId = null)
    {
        // Devolve os 3 pontos da reserva e aplica penalização de falta (-10).
        // Efeito líquido total da ocorrência: -10 pontos (não -13).
        $user->pontos += 3;
        $user->pontos -= 10;
        $user->save();

        MovimentoPontos::create([
            'utilizador_id' => $user->id,
            'reserva_id' => $reservaId,
            'tipo' => 'AJUSTE',
            'pontos' => 3
        ]);

        MovimentoPontos::create([
            'utilizador_id' => $user->id,
            'reserva_id' => $reservaId,
            'tipo' => 'FALTA',
            'pontos' => -10
        ]);
    }

    public static function addPoints(Utilizador $user, int $pontos)
    {
        $user->pontos += $pontos;
        $user->save();

        MovimentoPontos::create([
            'utilizador_id' => $user->id,
            'tipo' => 'AJUSTE',
            'pontos' => $pontos
        ]);
    }

    public static function resetMonthlyPoints()
    {
        $users = Utilizador::all();
        foreach ($users as $user) {
            $user->pontos = 30;
            $user->save();

            MovimentoPontos::create([
                'utilizador_id' => $user->id,
                'tipo' => 'RESET_MENSAL',
                'pontos' => 30
            ]);
        }
    }
}


//gerenciar pontos e penalizações
