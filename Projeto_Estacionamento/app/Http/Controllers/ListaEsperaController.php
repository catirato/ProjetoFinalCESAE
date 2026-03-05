<?php

namespace App\Http\Controllers;

use App\Models\Lugar;
use App\Models\Reserva;
use App\Models\MovimentoPontos;
use App\Models\Utilizador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ListaEspera;
use App\Models\HistoricoEventos;
use Carbon\Carbon;

class ListaEsperaController extends Controller
{
    private const LUGARES_FIXOS = [1, 2, 5];

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
        
        return view('listaEspera.index', compact('minhasEntradas', 'listaCompleta'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'data' => 'required|date|after:today',
        ]);
        
        $user = auth('utilizador')->user();
        
        $entradaExistente = ListaEspera::where('utilizador_id', $user->id)
            ->where('data', $request->data)
            ->first();

        if ($entradaExistente && in_array($entradaExistente->estado, ['ATIVO', 'NOTIFICADO', 'ACEITE'], true)) {
            return back()->with('error', 'Já está na lista de espera para este dia!');
        }
        
        // Verificar se já tem reserva para esse dia
        $jaTemReserva = Reserva::where('utilizador_id', $user->id)
            ->where('data', $request->data)
            ->whereIn('estado', ['ATIVA', 'PRESENTE'])
            ->where(function ($q) {
                $q->where('modo_reserva', 'COLAB')
                    ->orWhereNull('modo_reserva');
            })
            ->exists();
        
        if ($jaTemReserva) {
            return back()->with('error', 'Já tem uma reserva para este dia!');
        }
        
        // Calcular prioridade (baseado nos pontos)
        $prioridade = ListaEspera::where('data', $request->data)
            ->where('estado', 'ATIVO')
            ->count() + 1;

        if ($entradaExistente) {
            $entradaExistente->update([
                'estado' => 'ATIVO',
                'prioridade' => $prioridade,
                'notification_token' => null,
                'notificado_em' => null,
                'expira_em' => null,
            ]);
            $entrada = $entradaExistente;
        } else {
            // Criar entrada
            $entrada = ListaEspera::create([
                'utilizador_id' => $user->id,
                'data' => $request->data,
                'estado' => 'ATIVO',
                'prioridade' => $prioridade,
            ]);
        }
        
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
        $entrada = ListaEspera::findOrFail($id);
        $user = auth('utilizador')->user();

        $resultado = $this->confirmarEntrada($entrada, $user);
        if (!empty($resultado['error'])) {
            return back()->with('error', $resultado['error']);
        }

        return redirect('/reservas')->with('success', 'Vaga aceite! Reserva criada com sucesso.');
    }

    public function confirmFromEmail($id, $token)
    {
        $entrada = ListaEspera::findOrFail($id);
        $user = auth('utilizador')->user();

        $resultado = $this->confirmarEntrada($entrada, $user, $token);
        if (!empty($resultado['error'])) {
            return redirect('/lista-espera')->with('error', $resultado['error']);
        }

        return redirect('/reservas')->with('success', 'Reserva confirmada através do email.');
    }

    private function confirmarEntrada(ListaEspera $entrada, Utilizador $user, ?string $token = null): array
    {
        if ($entrada->utilizador_id !== $user->id) {
            abort(403);
        }

        if ($entrada->estado !== 'NOTIFICADO') {
            return ['error' => 'Esta vaga já não está disponível.'];
        }

        if ($token !== null && !hash_equals((string) $entrada->notification_token, (string) $token)) {
            return ['error' => 'Link de confirmação inválido.'];
        }

        $dataReserva = Carbon::parse($entrada->data);
        if ($dataReserva->isToday() && now()->gt($dataReserva->copy()->setTime(10, 0))) {
            $entrada->update(['estado' => 'EXPIRADO', 'notification_token' => null]);
            return ['error' => 'Para vagas de hoje, a confirmação só pode ser feita até às 10h.'];
        }

        if ($entrada->expira_em && now()->gt($entrada->expira_em)) {
            $entrada->update(['estado' => 'EXPIRADO', 'notification_token' => null]);
            return ['error' => 'O prazo de confirmação desta vaga já expirou.'];
        }

        if ($user->pontos < 3) {
            return ['error' => 'Não tem pontos suficientes para confirmar a vaga.'];
        }

        return DB::transaction(function () use ($entrada, $user, $dataReserva) {
            $entradaLocked = ListaEspera::where('id', $entrada->id)->lockForUpdate()->first();
            if (!$entradaLocked || $entradaLocked->estado !== 'NOTIFICADO') {
                return ['error' => 'Esta vaga já foi ocupada por outro colaborador.'];
            }

            $jaTemReserva = Reserva::where('utilizador_id', $user->id)
                ->where('data', $dataReserva->toDateString())
                ->whereIn('estado', ['ATIVA', 'PRESENTE'])
                ->where(function ($q) {
                    $q->where('modo_reserva', 'COLAB')
                        ->orWhereNull('modo_reserva');
                })
                ->lockForUpdate()
                ->exists();
            if ($jaTemReserva) {
                return ['error' => 'Já tem uma reserva ativa para esta data.'];
            }

            $lugarLivre = Lugar::where('ativo', true)
                ->whereNotIn('numero', self::LUGARES_FIXOS)
                ->whereDoesntHave('reservas', function ($q) use ($dataReserva) {
                    $q->where('data', $dataReserva->toDateString())
                        ->whereIn('estado', ['ATIVA', 'PRESENTE']);
                })
                ->lockForUpdate()
                ->first();

            if (!$lugarLivre) {
                $entradaLocked->update(['estado' => 'EXPIRADO', 'notification_token' => null]);
                return ['error' => 'A vaga já não está disponível.'];
            }

            $reserva = Reserva::create([
                'utilizador_id' => $user->id,
                'lugar_id' => $lugarLivre->id,
                'data' => $dataReserva->toDateString(),
                'estado' => 'ATIVA',
            ]);

            $user->decrement('pontos', 3);

            MovimentoPontos::create([
                'utilizador_id' => $user->id,
                'reserva_id' => $reserva->id,
                'tipo' => 'RESERVA',
                'pontos' => -3,
            ]);

            HistoricoEventos::create([
                'utilizador_id' => $user->id,
                'tipo_evento' => 'LISTA_ESPERA',
                'entidade_id' => $entradaLocked->id,
                'acao' => 'ATUALIZADO',
                'descricao' => "Aceitou vaga da lista de espera para " . $dataReserva->format('d/m/Y'),
            ]);

            $entradaLocked->update([
                'estado' => 'ACEITE',
                'notification_token' => null,
            ]);

            ListaEspera::where('data', $dataReserva->toDateString())
                ->where('estado', 'NOTIFICADO')
                ->where('id', '!=', $entradaLocked->id)
                ->update([
                    'estado' => 'EXPIRADO',
                    'notification_token' => null,
                ]);

            return ['reserva_id' => $reserva->id];
        });
    }
}
