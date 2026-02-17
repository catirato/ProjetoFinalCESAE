<?php

namespace App\Http\Controllers;

// use App\Models\Utilizador;
use App\Services\PointsService;
use Illuminate\Http\Request;
use App\Models\MovimentoPontos; // não sei se é este
use Carbon\Carbon;

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
        
        $pontosGastos = abs(MovimentoPontos::where('utilizador_id', $user->id)
            ->where('pontos', '<', 0)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('pontos'));
        
        // Calcular dias até próximo reset
        $diasProximoReset = now()->endOfMonth()->diffInDays(now());
        
        return view('pontos.index', compact(
            'movimentos',
            'pontosGanhos',
            'pontosGastos',
            'diasProximoReset'
        ));

    //     return Utilizador::all()->map(function($user){
    //         return [
    //             'nome' => $user->nome,
    //             'pontos' => $user->pontos,
    //             'id' => $user->id
    //         ];
    //     });
    // }

    // public function ajustar(Request $request, $id)
    // {
    //     $user = Utilizador::find($id);
    //     if (!$user) return response()->json(['error'=>'Utilizador não encontrado'],404);

    //     PointsService::addPoints($user, $request->pontos);
    //     return response()->json(['message'=>'Pontos ajustados']);
    }
}
