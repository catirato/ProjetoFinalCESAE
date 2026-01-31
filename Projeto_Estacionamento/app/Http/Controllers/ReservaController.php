<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Utilizador;
use App\Models\Lugar;
use App\Services\PointsService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Carbon\Carbon;


class ReservaController extends Controller
{
    // Criar reserva
    public function store(Request $request)
{
    $request->validate([
        'lugar_id' => 'required|exists:lugar,id',
        'data' => 'required|date'
    ]);

    $user = $request->user();
    $dataReserva = Carbon::parse($request->data);
    $hoje = Carbon::today();

    // data passada
    if ($dataReserva->lt($hoje)) {
        return response()->json(['error' => 'Não podes reservar datas passadas'], 400);
    }

    // hoje depois das 10h
    if ($dataReserva->isToday() && now()->format('H:i') > '10:00') {
        return response()->json(['error' => 'Reservas para hoje só até às 10h'], 400);
    }

    // fora da semana atual + próxima
    $fimSemanaSeguinte = Carbon::now()->addWeek()->endOfWeek();
    if ($dataReserva->gt($fimSemanaSeguinte)) {
        return response()->json(['error' => 'Só podes reservar até à próxima semana'], 400);
    }

    // sem pontos
    if ($user->pontos < 3) {
        return response()->json(['error' => 'Não tens pontos suficientes'], 400);
    }

    // lugar ocupado
    $ocupado = Reserva::where('lugar_id', $request->lugar_id)
        ->where('data', $dataReserva->toDateString())
        ->where('estado', 'ATIVA')
        ->exists();

    if ($ocupado) {
        return response()->json(['error' => 'Lugar já reservado'], 400);
    }

    $reserva = Reserva::create([
        'utilizador_id' => $user->id,
        'lugar_id' => $request->lugar_id,
        'data' => $dataReserva,
        'estado' => 'ATIVA'
    ]);

    PointsService::deductReserva($user);

    return response()->json($reserva, 201);
}
}