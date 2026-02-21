<?php

namespace App\Http\Controllers;

use App\Models\HistoricoEventos;
use App\Models\Report;
use App\Models\Reserva;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SegurancaController extends Controller
{
    public function index()
    {
        $reservasHoje = $this->baseReservasHojeQuery()
            ->whereIn('estado', ['ATIVA', 'PRESENTE'])
            ->orderBy('estado')
            ->orderBy('lugar_id')
            ->get();

        return view('seguranca.index', compact('reservasHoje'));
    }

    public function pendentes()
    {
        $reservasHoje = $this->baseReservasHojeQuery()
            ->where('estado', 'ATIVA')
            ->orderBy('lugar_id')
            ->get();

        return view('seguranca.pendentes', compact('reservasHoje'));
    }

    public function validadas()
    {
        $reservasHoje = $this->baseReservasHojeQuery()
            ->where('estado', 'PRESENTE')
            ->orderBy('lugar_id')
            ->get();

        return view('seguranca.validadas', compact('reservasHoje'));
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
        ]);

        $report = Report::create([
            'utilizador_id' => auth('utilizador')->id(),
            'tipo' => $validated['tipo'],
            'descricao' => $validated['descricao'],
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
}
