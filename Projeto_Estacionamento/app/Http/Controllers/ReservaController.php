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
    public function index()
    {
        $user = auth('utilizador')->user();
        
        // Reservas ativas (futuras ou hoje)
        $reservasAtivas = Reserva::where('utilizador_id', $user->id)
            ->whereIn('estado', ['ATIVA', 'PRESENTE'])
            ->where('data', '>=', today())
            ->with('lugar')
            ->orderBy('data')
            ->get();
        
        // Histórico (passadas ou canceladas)
        $reservasHistorico = Reserva::where('utilizador_id', $user->id)
            ->where(function($q) {
                $q->whereIn('estado', ['CANCELADA', 'NAO_COMPARECEU'])
                  ->orWhere('data', '<', today());
            })
            ->with('lugar')
            ->orderBy('data', 'desc')
            ->paginate(10);
        
        return view('reservas.index', compact('reservasAtivas', 'reservasHistorico'));
    }
    
    public function create()
    {
        return view('reservas.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'data' => 'required|date|after:today|before:' . now()->addDays(31)->format('Y-m-d'),
            'lugar_id' => 'required|exists:lugar,id',
        ]);
        
        $user = auth('utilizador')->user();
        
        // Verificar pontos
        if ($user->pontos < 5) {
            return back()->with('error', 'Não tem pontos suficientes! Precisa de 5 pontos.');
        }
        
        // Verificar se já tem reserva nesse dia
        $jaTemReserva = Reserva::where('utilizador_id', $user->id)
            ->where('data', $request->data)
            ->whereIn('estado', ['ATIVA', 'PRESENTE'])
            ->exists();
        
        if ($jaTemReserva) {
            return back()->with('error', 'Já tem uma reserva para este dia!');
        }
        
        // Verificar se o lugar está disponível
        $lugarOcupado = Reserva::where('lugar_id', $request->lugar_id)
            ->where('data', $request->data)
            ->where('estado', 'ATIVA')
            ->exists();
        
        if ($lugarOcupado) {
            return back()->with('error', 'Este lugar já está reservado para este dia!');
        }
        
        // Criar reserva
        $reserva = Reserva::create([
            'utilizador_id' => $user->id,
            'lugar_id' => $request->lugar_id,
            'data' => $request->data,
            'estado' => 'ATIVA',
        ]);
        
        // Descontar pontos
        $user->decrement('pontos', 5);
        
        // Registar movimento de pontos
        MovimentoPontos::create([
            'utilizador_id' => $user->id,
            'reserva_id' => $reserva->id,
            'tipo' => 'RESERVA',
            'pontos' => -5,
        ]);
        
        // Registar no histórico
        HistoricoEventos::create([
            'utilizador_id' => $user->id,
            'tipo_evento' => 'RESERVA',
            'entidade_id' => $reserva->id,
            'acao' => 'CRIADO',
            'descricao' => "Reservou o lugar {$reserva->lugar->numero} para " . Carbon::parse($request->data)->format('d/m/Y'),
        ]);
        
        return redirect('/reservas')->with('success', 'Reserva criada com sucesso!');
    }
    
    public function show($id)
    {
        $reserva = Reserva::with(['lugar', 'utilizador', 'validadaPor'])
            ->findOrFail($id);
        
        // Verificar permissões
        if ($reserva->utilizador_id !== auth('utilizador')->id() && 
            !in_array(auth('utilizador')->user()->role, ['ADMIN', 'SEGURANCA'])) {
            abort(403, 'Não tem permissão para ver esta reserva.');
        }
        
        // Buscar movimentos de pontos relacionados
        $movimentosPontos = MovimentoPontos::where('reserva_id', $id)
            ->orderBy('created_at')
            ->get();
        
        return view('reservas.show', compact('reserva', 'movimentosPontos'));
    }
    
    public function destroy($id)
    {
        $reserva = Reserva::findOrFail($id);
        $user = auth('utilizador')->user();
        
        // Verificar se é dono da reserva
        if ($reserva->utilizador_id !== $user->id) {
            abort(403, 'Não pode cancelar esta reserva.');
        }
        
        // Verificar se pode cancelar
        if ($reserva->estado !== 'ATIVA') {
            return back()->with('error', 'Esta reserva não pode ser cancelada.');
        }
        
        // Atualizar estado
        $reserva->update(['estado' => 'CANCELADA']);
        
        // Devolver pontos se cancelou com antecedência (24h)
        $dataReserva = Carbon::parse($reserva->data);
        $horasAntecedencia = $dataReserva->diffInHours(now());
        
        if ($horasAntecedencia > 24) {
            $user->increment('pontos', 3);
            
            MovimentoPontos::create([
                'utilizador_id' => $user->id,
                'reserva_id' => $reserva->id,
                'tipo' => 'CANCELAMENTO',
                'pontos' => 3,
            ]);
            
            $mensagem = 'Reserva cancelada! Recuperou 3 pontos.';
        } else {
            $mensagem = 'Reserva cancelada. Sem recuperação de pontos (cancelamento tardio).';
        }
        
        // Registar histórico
        HistoricoEventos::create([
            'utilizador_id' => $user->id,
            'tipo_evento' => 'RESERVA',
            'entidade_id' => $reserva->id,
            'acao' => 'CANCELADO',
            'descricao' => "Cancelou reserva do lugar {$reserva->lugar->numero}",
        ]);
        
        return redirect('/reservas')->with('success', $mensagem);
    }
    
    // API para AJAX - Buscar lugares disponíveis
    public function getDisponibilidade(Request $request)
    {
        $data = $request->input('data');
        
        $lugares = Lugar::where('ativo', true)
            ->get()
            ->map(function($lugar) use ($data) {
                $ocupado = Reserva::where('lugar_id', $lugar->id)
                    ->where('data', $data)
                    ->where('estado', 'ATIVA')
                    ->exists();
                
                return [
                    'id' => $lugar->id,
                    'numero' => $lugar->numero,
                    'disponivel' => !$ocupado,
                ];
            });
        
        return response()->json($lugares);
    }

}

// // Criar reserva
//     public function store(Request $request)
// {
//     $request->validate([
//         'lugar_id' => 'required|exists:lugar,id',
//         'data' => 'required|date'
//     ]);

//     $user = $request->user();
//     $dataReserva = Carbon::parse($request->data);
//     $hoje = Carbon::today();

//     // data passada
//     if ($dataReserva->lt($hoje)) {
//         return response()->json(['error' => 'Não podes reservar datas passadas'], 400);
//     }

//     // hoje depois das 10h
//     if ($dataReserva->isToday() && now()->format('H:i') > '10:00') {
//         return response()->json(['error' => 'Reservas para hoje só até às 10h'], 400);
//     }

//     // fora da semana atual + próxima
//     $fimSemanaSeguinte = Carbon::now()->addWeek()->endOfWeek();
//     if ($dataReserva->gt($fimSemanaSeguinte)) {
//         return response()->json(['error' => 'Só podes reservar até à próxima semana'], 400);
//     }

//     // sem pontos
//     if ($user->pontos < 3) {
//         return response()->json(['error' => 'Não tens pontos suficientes'], 400);
//     }

//     // lugar ocupado
//     $ocupado = Reserva::where('lugar_id', $request->lugar_id)
//         ->where('data', $dataReserva->toDateString())
//         ->where('estado', 'ATIVA')
//         ->exists();

//     if ($ocupado) {
//         return response()->json(['error' => 'Lugar já reservado'], 400);
//     }

//     $reserva = Reserva::create([
//         'utilizador_id' => $user->id,
//         'lugar_id' => $request->lugar_id,
//         'data' => $dataReserva,
//         'estado' => 'ATIVA'
//     ]);

//     PointsService::deductReserva($user);

//     return response()->json($reserva, 201);
// }

// public function validarPresenca(Request $request, $id)
// {
//     $reserva = Reserva::find($id);

//     if (!$reserva) {
//         return response()->json(['error' => 'Reserva não encontrada'], 404);
//     }

//     // Marca a reserva como validada pelo segurança
//     $reserva->validada_por = $request->user()->id; // o segurança que validou
//     $reserva->save();

//     // Notifica o utilizador que a presença foi validada
//     NotificationService::notifyUser(
//         $reserva->utilizador->id,
//         "Sua presença na reserva do dia {$reserva->data} foi validada."
//     );

//     return response()->json(['message' => 'Reserva validada com sucesso', 'reserva' => $reserva]);
// }