<?php

namespace App\Http\Controllers;

use App\Models\ListaEspera;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ListaEsperaController extends Controller
{
    public function index()
    {
        return ListaEspera::all();
    }

    public function notificar(Request $request)
    {
        NotificationService::notifyListaEspera($request->lugar_id);
        return response()->json(['message'=>'Notificações enviadas']);
    }
}
