<?php

namespace App\Services;

use App\Models\ListaEspera;
use App\Models\Reserva;
use App\Models\Utilizador;

class NotificationService
{
    public static function notifyListaEspera($lugar_id)
    {
        $usuarios = ListaEspera::where('lugar_id', $lugar_id)
            ->orderBy('prioridade')
            ->get();

        foreach ($usuarios as $usuarioEspera) {
            $user = Utilizador::find($usuarioEspera->utilizador_id);
            if ($user->pontos >= 3) {
                \Log::info("Notificação enviada a {$user->nome} para reserva do lugar $lugar_id");
            }
        }
    }

    public static function notifyUser($userId, $mensagem)
    {
        $user = Utilizador::find($userId);
        \Log::info("Notificação a {$user->nome}: $mensagem");
    }
}


// lista de espera e notificações