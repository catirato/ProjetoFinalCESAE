<?php

namespace App\Http\Controllers;

use App\Models\Utilizador;
use App\Models\Reserva;
use App\Models\MovimentoPontos;
use App\Models\HistoricoEventos;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HistoricoController extends Controller
{
    /**
     * Lista todos os utilizadores com resumo (nome, email, role, pontos, nº reservas, nº faltas).
     * Ponto de entrada para o admin.
     */
    public function index(Request $request)
    {
        $query = Utilizador::query();

        // Filtro por role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Pesquisa por nome
        if ($request->filled('search')) {
            $query->where('nome', 'LIKE', '%' . $request->search . '%');
        }

        $utilizadores = $query
            ->withCount([
                'reservas',
                'reservas as faltas_count' => function ($q) {
                    $q->where('estado', 'NAO_COMPARECEU');
                },
            ])
            ->orderBy('nome')
            ->paginate(15)
            ->appends($request->only(['role', 'search']));

        return view('historico.index', compact('utilizadores'));
    }

    /**
     * Detalhe de um utilizador: tabelas com reservas, movimentos de pontos e eventos.
     * Dados para gráficos em HTML/CSS.
     */
    public function show($id)
    {
        $utilizador = Utilizador::findOrFail($id);

        // Reservas com paginação
        $reservas = Reserva::where('utilizador_id', $id)
            ->with(['lugar', 'validadaPor'])
            ->orderBy('data', 'desc')
            ->paginate(10, ['*'], 'reservas_page');

        // Movimentos de pontos com paginação
        $movimentos = MovimentoPontos::where('utilizador_id', $id)
            ->with('reserva')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'movimentos_page');

        // Histórico de eventos com paginação
        $eventos = HistoricoEventos::where('utilizador_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'eventos_page');

        // ───── Dados para gráficos ─────

        // 1. Reservas por mês (últimos 6 meses)
        $reservasPorMes = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = Reserva::where('utilizador_id', $id)
                ->whereYear('data', $date->year)
                ->whereMonth('data', $date->month)
                ->count();
            $reservasPorMes[] = [
                'mes'   => $date->locale('pt')->isoFormat('MMM YYYY'),
                'total' => $count,
            ];
        }
        $maxReservasMes = max(array_column($reservasPorMes, 'total') ?: [1]);

        // 2. Distribuição de estados
        $estados = ['ATIVA', 'PRESENTE', 'NAO_COMPARECEU', 'CANCELADA'];
        $distribuicaoEstados = [];
        foreach ($estados as $estado) {
            $distribuicaoEstados[] = [
                'estado' => $estado,
                'total'  => Reserva::where('utilizador_id', $id)->where('estado', $estado)->count(),
            ];
        }
        $maxEstado = max(array_column($distribuicaoEstados, 'total') ?: [1]);

        // 3. Evolução de pontos – ganhos vs gastos (últimos 6 meses)
        $evolucaoPontos = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $ganhos = MovimentoPontos::where('utilizador_id', $id)
                ->where('pontos', '>', 0)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('pontos');
            $gastos = abs(MovimentoPontos::where('utilizador_id', $id)
                ->where('pontos', '<', 0)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('pontos'));
            $evolucaoPontos[] = [
                'mes'    => $date->locale('pt')->isoFormat('MMM YYYY'),
                'ganhos' => $ganhos,
                'gastos' => $gastos,
            ];
        }
        $maxPontos = max(
            max(array_column($evolucaoPontos, 'ganhos') ?: [1]),
            max(array_column($evolucaoPontos, 'gastos') ?: [1]),
            1
        );

        return view('historico.show', compact(
            'utilizador',
            'reservas',
            'movimentos',
            'eventos',
            'reservasPorMes',
            'maxReservasMes',
            'distribuicaoEstados',
            'maxEstado',
            'evolucaoPontos',
            'maxPontos'
        ));
    }

    /**
     * Formulário para editar um registo do historico_eventos.
     */
    public function editEvento($id)
    {
        $evento = HistoricoEventos::findOrFail($id);
        $utilizador = Utilizador::findOrFail($evento->utilizador_id);

        return view('historico.editEvento', compact('evento', 'utilizador'));
    }

    /**
     * Guarda a edição de um evento.
     */
    public function updateEvento(Request $request, $id)
    {
        $request->validate([
            'tipo_evento' => 'required|in:RESERVA,LISTA_ESPERA,REPORT,PONTOS',
            'acao'        => 'required|in:CRIADO,ATUALIZADO,REMOVIDO,VALIDADO,CANCELADO',
            'descricao'   => 'required|string|max:255',
        ]);

        $evento = HistoricoEventos::findOrFail($id);
        $evento->update($request->only(['tipo_evento', 'acao', 'descricao']));

        return redirect('/historico/' . $evento->utilizador_id)
            ->with('success', 'Evento atualizado com sucesso!');
    }

    /**
     * Apaga um registo do histórico (limpeza de dados de teste).
     */
    public function destroyEvento($id)
    {
        $evento = HistoricoEventos::findOrFail($id);
        $utilizadorId = $evento->utilizador_id;
        $evento->delete();

        return redirect('/historico/' . $utilizadorId)
            ->with('success', 'Evento apagado com sucesso!');
    }
}
