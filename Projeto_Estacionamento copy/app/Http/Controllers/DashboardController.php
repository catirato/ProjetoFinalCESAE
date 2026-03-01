<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Lugar;
use App\Models\HistoricoEventos;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    private const LUGARES_FIXOS = [1, 2, 5];

    public function index()
    {
        // return view('dashboard.index');
        $user = auth('utilizador')->user();

        if ($user->role === 'SEGURANCA') {
            return redirect()->route('seguranca.reservas.hoje');
        }
        
        // Contar reservas ativas
        $reservasAtivas = Reserva::where('utilizador_id', $user->id)
            ->where('estado', 'ATIVA')
            ->where('data', '>=', today())
            ->count();
        
        // Contar entradas na lista de espera
        $listaEsperaAtiva = $user->listaEspera()
            ->where('estado', 'ATIVO')
            ->count();
        
        // Calcular vagas disponíveis hoje
        if (Carbon::today()->isWeekend()) {
            $vagasDisponiveis = 0;
        } else {
            $vagasDisponiveis = Lugar::where('ativo', true)
                ->whereNotIn('numero', self::LUGARES_FIXOS)
                ->whereDoesntHave('reservas', function($q) {
                    $q->where('data', today())
                        ->whereIn('estado', ['ATIVA', 'PRESENTE']);
                })
                ->count();
        }
        
        // Buscar próximas reservas
        $proximasReservas = Reserva::where('utilizador_id', $user->id)
            ->where('estado', 'ATIVA')
            ->where('data', '>=', today())
            ->with('lugar')
            ->orderBy('data')
            ->limit(5)
            ->get();
        
        // Histórico recente
        $historico = HistoricoEventos::where('utilizador_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('dashboard.index', compact(
            'reservasAtivas',
            'listaEsperaAtiva',
            'vagasDisponiveis',
            'proximasReservas',
            'historico'
        ));
    }
}
