<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Utilizador;
use App\Services\PointsService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    public function store(Request $request)
    {
        $user = Utilizador::find($request->utilizador_id);
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

        PointsService::deductReserva($user);

        return response()->json($reserva);
    }

    public function cancelar($id)
    {
        $reserva = Reserva::find($id);
        if (!$reserva) return response()->json(['error'=>'Reserva não encontrada'], 404);

        $reserva->estado = 'CANCELADA';
        $reserva->save();

        PointsService::penalizeCancelamento($reserva->utilizador);
        NotificationService::notifyListaEspera($reserva->lugar_id);

        return response()->json(['message'=>'Reserva cancelada']);
    }

    public function index(Request $request)
    {
        $data = $request->data ?? date('Y-m-d');
        $reservas = Reserva::where('data', $data)->get();
        return response()->json($reservas);
    }
}
