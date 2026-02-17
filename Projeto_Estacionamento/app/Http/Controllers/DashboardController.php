<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Lugar;
use App\Models\HistoricoEventos;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // return view('dashboard.index');
        $user = auth('utilizador')->user();
        
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
        $vagasDisponiveis = Lugar::where('ativo', true)
            ->whereDoesntHave('reservas', function($q) {
                $q->where('data', today())
                  ->where('estado', 'ATIVA');
            })
            ->count();
        
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
