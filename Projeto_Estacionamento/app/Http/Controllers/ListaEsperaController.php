<?php

namespace App\Http\Controllers;

use App\Models\Lugar;
use App\Models\Reserva;
use App\Services\PointsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListaEsperaController extends Controller
{
    public function index()
    {
        
    }

    public function notificar(Request $request)
    {
       
    }

    // Primeiro a aceitar
    public function aceitar(Request $request)
    {
        $user = $request->user();
        $data = $request->data;

        if ($user->pontos < 3) {
            return response()->json(['error' => 'Sem pontos'], 400);
        }

        return DB::transaction(function () use ($user, $data) {

            $lugarLivre = Lugar::whereDoesntHave('reservas', function ($q) use ($data) {
                $q->where('data', $data)
                  ->where('estado', 'ATIVA');
            })
            ->lockForUpdate()
            ->first();

            if (!$lugarLivre) {
                return response()->json(['error' => 'Já foi ocupado'], 400);
            }

            $reserva = Reserva::create([
                'utilizador_id' => $user->id,
                'lugar_id' => $lugarLivre->id,
                'data' => $data,
                'estado' => 'ATIVA'
            ]);

            PointsService::deductReserva($user);

            return response()->json($reserva);
        });
    }
}
