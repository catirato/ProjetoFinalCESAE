<?php

namespace App\Http\Controllers;

use App\Models\HistoricoEventos;
use App\Models\Report;
use App\Models\Reserva;
use App\Models\Utilizador;
use App\Services\PointsService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SegurancaController extends Controller
{
    public function index()
    {
        $this->processarNaoComparecenciasAtrasadas();

        $reservasHoje = $this->baseReservasHojeQuery()
            ->whereIn('estado', ['ATIVA', 'PRESENTE'])
            ->orderBy('estado')
            ->orderBy('lugar_id')
            ->get();

        return view('seguranca.index', compact('reservasHoje'));
    }

    public function validadas()
    {
        $this->processarNaoComparecenciasAtrasadas();

        $reservasHoje = $this->baseReservasHojeQuery()
            ->where('estado', 'PRESENTE')
            ->orderBy('lugar_id')
            ->get();

        return view('seguranca.validadas', compact('reservasHoje'));
    }

    public function naoCompareceu()
    {
        $this->processarNaoComparecenciasAtrasadas();

        $reservasHoje = $this->baseReservasHojeQuery()
            ->where('estado', 'NAO_COMPARECEU')
            ->orderBy('lugar_id')
            ->get();

        return view('seguranca.nao-compareceu', compact('reservasHoje'));
    }

    public function validarChegada($id)
    {
        $reserva = Reserva::with(['utilizador', 'lugar'])->findOrFail($id);

        if (!Carbon::parse($reserva->data)->isToday()) {
            return back()->with('error', 'Só pode validar reservas do dia de hoje.');
        }

        if ($reserva->estado !== 'ATIVA') {
            return back()->with('error', 'Esta reserva já não está ativa para validação.');
        }

        $reserva->update([
            'estado' => 'PRESENTE',
            'validada_por' => auth('utilizador')->id(),
        ]);

        HistoricoEventos::create([
            'utilizador_id' => $reserva->utilizador_id,
            'tipo_evento' => 'RESERVA',
            'entidade_id' => $reserva->id,
            'acao' => 'VALIDADO',
            'descricao' => 'Chegada validada pela segurança para o lugar ' . $reserva->lugar->numero,
        ]);

        return back()->with('success', 'Chegada validada com sucesso.');
    }

    public function storeReport(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:LUGAR_OCUPADO,SEM_RESERVA,PROBLEMA',
            'descricao' => 'required|string|max:2000',
            'fotos' => 'nullable|array|max:5',
            'fotos.*' => 'file|mimes:jpg,jpeg,png,webp,heic,heif|max:10240',
        ]);

        $fotos = [];
        if ($request->hasFile('fotos')) {
            foreach ((array) $request->file('fotos') as $file) {
                if ($file && $file->isValid()) {
                    $fotos[] = $file->store('reports', 'public');
                }
            }
        }

        $report = Report::create([
            'utilizador_id' => auth('utilizador')->id(),
            'tipo' => $validated['tipo'],
            'descricao' => $validated['descricao'],
            'fotos' => !empty($fotos) ? $fotos : null,
            'estado' => 'PENDENTE',
        ]);

        NotificationService::notifyAdminsAboutReport($report);

        return back()->with('success', 'Report submetido com sucesso.');
    }

    private function baseReservasHojeQuery()
    {
        return Reserva::with(['utilizador', 'lugar'])
            ->whereDate('data', today());
    }

    private function processarNaoComparecenciasAtrasadas(): void
    {
        $limite = Carbon::today()->setTime(10, 31, 0);
        if (now()->lt($limite)) {
            return;
        }

        $reservasNaoValidadas = Reserva::with(['utilizador', 'lugar'])
            ->whereDate('data', today())
            ->where('estado', 'ATIVA')
            ->whereNull('validada_por')
            ->get();

        if ($reservasNaoValidadas->isEmpty()) {
            return;
        }

        foreach ($reservasNaoValidadas as $reserva) {
            $reserva->estado = 'NAO_COMPARECEU';
            $reserva->save();

            PointsService::penalizeNoShow($reserva->utilizador, $reserva->id);

            NotificationService::notifyUser($reserva->utilizador->id, 'Não compareceu à reserva');

            $admin = Utilizador::where('role', 'ADMIN')->first();
            if ($admin) {
                NotificationService::notifyUser($admin->id, 'Reserva não compareceu');
            }

            NotificationService::notifyListaEspera($reserva->data, $reserva->lugar_id);
        }
    }
}
