<?php

namespace App\Http\Controllers;

use App\Models\Utilizador;
use App\Services\PointsService;
use Illuminate\Http\Request;

class PontosController extends Controller
{
    public function index()
    {
        return Utilizador::all()->map(function($user){
            return [
                'nome' => $user->nome,
                'pontos' => $user->pontos,
                'id' => $user->id
            ];
        });
    }

    public function ajustar(Request $request, $id)
    {
        $user = Utilizador::find($id);
        if (!$user) return response()->json(['error'=>'Utilizador não encontrado'],404);

        PointsService::addPoints($user, $request->pontos);
        return response()->json(['message'=>'Pontos ajustados']);
    }
}
