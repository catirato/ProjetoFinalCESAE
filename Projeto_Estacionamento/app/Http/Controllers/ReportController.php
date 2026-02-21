<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function create()
    {
        return view('reports.create');
    }

    public function index(Request $request)
    {
        $allowedSorts = ['data', 'utilizador', 'tipo', 'descricao', 'estado'];
        $sort = $request->get('sort', 'data');
        $direction = strtolower($request->get('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'data';
        }

        $query = Report::query()->with('utilizador');

        if ($sort === 'utilizador') {
            $query->join('utilizador', 'utilizador.id', '=', 'report.utilizador_id')
                ->select('report.*')
                ->orderBy('utilizador.nome', $direction);
        } else {
            $columnMap = [
                'data' => 'created_at',
                'tipo' => 'tipo',
                'descricao' => 'descricao',
                'estado' => 'estado',
            ];

            $query->orderBy($columnMap[$sort], $direction);
        }

        $reports = $query->paginate(20)->withQueryString();

        return view('reports.index', compact('reports'));
    }

    public function show($id)
    {
        $report = Report::with('utilizador')->findOrFail($id);

        return view('reports.show', compact('report'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:LUGAR_OCUPADO,SEM_RESERVA,PROBLEMA',
            'descricao' => 'required|string|max:2000',
        ]);

        $report = Report::create([
            'utilizador_id' => auth('utilizador')->id(),
            'tipo' => $validated['tipo'],
            'descricao' => $validated['descricao'],
            'estado' => 'PENDENTE',
        ]);

        NotificationService::notifyAdminsAboutReport($report);

        return back()->with('success', 'Relatório submetido com sucesso.');
    }

    public function pendentes()
    {
        return Report::where('estado', 'PENDENTE')->get();
    }

    public function validar(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        if ($report->estado !== 'PENDENTE') {
            return back()->with('error', 'Só é possível validar relatórios pendentes.');
        }

        $report->estado = 'VALIDADO';
        $report->save();

        if ($request->boolean('ajustar_pontos')) {
            return redirect()
                ->route('admin.pontos.index')
                ->with('success', 'Relatório validado. Faça agora o ajuste de pontos.');
        }

        return redirect()
            ->route('admin.relatorios.index')
            ->with('success', 'Relatório validado.');
    }

    public function rejeitar($id)
    {
        $report = Report::findOrFail($id);

        if ($report->estado !== 'PENDENTE') {
            return back()->with('error', 'Só é possível rejeitar relatórios pendentes.');
        }

        $report->estado = 'REJEITADO';
        $report->save();

        return back()->with('success', 'Relatório rejeitado.');
    }
}
