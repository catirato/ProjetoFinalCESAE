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
        $filtroNome = trim((string) $request->get('filtro_nome', ''));
        $filtroFuncao = strtoupper(trim((string) $request->get('filtro_funcao', '')));
        $funcoesValidas = ['ADMIN', 'SEGURANCA', 'COLAB'];
        if (!in_array($filtroFuncao, $funcoesValidas, true)) {
            $filtroFuncao = '';
        }

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

            if ($filtroNome !== '' || $filtroFuncao !== '') {
                $ativasQuery->whereHas('utilizador', function ($q) use ($filtroNome, $filtroFuncao) {
                    if ($filtroNome !== '') {
                        $q->where('nome', 'like', '%' . $filtroNome . '%');
                    }
                    if ($filtroFuncao !== '') {
                        $q->where('role', $filtroFuncao);
                    }
                });
            }

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

            if ($filtroNome !== '' || $filtroFuncao !== '') {
                $historicoQuery->whereHas('utilizador', function ($q) use ($filtroNome, $filtroFuncao) {
                    if ($filtroNome !== '') {
                        $q->where('nome', 'like', '%' . $filtroNome . '%');
                    }
                    if ($filtroFuncao !== '') {
                        $q->where('role', $filtroFuncao);
                    }
                });
            }

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

        return view('reservas.index', compact('reservasAtivas', 'reservasHistorico', 'filtroNome', 'filtroFuncao'));
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

        $validationRules = [];

        if ($isAdmin) {
            $validationRules['modo_reserva'] = 'required|in:COLAB,ADMIN';
            $validationRules['tipo_periodo'] = 'required|in:UNICO,INTERVALO';
            $validationRules['data'] = 'required_if:tipo_periodo,UNICO|nullable|date';
            $validationRules['periodos'] = 'required_if:tipo_periodo,INTERVALO|nullable|array|min:1';
            $validationRules['periodos.*.data_inicio'] = 'required_with:periodos|date';
            $validationRules['periodos.*.data_fim'] = 'required_with:periodos|date';
            $validationRules['justificacao_tipo'] = 'required_if:modo_reserva,ADMIN|nullable|in:EVENTO,OBRAS,MOBILIDADE_REDUZIDA,OUTRO';
            $validationRules['justificacao_detalhe'] = 'nullable|string|max:500';
            $validationRules['lugar_id'] = 'nullable|exists:lugar,id';
            $validationRules['lugar_ids'] = 'nullable|array|min:1';
            $validationRules['lugar_ids.*'] = 'integer|exists:lugar,id|distinct';
            $validationRules['lugares_por_dia'] = 'nullable|array';
            $validationRules['lugares_por_dia.*'] = 'nullable|array|min:1';
            $validationRules['lugares_por_dia.*.*'] = 'integer|exists:lugar,id|distinct';
        } else {
            $validationRules['data'] = 'required|date';
            $validationRules['lugar_id'] = 'required|exists:lugar,id';
        }

        $validated = $request->validate($validationRules, [
            'justificacao_tipo.required_if' => 'O motivo da reserva é de preenchimento obrigatório',
        ]);
        $modoReserva = $isAdmin ? strtoupper((string) ($validated['modo_reserva'] ?? $modoPedido)) : 'COLAB';
        $isAdminMode = $isAdmin && $modoReserva === 'ADMIN';
        $tipoPeriodo = $isAdmin ? strtoupper((string) ($validated['tipo_periodo'] ?? 'UNICO')) : 'UNICO';

        if (!$isAdminMode && $tipoPeriodo === 'INTERVALO') {
            return back()->withInput()->withErrors([
                'tipo_periodo' => 'Reserva para vários dias está disponível apenas no modo administrativo.',
            ]);
        }

        if (!$isAdminMode) {
            $validated['justificacao_tipo'] = null;
            $validated['justificacao_detalhe'] = null;
        }

        if ($isAdminMode && $validated['justificacao_tipo'] === 'OUTRO' && empty($validated['justificacao_detalhe'])) {
            return back()->withInput()->withErrors([
                'justificacao_detalhe' => 'Ao selecionar "Outro motivo", deve descrever a justificação.',
            ]);
        }

        $datasReserva = [];
        $selecoesPorDia = [];
        if ($tipoPeriodo === 'INTERVALO') {
            $periodosInput = $validated['periodos'] ?? [];

            $legacyDataInicio = $validated['data_inicio'] ?? null;
            $legacyDataFim = $validated['data_fim'] ?? null;
            if (empty($periodosInput) && !empty($legacyDataInicio) && !empty($legacyDataFim)) {
                $periodosInput[] = [
                    'data_inicio' => $legacyDataInicio,
                    'data_fim' => $legacyDataFim,
                ];
            }

            try {
                $datasReserva = $this->buildWeekdayDateRangesFromPeriods($periodosInput);
            } catch (\InvalidArgumentException $e) {
                return back()->withInput()->withErrors([
                    'periodos' => $e->getMessage(),
                ]);
            }

            if (empty($datasReserva)) {
                return back()->withInput()->withErrors([
                    'periodos' => 'Os períodos selecionados não contêm dias úteis reserváveis.',
                ]);
            }

            $lugaresPorDiaInput = $validated['lugares_por_dia'] ?? [];
            foreach ($datasReserva as $dataReserva) {
                $lugarIdsDia = array_values(array_unique(array_map('intval', $lugaresPorDiaInput[$dataReserva] ?? [])));
                if (empty($lugarIdsDia)) {
                    return back()->withInput()->withErrors([
                        'lugares_por_dia' => 'Selecione pelo menos um lugar para o dia ' . Carbon::parse($dataReserva)->format('d/m/Y') . '.',
                    ]);
                }
                $selecoesPorDia[$dataReserva] = $lugarIdsDia;
            }
        } else {
            $reservaData = Carbon::parse($validated['data']);
            if (!$isAdminMode) {
                $mensagemRegraData = $this->validateColabReservationDate($reservaData);
                if ($mensagemRegraData !== null) {
                    return back()->withInput()->withErrors([
                        'data' => $mensagemRegraData,
                    ]);
                }
            }

            if ($reservaData->isWeekend()) {
                return back()->withInput()->withErrors([
                    'data' => 'Sábado e domingo estão indisponíveis para reserva.',
                ]);
            }

            $datasReserva[] = $reservaData->toDateString();
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
            $selecoesPorDia[$reservaData->toDateString()] = $lugarIds;
        }

        $lugarIds = collect($selecoesPorDia)->flatten()->unique()->values()->all();
        $lugaresSelecionados = Lugar::whereIn('id', $lugarIds)->get()->keyBy('id');
        foreach ($selecoesPorDia as $dataReserva => $lugarIdsDia) {
            foreach ($lugarIdsDia as $lugarId) {
                $lugar = $lugaresSelecionados->get($lugarId);
                if (!$lugar || in_array((int) $lugar->numero, self::LUGARES_FIXOS, true)) {
                    return back()->withInput()->withErrors([
                        'lugar_id' => 'Um dos lugares selecionados está reservado de forma permanente e não pode ser escolhido.',
                    ]);
                }
            }
        }

        if (!$isAdminMode) {
            // Verificar pontos
            $pontosNecessarios = 3 * count($datasReserva);
            if ($user->pontos < $pontosNecessarios) {
                return back()->with('error', "Não tem pontos suficientes! Precisa de {$pontosNecessarios} pontos.");
            }

            // Verificar se já tem reserva em algum dos dias
            $jaTemReserva = Reserva::where('utilizador_id', $user->id)
                ->whereIn('data', $datasReserva)
                ->whereIn('estado', ['ATIVA', 'PRESENTE'])
                ->exists();

            if ($jaTemReserva) {
                return back()->with('error', 'Já tem pelo menos uma reserva ativa num dos dias selecionados.');
            }
        }

        // Verificar conflitos para cada dia com os lugares selecionados nesse dia
        $ocupacoes = collect();
        foreach ($selecoesPorDia as $dataReserva => $lugarIdsDia) {
            $ocupacoesDia = Reserva::with('lugar:id,numero')
                ->whereIn('lugar_id', $lugarIdsDia)
                ->where('data', $dataReserva)
                ->whereIn('estado', ['ATIVA', 'PRESENTE'])
                ->get();
            if ($ocupacoesDia->isNotEmpty()) {
                $ocupacoes = $ocupacoes->merge($ocupacoesDia);
            }
        }

        if ($ocupacoes->isNotEmpty()) {
            $detalhes = $ocupacoes
                ->take(5)
                ->map(function ($reserva) {
                    $numero = $reserva->lugar->numero ?? '?';
                    return "Lugar {$numero} em " . Carbon::parse($reserva->data)->format('d/m/Y');
                })
                ->implode('; ');

            return back()->with('error', 'Existem conflitos de disponibilidade: ' . $detalhes . '.');
        }

        $reservasCriadas = [];
        DB::transaction(function () use ($selecoesPorDia, $user, $isAdminMode, $validated, &$reservasCriadas) {
            foreach ($selecoesPorDia as $dataReserva => $lugarIdsDia) {
                foreach ($lugarIdsDia as $lugarId) {
                    $reserva = Reserva::create([
                        'utilizador_id' => $user->id,
                        'lugar_id' => $lugarId,
                        'data' => $dataReserva,
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
                        'descricao' => "Reservou o lugar {$reserva->lugar->numero} para " . Carbon::parse($dataReserva)->format('d/m/Y') . $sufixoAdmin,
                    ]);
                }
            }
        });

        if ($isAdminMode) {
            $diasSelecionados = count($selecoesPorDia);
            if ($diasSelecionados > 1) {
                return redirect('/reservas')->with('success', count($reservasCriadas) . " reserva(s) administrativa(s) criada(s) para {$diasSelecionados} dia(s) com sucesso.");
            }

            return redirect('/reservas')->with('success', count($reservasCriadas) . ' reserva(s) administrativa(s) criada(s) com sucesso.');
        }

        return redirect('/reservas')->with('success', 'Reserva criada com sucesso!');
    }

    private function buildWeekdayDateRange(Carbon $dataInicio, Carbon $dataFim): array
    {
        $datas = [];
        $cursor = $dataInicio->copy()->startOfDay();
        $fim = $dataFim->copy()->startOfDay();

        while ($cursor->lte($fim)) {
            if (!$cursor->isWeekend()) {
                $datas[] = $cursor->toDateString();
            }
            $cursor->addDay();
        }

        return $datas;
    }

    private function buildWeekdayDateRangesFromPeriods(array $periodos): array
    {
        $datas = [];

        foreach ($periodos as $index => $periodo) {
            $dataInicioRaw = $periodo['data_inicio'] ?? null;
            $dataFimRaw = $periodo['data_fim'] ?? null;
            $numeroPeriodo = $index + 1;

            if (!$dataInicioRaw || !$dataFimRaw) {
                throw new \InvalidArgumentException("Preencha a data de início e a data de fim no período {$numeroPeriodo}.");
            }

            try {
                $dataInicio = Carbon::parse($dataInicioRaw);
                $dataFim = Carbon::parse($dataFimRaw);
            } catch (\Exception $e) {
                throw new \InvalidArgumentException("O período {$numeroPeriodo} contém datas inválidas.");
            }

            if ($dataFim->lt($dataInicio)) {
                throw new \InvalidArgumentException("No período {$numeroPeriodo}, a data de fim deve ser igual ou posterior à data de início.");
            }

            $datas = array_merge($datas, $this->buildWeekdayDateRange($dataInicio, $dataFim));
        }

        $datas = array_values(array_unique($datas));
        sort($datas);

        return $datas;
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

    public function adminCancel($id)
    {
        $admin = auth('utilizador')->user();
        if ($admin->role !== 'ADMIN') {
            abort(403, 'Apenas administradores podem cancelar reservas de outros utilizadores.');
        }

        $reserva = Reserva::with(['lugar', 'utilizador'])->findOrFail($id);

        if ($reserva->estado !== 'ATIVA') {
            return back()->with('error', 'Apenas reservas ativas podem ser canceladas.');
        }

        $isReservaColab = ($reserva->modo_reserva ?? 'COLAB') === 'COLAB';

        DB::transaction(function () use ($reserva, $admin) {
            $reserva->update(['estado' => 'CANCELADA']);

            if (($reserva->modo_reserva ?? 'COLAB') === 'COLAB') {
                $reserva->utilizador->increment('pontos', 3);
                MovimentoPontos::create([
                    'utilizador_id' => $reserva->utilizador_id,
                    'reserva_id' => $reserva->id,
                    'tipo' => 'AJUSTE',
                    'pontos' => 3,
                ]);
            }

            HistoricoEventos::create([
                'utilizador_id' => $reserva->utilizador_id,
                'tipo_evento' => 'RESERVA',
                'entidade_id' => $reserva->id,
                'acao' => 'CANCELADO',
                'descricao' => "Admin {$admin->nome} cancelou reserva #{$reserva->id} (lugar {$reserva->lugar->numero})",
            ]);
        });

        $mensagem = $isReservaColab
            ? 'Reserva cancelada com sucesso. Foram devolvidos 3 pontos ao utilizador.'
            : 'Reserva cancelada com sucesso.';

        return redirect('/reservas')->with('success', $mensagem);
    }

    // API para AJAX - Buscar lugares disponíveis
    public function getDisponibilidade(Request $request)
    {
        $user = auth('utilizador')->user();
        $modoPedido = strtoupper((string) $request->input('modo_reserva', $request->input('modo', 'COLAB')));
        $isAdminMode = $user && $user->role === 'ADMIN' && $modoPedido === 'ADMIN';
        $tipoPeriodo = strtoupper((string) $request->input('tipo_periodo', 'UNICO'));
        $datasConsulta = [];
        $data = $request->input('data');

        if ($isAdminMode && $tipoPeriodo === 'INTERVALO') {
            $periodosInput = $request->input('periodos', []);

            if (empty($periodosInput)) {
                $dataInicio = $request->input('data_inicio');
                $dataFim = $request->input('data_fim');
                if ($dataInicio && $dataFim) {
                    $periodosInput[] = [
                        'data_inicio' => $dataInicio,
                        'data_fim' => $dataFim,
                    ];
                }
            }

            if (empty($periodosInput)) {
                return response()->json(['message' => 'Pelo menos um período com início e fim é obrigatório.'], 422);
            }

            try {
                $datasConsulta = $this->buildWeekdayDateRangesFromPeriods($periodosInput);
            } catch (\InvalidArgumentException $e) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            if (empty($datasConsulta)) {
                return response()->json([
                    'bloqueado' => true,
                    'mensagem' => 'Os períodos selecionados não contêm dias úteis reserváveis.',
                ]);
            }

            $dias = collect($datasConsulta)
                ->map(function ($dataConsulta) {
                    return [
                        'data' => $dataConsulta,
                        'lugares' => $this->getLugaresDisponibilidadePorData($dataConsulta),
                    ];
                })
                ->values();

            return response()->json([
                'bloqueado' => false,
                'dias' => $dias,
            ]);
        } else {
            if (!$data) {
                return response()->json(['message' => 'Data é obrigatória.'], 422);
            }

            try {
                $dataCarbon = Carbon::parse($data);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Data inválida.'], 422);
            }

            if ($dataCarbon->isWeekend()) {
                return response()->json([
                    'bloqueado' => false,
                    'lugares' => [],
                ]);
            }

            $datasConsulta = [$dataCarbon->toDateString()];
            $data = $dataCarbon->toDateString();
        }

        // Colaborador: só pode ter uma reserva por dia (bloqueia seleção de lugares)
        if (!$isAdminMode && $user) {
            $dataCarbon = Carbon::parse($data);
            $mensagemRegraData = $this->validateColabReservationDate($dataCarbon);
            if ($mensagemRegraData !== null) {
                return response()->json([
                    'bloqueado' => true,
                    'mensagem' => $mensagemRegraData,
                ]);
            }

            $reservaDoDia = Reserva::where('utilizador_id', $user->id)
                ->where('data', $data)
                ->whereIn('estado', ['ATIVA', 'PRESENTE'])
                ->with('lugar:id,numero')
                ->latest('id')
                ->first();

            if ($reservaDoDia) {
                $numeroLugar = $reservaDoDia->lugar->numero ?? null;
                $mensagem = 'Já tem uma reserva para este dia.';
                if ($numeroLugar) {
                    $mensagem .= ' Lugar reservado: ' . $numeroLugar . '.';
                }

                return response()->json([
                    'bloqueado' => true,
                    'mensagem' => $mensagem,
                ]);
            }
        }

        $lugares = $this->getLugaresDisponibilidadePorData($datasConsulta[0]);

        return response()->json([
            'bloqueado' => false,
            'lugares' => $lugares,
        ]);
    }

    private function getLugaresDisponibilidadePorData(string $data): \Illuminate\Support\Collection
    {
        return Lugar::where('ativo', true)
            ->get()
            ->map(function ($lugar) use ($data) {
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
    }

    private function validateColabReservationDate(Carbon $dataReserva): ?string
    {
        $hoje = now();
        $fimSemanaSeguinte = $hoje->copy()->addWeek()->endOfWeek(Carbon::FRIDAY);

        if ($dataReserva->lt($hoje->copy()->startOfDay())) {
            return 'Não pode reservar para datas passadas.';
        }

        if ($dataReserva->isToday() && $hoje->gt($hoje->copy()->setTime(10, 0))) {
            return 'Para hoje, a reserva só pode ser feita até às 10:00.';
        }

        if ($dataReserva->gt($fimSemanaSeguinte->copy()->startOfDay())) {
            return 'Pode reservar apenas para o resto desta semana e para a semana seguinte.';
        }

        return null;
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
