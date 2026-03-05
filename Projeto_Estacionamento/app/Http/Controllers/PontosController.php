<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MovimentoPontos;
use App\Models\Reserva;
use App\Models\Report;
use App\Models\Utilizador;
use App\Services\PointsService;

class PontosController extends Controller
{
    public function index()
    {
        $user = auth('utilizador')->user();
        return view('pontos.pontos', $this->buildHistoricoData($user));
    }

    public function showByUser($id)
    {
        $admin = auth('utilizador')->user();
        if ($admin->role !== 'ADMIN') {
            abort(403, 'Apenas administradores podem visualizar históricos de outros utilizadores.');
        }

        $user = Utilizador::findOrFail($id);
        return view('pontos.pontos', $this->buildHistoricoData($user));
    }

    public function adminIndex()
    {
        $admin = auth('utilizador')->user();
        if ($admin->role !== 'ADMIN') {
            abort(403, 'Apenas administradores podem gerir pontos.');
        }

        $filtroNome = trim((string) request('filtro_nome', ''));
        $filtroFuncao = strtoupper(trim((string) request('filtro_funcao', '')));
        $funcoesValidas = ['ADMIN', 'SEGURANCA', 'COLAB'];
        if (!in_array($filtroFuncao, $funcoesValidas, true)) {
            $filtroFuncao = '';
        }

        $reportEmAjuste = null;
        $reportId = (int) request('report_id', 0);
        if ($reportId > 0) {
            $reportEmAjuste = Report::with('utilizador')
                ->where('id', $reportId)
                ->where('estado', 'VALIDADO')
                ->first();
        }

        $utilizadores = Utilizador::query()
            ->where('role', '!=', 'SEGURANCA')
            ->when($filtroNome !== '', function ($query) use ($filtroNome) {
                $query->where('nome', 'like', '%' . $filtroNome . '%');
            })
            ->when($filtroFuncao !== '', function ($query) use ($filtroFuncao) {
                $query->where('role', $filtroFuncao);
            })
            ->orderBy('nome')
            ->paginate(20)
            ->withQueryString();

        return view('pontos.admin', compact('utilizadores', 'reportEmAjuste', 'filtroNome', 'filtroFuncao'));
    }

    public function adminAdjust(Request $request, $id)
    {
        $admin = auth('utilizador')->user();
        if ($admin->role !== 'ADMIN') {
            abort(403, 'Apenas administradores podem gerir pontos.');
        }

        $validated = $request->validate([
            'ajuste' => ['required', 'integer', 'between:-100,100', 'not_in:0'],
            'report_id' => ['nullable', 'integer', 'exists:report,id'],
        ]);

        $user = Utilizador::findOrFail($id);
        if ($user->role === 'SEGURANCA') {
            return back()->with('error', 'Utilizadores de segurança não têm gestão de pontos.');
        }

        PointsService::addPoints($user, (int) $validated['ajuste']);

        if (!empty($validated['report_id'])) {
            $report = Report::find($validated['report_id']);
            if ($report
                && (int) $report->utilizador_id === (int) $user->id
                && $report->estado === 'VALIDADO'
                && $report->ajuste_pontos_necessario
            ) {
                $report->ajuste_pontos_concluido = true;
                $report->save();

                return redirect()
                    ->route('admin.relatorios.index')
                    ->with('success', "Pontos de {$user->nome} ajustados e relatório marcado como concluído.");
            }
        }

        return back()->with('success', "Pontos de {$user->nome} ajustados com sucesso.");
    }

    private function buildHistoricoData(Utilizador $user): array
    {
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

        // Gráfico 1: reservas por mês (últimos 6 meses)
        $inicioJanela = now()->copy()->startOfMonth()->subMonths(5);
        $fimJanela = now()->copy()->endOfMonth();

        $reservasPorMesRaw = Reserva::where('utilizador_id', $user->id)
            ->whereBetween('data', [$inicioJanela->toDateString(), $fimJanela->toDateString()])
            ->selectRaw("DATE_FORMAT(data, '%Y-%m') as periodo, COUNT(*) as total")
            ->groupBy('periodo')
            ->orderBy('periodo')
            ->pluck('total', 'periodo');

        $mesesPt = [
            1 => 'Jan',
            2 => 'Fev',
            3 => 'Mar',
            4 => 'Abr',
            5 => 'Mai',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Ago',
            9 => 'Set',
            10 => 'Out',
            11 => 'Nov',
            12 => 'Dez',
        ];

        $reservasPorMesLabels = [];
        $reservasPorMesData = [];
        for ($i = 5; $i >= 0; $i--) {
            $mes = now()->copy()->subMonths($i);
            $chave = $mes->format('Y-m');
            $reservasPorMesLabels[] = $mesesPt[(int) $mes->format('n')] . '/' . $mes->format('y');
            $reservasPorMesData[] = (int) ($reservasPorMesRaw[$chave] ?? 0);
        }

        // Gráfico 2: distribuição de estados das reservas
        $estadosRaw = Reserva::where('utilizador_id', $user->id)
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $estadoLabels = [
            'ATIVA' => 'Ativa',
            'PRESENTE' => 'Presente',
            'NAO_COMPARECEU' => 'Não Compareceu',
            'CANCELADA' => 'Cancelada',
        ];

        $distribuicaoEstadosLabels = [];
        $distribuicaoEstadosData = [];
        foreach ($estadoLabels as $estado => $label) {
            $distribuicaoEstadosLabels[] = $label;
            $distribuicaoEstadosData[] = (int) ($estadosRaw[$estado] ?? 0);
        }

        return [
            'historicoUser' => $user,
            'movimentos' => $movimentos,
            'pontosGanhos' => $pontosGanhos,
            'pontosGastos' => $pontosGastos,
            'diasProximoReset' => $diasProximoReset,
            'reservasPorMesLabels' => $reservasPorMesLabels,
            'reservasPorMesData' => $reservasPorMesData,
            'distribuicaoEstadosLabels' => $distribuicaoEstadosLabels,
            'distribuicaoEstadosData' => $distribuicaoEstadosData,
        ];
    }
}
