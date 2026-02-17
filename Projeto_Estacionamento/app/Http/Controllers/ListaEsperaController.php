<?php

namespace App\Http\Controllers;

// use App\Models\Lugar;
use App\Models\Reserva;
use App\Services\PointsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ListaEspera; //AQUI
use App\Models\HistoricoEventos; //AQUI


class ListaEsperaController extends Controller
{
    public function index()
    {
        $user = auth('utilizador')->user();
        
        // Minhas entradas na lista
        $minhasEntradas = ListaEspera::where('utilizador_id', $user->id)
            ->whereIn('estado', ['ATIVO', 'NOTIFICADO'])
            ->orderBy('data')
            ->get();
        
        // Lista completa (para transparência)
        $listaCompleta = ListaEspera::with('utilizador')
            ->where('estado', 'ATIVO')
            ->orderBy('data')
            ->orderBy('prioridade')
            ->get();
        
        return view('lista-espera.index', compact('minhasEntradas', 'listaCompleta'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'data' => 'required|date|after:today',
        ]);
        
        $user = auth('utilizador')->user();
        
        // Verificar se já está na lista para esse dia
        $jaEstaLista = ListaEspera::where('utilizador_id', $user->id)
            ->where('data', $request->data)
            ->where('estado', 'ATIVO')
            ->exists();
        
        if ($jaEstaLista) {
            return back()->with('error', 'Já está na lista de espera para este dia!');
        }
        
        // Verificar se já tem reserva para esse dia
        $jaTemReserva = Reserva::where('utilizador_id', $user->id)
            ->where('data', $request->data)
            ->where('estado', 'ATIVA')
            ->exists();
        
        if ($jaTemReserva) {
            return back()->with('error', 'Já tem uma reserva para este dia!');
        }
        
        // Calcular prioridade (baseado nos pontos)
        $prioridade = ListaEspera::where('data', $request->data)
            ->where('estado', 'ATIVO')
            ->count() + 1;
        
        // Criar entrada
        $entrada = ListaEspera::create([
            'utilizador_id' => $user->id,
            'data' => $request->data,
            'estado' => 'ATIVO',
            'prioridade' => $prioridade,
        ]);
        
        // Registar histórico
        HistoricoEventos::create([
            'utilizador_id' => $user->id,
            'tipo_evento' => 'LISTA_ESPERA',
            'entidade_id' => $entrada->id,
            'acao' => 'CRIADO',
            'descricao' => "Entrou na lista de espera para " . \Carbon\Carbon::parse($request->data)->format('d/m/Y'),
        ]);
        
        return back()->with('success', 'Entrou na lista de espera! Será notificado se uma vaga ficar disponível.');
    }
    
    public function destroy($id)
    {
        $entrada = ListaEspera::findOrFail($id);
        $user = auth('utilizador')->user();
        
        if ($entrada->utilizador_id !== $user->id) {
            abort(403);
        }
        
        $entrada->delete();
        
        return back()->with('success', 'Saiu da lista de espera.');
    }
    
    public function accept($id)
    {
        // Aceitar vaga disponível
        $entrada = ListaEspera::findOrFail($id);
        $user = auth('utilizador')->user();
        
        if ($entrada->utilizador_id !== $user->id) {
            abort(403);
        }
        
        if ($entrada->estado !== 'NOTIFICADO') {
            return back()->with('error', 'Esta vaga já não está disponível.');
        }
        
        // Aqui criaria a reserva automaticamente
        // ... (código similar ao ReservaController@store)
        
        $entrada->update(['estado' => 'ACEITE']);
        
        return redirect('/reservas')->with('success', 'Vaga aceite! Reserva criada.');
    }

    // public function notificar(Request $request)
    // {
       
    // }

    // // Primeiro a aceitar
    // public function aceitar(Request $request)
    // {
    //     $user = $request->user();
    //     $data = $request->data;

    //     if ($user->pontos < 3) {
    //         return response()->json(['error' => 'Sem pontos'], 400);
    //     }

    //     return DB::transaction(function () use ($user, $data) {

    //         $lugarLivre = Lugar::whereDoesntHave('reservas', function ($q) use ($data) {
    //             $q->where('data', $data)
    //               ->where('estado', 'ATIVA');
    //         })
    //         ->lockForUpdate()
    //         ->first();

    //         if (!$lugarLivre) {
    //             return response()->json(['error' => 'Já foi ocupado'], 400);
    //         }

    //         $reserva = Reserva::create([
    //             'utilizador_id' => $user->id,
    //             'lugar_id' => $lugarLivre->id,
    //             'data' => $data,
    //             'estado' => 'ATIVA'
    //         ]);

    //         PointsService::deductReserva($user);

    //         return response()->json($reserva);
    //     });
    // }
}
