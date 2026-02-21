<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
// use App\Models\Utilizador;
use App\Models\Lugar;
use App\Services\PointsService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\MovimentoPontos; // podem não ser
use App\Models\HistoricoEventos; // podem não ser
use Illuminate\Support\Facades\DB;


class ReservaController extends Controller
{
    private const LUGARES_FIXOS = [1, 2, 5];

    public function index(Request $request)
    {
        $user = auth('utilizador')->user();

        $allowedSortsAdmin = ['lugar', 'data', 'utilizador', 'estado'];
        $allowedSortsUser = ['lugar', 'data', 'estado'];

        $sortAtivas = $request->get('sort_ativas', 'data');
        $directionAtivas = strtolower($request->get('direction_ativas', 'asc')) === 'desc' ? 'desc' : 'asc';

        $sortHistorico = $request->get('sort_historico', 'data');
        $directionHistorico = strtolower($request->get('direction_historico', 'desc')) === 'asc' ? 'asc' : 'desc';

        if ($user->role === 'ADMIN') {
            if (!in_array($sortAtivas, $allowedSortsAdmin, true)) {
                $sortAtivas = 'data';
            }
            if (!in_array($sortHistorico, $allowedSortsAdmin, true)) {
                $sortHistorico = 'data';
            }

            $ativasQuery = Reserva::query()->with(['lugar', 'utilizador'])
                ->where('estado', 'ATIVA')
                ->where('data', '>=', today());

            if ($sortAtivas === 'lugar') {
                $ativasQuery->join('lugar as l', 'l.id', '=', 'reserva.lugar_id')
                    ->select('reserva.*')
                    ->orderBy('l.numero', $directionAtivas);
            } elseif ($sortAtivas === 'utilizador') {
                $ativasQuery->join('utilizador as u', 'u.id', '=', 'reserva.utilizador_id')
                    ->select('reserva.*')
                    ->orderBy('u.nome', $directionAtivas);
            } else {
                $ativasQuery->orderBy($sortAtivas, $directionAtivas);
            }

            $reservasAtivas = $ativasQuery->orderBy('id')->get();

            $historicoQuery = Reserva::query()->with(['lugar', 'utilizador'])
                ->where(function ($q) {
                    $q->where('estado', '!=', 'ATIVA')
                        ->orWhere('data', '<', today());
                });

            if ($sortHistorico === 'lugar') {
                $historicoQuery->join('lugar as l', 'l.id', '=', 'reserva.lugar_id')
                    ->select('reserva.*')
                    ->orderBy('l.numero', $directionHistorico);
            } elseif ($sortHistorico === 'utilizador') {
                $historicoQuery->join('utilizador as u', 'u.id', '=', 'reserva.utilizador_id')
                    ->select('reserva.*')
                    ->orderBy('u.nome', $directionHistorico);
            } else {
                $historicoQuery->orderBy($sortHistorico, $directionHistorico);
            }

            $reservasHistorico = $historicoQuery
                ->orderBy('id', 'desc')
                ->paginate(20)
                ->withQueryString();
        } else {
            if (!in_array($sortAtivas, $allowedSortsUser, true)) {
                $sortAtivas = 'data';
            }
            if (!in_array($sortHistorico, $allowedSortsUser, true)) {
                $sortHistorico = 'data';
            }

            // Reservas ativas (futuras ou hoje)
            $ativasQuery = Reserva::query()
                ->where('utilizador_id', $user->id)
                ->where('estado', 'ATIVA')
                ->where('data', '>=', today())
                ->with('lugar');

            if ($sortAtivas === 'lugar') {
                $ativasQuery->join('lugar as l', 'l.id', '=', 'reserva.lugar_id')
                    ->select('reserva.*')
                    ->orderBy('l.numero', $directionAtivas);
            } else {
                $ativasQuery->orderBy($sortAtivas, $directionAtivas);
            }

            $reservasAtivas = $ativasQuery->orderBy('id')->get();

            // Histórico (passadas ou canceladas)
            $historicoQuery = Reserva::query()
                ->where('utilizador_id', $user->id)
                ->where(function ($q) {
                    $q->where('estado', '!=', 'ATIVA')
                        ->orWhere('data', '<', today());
                })
                ->with('lugar');

            if ($sortHistorico === 'lugar') {
                $historicoQuery->join('lugar as l', 'l.id', '=', 'reserva.lugar_id')
                    ->select('reserva.*')
                    ->orderBy('l.numero', $directionHistorico);
            } else {
                $historicoQuery->orderBy($sortHistorico, $directionHistorico);
            }

            $reservasHistorico = $historicoQuery
                ->orderBy('id', 'desc')
                ->paginate(10)
                ->withQueryString();
        }

        return view('reservas.index', compact('reservasAtivas', 'reservasHistorico'));
    }

    public function create()
    {
        return view('reservas.create');
    }

    public function store(Request $request)
    {
        $user = auth('utilizador')->user();
        $isAdmin = $user->role === 'ADMIN';
        $modoPedido = $isAdmin
            ? strtoupper((string) $request->input('modo_reserva', $request->input('modo', 'COLAB')))
            : 'COLAB';

        $validationRules = [
            'data' => 'required|date',
        ];

        if ($isAdmin) {
            $validationRules['modo_reserva'] = 'required|in:COLAB,ADMIN';
            $validationRules['justificacao_tipo'] = 'required_if:modo_reserva,ADMIN|nullable|in:EVENTO,OBRAS,MOBILIDADE_REDUZIDA,OUTRO';
            $validationRules['justificacao_detalhe'] = 'nullable|string|max:500';
            $validationRules['lugar_id'] = 'required_without:lugar_ids|nullable|exists:lugar,id';
            $validationRules['lugar_ids'] = 'required_without:lugar_id|array|min:1';
            $validationRules['lugar_ids.*'] = 'integer|exists:lugar,id|distinct';
        } else {
            $validationRules['lugar_id'] = 'required|exists:lugar,id';
            $validationRules['data'] .= '|after:today|before:' . now()->addDays(31)->format('Y-m-d');
        }

        $validated = $request->validate($validationRules, [
            'justificacao_tipo.required_if' => 'O motivo da reserva é de preenchimento obrigatório',
        ]);
        $modoReserva = $isAdmin ? strtoupper((string) ($validated['modo_reserva'] ?? $modoPedido)) : 'COLAB';
        $isAdminMode = $isAdmin && $modoReserva === 'ADMIN';

        if (!$isAdminMode) {
            $validated['justificacao_tipo'] = null;
            $validated['justificacao_detalhe'] = null;
            $validatedDataLimit = now()->addDays(31)->toDateString();
            if (Carbon::parse($validated['data'])->lte(today()) || Carbon::parse($validated['data'])->gt($validatedDataLimit)) {
                return back()->withInput()->withErrors([
                    'data' => 'A data deve ser no intervalo permitido para colaboradores.',
                ]);
            }
        }

        if (Carbon::parse($validated['data'])->isWeekend()) {
            return back()->withInput()->withErrors([
                'data' => 'Sábado e domingo estão indisponíveis para reserva.',
            ]);
        }

        if ($isAdminMode && $validated['justificacao_tipo'] === 'OUTRO' && empty($validated['justificacao_detalhe'])) {
            return back()->withInput()->withErrors([
                'justificacao_detalhe' => 'Ao selecionar "Outro motivo", deve descrever a justificação.',
            ]);
        }

        if (!$isAdminMode && empty($validated['lugar_id'])) {
            return back()->withInput()->withErrors([
                'lugar_id' => 'Selecione um lugar.',
            ]);
        }

        if ($isAdminMode && empty($validated['lugar_ids']) && !empty($validated['lugar_id'])) {
            $validated['lugar_ids'] = [(int) $validated['lugar_id']];
        }

        $lugarIds = $isAdminMode
            ? array_values(array_unique(array_map('intval', $validated['lugar_ids'] ?? [])))
            : [(int) $validated['lugar_id']];

        if (empty($lugarIds)) {
            return back()->withInput()->withErrors([
                'lugar_id' => 'Selecione pelo menos um lugar.',
            ]);
        }

        $lugaresSelecionados = Lugar::whereIn('id', $lugarIds)->get()->keyBy('id');
        foreach ($lugarIds as $lugarId) {
            $lugar = $lugaresSelecionados->get($lugarId);
            if (!$lugar || in_array((int) $lugar->numero, self::LUGARES_FIXOS, true)) {
                return back()->withInput()->withErrors([
                    'lugar_id' => 'Um dos lugares selecionados está reservado de forma permanente e não pode ser escolhido.',
                ]);
            }
        }

        if (!$isAdminMode) {
            // Verificar pontos
            if ($user->pontos < 3) {
                return back()->with('error', 'Não tem pontos suficientes! Precisa de 3 pontos.');
            }

            // Verificar se já tem reserva nesse dia
            $jaTemReserva = Reserva::where('utilizador_id', $user->id)
                ->where('data', $validated['data'])
                ->whereIn('estado', ['ATIVA', 'PRESENTE'])
                ->exists();

            if ($jaTemReserva) {
                return back()->with('error', 'Já tem uma reserva para este dia!');
            }

        }

        // Verificar se algum dos lugares já está reservado na data (inclui modo admin)
        $lugaresOcupados = Reserva::whereIn('lugar_id', $lugarIds)
            ->where('data', $validated['data'])
            ->whereIn('estado', ['ATIVA', 'PRESENTE'])
            ->pluck('lugar_id')
            ->all();

        if (!empty($lugaresOcupados)) {
            $lugaresTexto = Lugar::whereIn('id', $lugaresOcupados)
                ->orderBy('numero')
                ->pluck('numero')
                ->implode(', ');

            return back()->with('error', 'Os seguintes lugares já estão reservados para este dia: ' . $lugaresTexto . '.');
        }

        $reservasCriadas = [];
        foreach ($lugarIds as $lugarId) {
            $reserva = Reserva::create([
                'utilizador_id' => $user->id,
                'lugar_id' => $lugarId,
                'data' => $validated['data'],
                'estado' => 'ATIVA',
                'modo_reserva' => $isAdminMode ? 'ADMIN' : 'COLAB',
                'justificacao_tipo' => $isAdminMode ? $validated['justificacao_tipo'] : null,
                'justificacao_detalhe' => $isAdminMode ? ($validated['justificacao_detalhe'] ?? null) : null,
            ]);
            $reservasCriadas[] = $reserva;

            if (!$isAdminMode) {
                // Descontar pontos
                $user->decrement('pontos', 3);

                // Registar movimento de pontos
                MovimentoPontos::create([
                    'utilizador_id' => $user->id,
                    'reserva_id' => $reserva->id,
                    'tipo' => 'RESERVA',
                    'pontos' => -3,
                ]);
            }

            // Registar no histórico
            $sufixoAdmin = '';
            if ($isAdminMode) {
                $sufixoAdmin = " (reserva como ADMIN - {$validated['justificacao_tipo']}";
                if (!empty($validated['justificacao_detalhe'])) {
                    $sufixoAdmin .= ": {$validated['justificacao_detalhe']}";
                }
                $sufixoAdmin .= ')';
            }

            HistoricoEventos::create([
                'utilizador_id' => $user->id,
                'tipo_evento' => 'RESERVA',
                'entidade_id' => $reserva->id,
                'acao' => 'CRIADO',
                'descricao' => "Reservou o lugar {$reserva->lugar->numero} para " . Carbon::parse($validated['data'])->format('d/m/Y') . $sufixoAdmin,
            ]);
        }

        return redirect('/reservas')->with('success', $isAdminMode
            ? count($reservasCriadas) . ' reserva(s) administrativa(s) criada(s) com sucesso.'
            : 'Reserva criada com sucesso!');
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

        $movimentoReserva = $movimentosPontos->firstWhere('tipo', 'RESERVA');
        $reservaCriadaEm = $movimentoReserva?->created_at;

        return view('reservas.show', compact('reserva', 'movimentosPontos', 'reservaCriadaEm'));
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

        // Regras de pontos no cancelamento (apenas reservas em modo colaborador)
        $isReservaColab = ($reserva->modo_reserva ?? 'COLAB') === 'COLAB';

        if ($isReservaColab) {
            // Ao desmarcar: aplica penalização de 2, mesmo com saldo insuficiente.
            $user->decrement('pontos', 2);
            MovimentoPontos::create([
                'utilizador_id' => $user->id,
                'reserva_id' => $reserva->id,
                'tipo' => 'CANCELAMENTO',
                'pontos' => -2,
            ]);

            $mensagem = 'Reserva cancelada. Foi aplicada penalização de 2 pontos.';
        } else {
            $mensagem = 'Reserva cancelada.';
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

    public function edit($id)
    {
        $admin = auth('utilizador')->user();
        if ($admin->role !== 'ADMIN') {
            abort(403, 'Apenas administradores podem editar reservas.');
        }

        $reserva = Reserva::with(['utilizador', 'lugar'])->findOrFail($id);
        $lugares = Lugar::where('ativo', true)->orderBy('numero')->get();

        return view('reservas.edit', compact('reserva', 'lugares'));
    }

    public function update(Request $request, $id)
    {
        $admin = auth('utilizador')->user();
        if ($admin->role !== 'ADMIN') {
            abort(403, 'Apenas administradores podem editar reservas.');
        }

        $reserva = Reserva::with(['utilizador', 'lugar'])->findOrFail($id);

        $validated = $request->validate([
            'data' => 'required|date',
            'lugar_id' => 'required|exists:lugar,id',
            'estado' => 'required|in:ATIVA,PRESENTE,NAO_COMPARECEU,CANCELADA',
        ]);

        $lugarSelecionado = Lugar::find($validated['lugar_id']);
        if (!$lugarSelecionado || in_array((int) $lugarSelecionado->numero, self::LUGARES_FIXOS, true)) {
            return back()->withInput()->withErrors([
                'lugar_id' => 'Este lugar está reservado de forma permanente e não pode ser escolhido.',
            ]);
        }

        $duplicadoUtilizadorData = Reserva::where('utilizador_id', $reserva->utilizador_id)
            ->where('data', $validated['data'])
            ->where('id', '!=', $reserva->id)
            ->exists();

        if ($duplicadoUtilizadorData) {
            return back()->withInput()->withErrors([
                'data' => 'O utilizador já tem uma reserva para essa data.',
            ]);
        }

        $duplicadoLugarData = Reserva::where('lugar_id', $validated['lugar_id'])
            ->where('data', $validated['data'])
            ->where('id', '!=', $reserva->id)
            ->exists();

        if ($duplicadoLugarData) {
            return back()->withInput()->withErrors([
                'lugar_id' => 'O lugar já está atribuído nessa data.',
            ]);
        }

        $estadoAnterior = $reserva->estado;

        $reserva->update([
            'data' => $validated['data'],
            'lugar_id' => $validated['lugar_id'],
            'estado' => $validated['estado'],
            'validada_por' => $validated['estado'] === 'PRESENTE' ? ($reserva->validada_por ?? $admin->id) : null,
        ]);

        HistoricoEventos::create([
            'utilizador_id' => $reserva->utilizador_id,
            'tipo_evento' => 'RESERVA',
            'entidade_id' => $reserva->id,
            'acao' => 'ATUALIZADO',
            'descricao' => "Admin {$admin->nome} editou reserva #{$reserva->id} (estado {$estadoAnterior} -> {$reserva->estado})",
        ]);

        return redirect('/reservas/' . $reserva->id)->with('success', 'Reserva atualizada com sucesso.');
    }

    public function delete($id)
    {
        $admin = auth('utilizador')->user();
        if ($admin->role !== 'ADMIN') {
            abort(403, 'Apenas administradores podem apagar reservas.');
        }

        $reserva = Reserva::with(['lugar', 'utilizador'])->findOrFail($id);

        DB::transaction(function () use ($reserva, $admin) {
            MovimentoPontos::where('reserva_id', $reserva->id)->delete();

            HistoricoEventos::create([
                'utilizador_id' => $reserva->utilizador_id,
                'tipo_evento' => 'RESERVA',
                'entidade_id' => $reserva->id,
                'acao' => 'REMOVIDO',
                'descricao' => "Admin {$admin->nome} apagou reserva #{$reserva->id}",
            ]);

            $reserva->delete();
        });

        return redirect('/reservas')->with('success', 'Reserva apagada com sucesso.');
    }

    // API para AJAX - Buscar lugares disponíveis
    public function getDisponibilidade(Request $request)
    {
        $data = $request->input('data');

        if (!$data) {
            return response()->json(['message' => 'Data é obrigatória.'], 422);
        }

        try {
            $dataCarbon = Carbon::parse($data);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Data inválida.'], 422);
        }

        if ($dataCarbon->isWeekend()) {
            return response()->json([]);
        }

        $lugares = Lugar::where('ativo', true)
            ->get()
            ->map(function($lugar) use ($data) {
                $isFixo = in_array((int) $lugar->numero, self::LUGARES_FIXOS, true);
                $reservaOcupante = Reserva::where('lugar_id', $lugar->id)
                    ->where('data', $data)
                    ->whereIn('estado', ['ATIVA', 'PRESENTE'])
                    ->latest('id')
                    ->first();
                $ocupado = (bool) $reservaOcupante;
                $ocupadoPorAdmin = $ocupado && (($reservaOcupante->modo_reserva ?? 'COLAB') === 'ADMIN');

                return [
                    'id' => $lugar->id,
                    'numero' => $lugar->numero,
                    'disponivel' => !$isFixo && !$ocupado,
                    'motivo' => $isFixo ? 'fixo' : ($ocupado ? 'ocupado' : null),
                    'ocupado' => $ocupado,
                    'fixo' => $isFixo,
                    'ocupado_por_admin' => $ocupadoPorAdmin,
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
