<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function create()
    {
        return view('reports.create');
    }

    public function index(Request $request)
    {
        $allowedSorts = ['data', 'utilizador', 'tipo', 'descricao', 'estado'];
        $allowedRoles = ['ADMIN', 'SEGURANCA', 'COLAB'];
        $sort = $request->get('sort', 'data');
        $direction = strtolower($request->get('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
        $search = trim((string) $request->get('search', ''));
        $role = strtoupper(trim((string) $request->get('role', '')));

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'data';
        }

        if (!in_array($role, $allowedRoles, true)) {
            $role = '';
        }

        $query = Report::query()->with('utilizador');

        if ($search !== '') {
            $query->whereHas('utilizador', function ($q) use ($search) {
                $q->where('nome', 'like', '%' . $search . '%');
            });
        }

        if ($role !== '') {
            $query->whereHas('utilizador', function ($q) use ($role) {
                $q->where('role', $role);
            });
        }

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

        return view('reports.index', compact('reports', 'search', 'role'));
    }

    public function show($id)
    {
        $report = Report::with('utilizador')->findOrFail($id);

        return view('reports.show', compact('report'));
    }

    public function edit($id)
    {
        $report = Report::with('utilizador')->findOrFail($id);

        return view('reports.edit', compact('report'));
    }

    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        $validated = $request->validate([
            'tipo' => 'required|in:LUGAR_OCUPADO,SEM_RESERVA,PROBLEMA',
            'descricao' => 'required|string|max:2000',
            'estado' => 'required|in:PENDENTE,VALIDADO,REJEITADO',
            'ajuste_pontos_necessario' => 'nullable|boolean',
            'ajuste_pontos_concluido' => 'nullable|boolean',
            'fotos' => 'nullable|array|max:5',
            'fotos.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'remove_fotos' => 'nullable|array',
            'remove_fotos.*' => 'string',
        ]);

        $estado = $validated['estado'];
        $ajusteNecessario = (bool) ($validated['ajuste_pontos_necessario'] ?? false);
        $ajusteConcluido = (bool) ($validated['ajuste_pontos_concluido'] ?? false);

        if ($estado !== 'VALIDADO') {
            $ajusteNecessario = false;
            $ajusteConcluido = false;
        } elseif (!$ajusteNecessario) {
            $ajusteConcluido = false;
        }

        $existingFotos = array_values($report->fotos ?? []);
        $removeFotos = array_values($validated['remove_fotos'] ?? []);
        $fotosParaRemover = array_values(array_intersect($existingFotos, $removeFotos));

        foreach ($fotosParaRemover as $fotoPath) {
            Storage::disk('public')->delete($fotoPath);
        }

        $fotosRestantes = array_values(array_diff($existingFotos, $fotosParaRemover));
        $novasFotos = $this->storeUploadedPhotos($request);
        $fotosAtualizadas = array_values(array_unique(array_merge($fotosRestantes, $novasFotos)));

        $report->update([
            'tipo' => $validated['tipo'],
            'descricao' => $validated['descricao'],
            'fotos' => !empty($fotosAtualizadas) ? $fotosAtualizadas : null,
            'estado' => $estado,
            'ajuste_pontos_necessario' => $ajusteNecessario,
            'ajuste_pontos_concluido' => $ajusteConcluido,
        ]);

        return back()->with('success', 'Relatório atualizado com sucesso.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:LUGAR_OCUPADO,SEM_RESERVA,PROBLEMA',
            'descricao' => 'required|string|max:2000',
            'fotos' => 'nullable|array|max:5',
            'fotos.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $uploadedPhotos = $this->storeUploadedPhotos($request);

        $report = Report::create([
            'utilizador_id' => auth('utilizador')->id(),
            'tipo' => $validated['tipo'],
            'descricao' => $validated['descricao'],
            'fotos' => !empty($uploadedPhotos) ? $uploadedPhotos : null,
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

        $ajustarPontos = $request->boolean('ajustar_pontos');

        $report->estado = 'VALIDADO';
        $report->ajuste_pontos_necessario = $ajustarPontos;
        $report->ajuste_pontos_concluido = false;
        $report->save();

        if ($ajustarPontos) {
            return redirect()
                ->route('admin.pontos.index', ['report_id' => $report->id])
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

    public function destroy($id)
    {
        $report = Report::findOrFail($id);

        foreach ((array) ($report->fotos ?? []) as $fotoPath) {
            Storage::disk('public')->delete($fotoPath);
        }

        $report->delete();

        return redirect()
            ->route('admin.relatorios.index')
            ->with('success', 'Relatório apagado com sucesso.');
    }

    private function storeUploadedPhotos(Request $request): array
    {
        if (!$request->hasFile('fotos')) {
            return [];
        }

        $paths = [];
        foreach ((array) $request->file('fotos') as $file) {
            if ($file && $file->isValid()) {
                $paths[] = $file->store('reports', 'public');
            }
        }

        return $paths;
    }
}
