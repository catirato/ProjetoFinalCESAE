<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Utilizador;
use App\Models\Lugar;
use App\Services\PointsService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    // Criar reserva
    public function store(Request $request)
    {
        $user = Utilizador::find($request->utilizador_id);
        if (!$user) return response()->json(['error'=>'Utilizador não encontrado'], 404);

        if ($user->pontos < 3) {
            return response()->json(['error'=>'Não tem pontos suficientes'], 400);
        }

        $exists = Reserva::where('lugar_id', $request->lugar_id)
            ->where('data', $request->data)
            ->where('estado','ATIVA')
            ->exists();

        if ($exists) {
            return response()->json(['error'=>'Lugar já reservado'], 400);
        }

        $reserva = Reserva::create([
            'utilizador_id' => $user->id,
            'lugar_id' => $request->lugar_id,
            'data' => $request->data,
            'estado' => 'ATIVA'
        ]);

        // Deduz pontos da reserva
        PointsService::deductReserva($user);

        return response()->json($reserva);
    }

    // Cancelar reserva
    public function cancelar($id)
    {
        $reserva = Reserva::find($id);
        if (!$reserva) return response()->json(['error'=>'Reserva não encontrada'], 404);

        $reserva->estado = 'CANCELADA';
        $reserva->save();

        // Penalização por cancelamento
        PointsService::penalizeCancelamento($reserva->utilizador);

        // Notificar lista de espera
        NotificationService::notifyListaEspera($reserva->lugar_id);

        return response()->json(['message'=>'Reserva cancelada']);
    }

    // Listar todas as reservas de um dia
    public function index(Request $request)
    {
        $data = $request->data ?? date('Y-m-d');
        $reservas = Reserva::where('data', $data)->get();
        return response()->json($reservas);
    }

    // Detalhes de uma reserva
    public function show($id)
    {
        $reserva = Reserva::find($id);
        if (!$reserva) return response()->json(['error'=>'Reserva não encontrada'], 404);

        return response()->json($reserva);
    }

    // Verificar disponibilidade de lugares
    public function disponibilidade(Request $request)
    {
        $data = $request->data ?? date('Y-m-d');

        // Lugares ocupados no dia
        $lugaresReservados = Reserva::where('data', $data)
            ->where('estado','ATIVA')
            ->pluck('lugar_id')
            ->toArray();

        // Lugares livres
        $lugaresLivres = Lugar::whereNotIn('id', $lugaresReservados)->get();

        return response()->json($lugaresLivres);
    }
}
