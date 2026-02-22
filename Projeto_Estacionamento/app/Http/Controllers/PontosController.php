<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MovimentoPontos;
use App\Models\Utilizador;
use App\Services\PointsService;

class PontosController extends Controller
{
    public function index()
    {
        $user = auth('utilizador')->user();
        
        // Buscar todos os movimentos
        $movimentos = MovimentoPontos::where('utilizador_id', $user->id)
            ->with('reserva')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Estatísticas do mês atual
        $pontosGanhos = MovimentoPontos::where('utilizador_id', $user->id)
            ->where('pontos', '>', 0)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('pontos');
        
        $pontosGastosRaw = MovimentoPontos::where('utilizador_id', $user->id)
            ->where('pontos', '<', 0)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('pontos');
        $pontosGastos = max(0, abs((int) $pontosGastosRaw));
        
        // Calcular dias até próximo reset
        $diasProximoReset = max(0, now()->startOfDay()->diffInDays(now()->copy()->endOfMonth()->startOfDay(), false));
        
        return view('pontos.pontos', compact(
            'movimentos',
            'pontosGanhos',
            'pontosGastos',
            'diasProximoReset'
        ));
    }

    public function adminIndex()
    {
        $admin = auth('utilizador')->user();
        if ($admin->role !== 'ADMIN') {
            abort(403, 'Apenas administradores podem gerir pontos.');
        }

        $utilizadores = Utilizador::query()
            ->where('role', '!=', 'SEGURANCA')
            ->orderBy('nome')
            ->paginate(20);

        return view('pontos.admin', compact('utilizadores'));
    }

    public function adminAdjust(Request $request, $id)
    {
        $admin = auth('utilizador')->user();
        if ($admin->role !== 'ADMIN') {
            abort(403, 'Apenas administradores podem gerir pontos.');
        }

        $validated = $request->validate([
            'ajuste' => ['required', 'integer', 'between:-100,100', 'not_in:0'],
        ]);

        $user = Utilizador::findOrFail($id);
        if ($user->role === 'SEGURANCA') {
            return back()->with('error', 'Utilizadores de segurança não têm gestão de pontos.');
        }

        PointsService::addPoints($user, (int) $validated['ajuste']);

        return back()->with('success', "Pontos de {$user->nome} ajustados com sucesso.");
    }
}
