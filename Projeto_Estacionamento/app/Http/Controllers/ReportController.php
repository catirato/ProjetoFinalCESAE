<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Utilizador;
use App\Models\Lugar;
use App\Services\PointsService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    //️Submeter report (feito pelo colaborador)
    public function store(Request $request)
    {
        $report = Report::create([
            'utilizador_id' => $request->utilizador_id, // quem faz o report
            'tipo' => $request->tipo,                  // ex: 'LUGAR_OCUPADO'
            'descricao' => $request->descricao,
            'estado' => 'PENDENTE'
        ]);

        return response()->json($report);
    }

    //  Listar reports pendentes (ADMIN analisa)
    public function pendentes()
    {
        return Report::where('estado', 'PENDENTE')->get();
    }

    //  Validar report (feito pelo ADMIN)
    public function validar(Request $request, $id)
    {
        $report = Report::find($id);
        if (!$report) {
            return response()->json(['error' => 'Report não encontrado'], 404);
        }

        $report->estado = 'VALIDADO';
        $report->save();

        // Aplica ações dependendo do tipo de report
        switch ($report->tipo) {
            case 'LUGAR_OCUPADO':
            case 'VEICULO_SEM_RESERVA':
            case 'ACIDENTE_ENTRE_VIATURAS':
                // Apenas regista a intenção de penalizar, o admin aplica os pontos depois
                $usuario = Utilizador::find($request->utilizador_afetado_id);
                if ($usuario) {
                    NotificationService::notifyUser(
                        $usuario->id,
                        "Report validado: {$report->descricao}. Penalização será aplicada pelo ADMIN."
                    );
                }
                break;

            case 'PROBLEMAS_NO_ESTACIONAMENTO':
                break;

            case 'TORNAR_LUGAR_INDISPONIVEL':
                $lugar = Lugar::find($request->lugar_id);
                if ($lugar) {
                    $lugar->ativo = false;
                    $lugar->save();
                }
                break;
        }

        return response()->json($report);
    }

    //  Rejeitar report
    public function rejeitar($id)
    {
        $report = Report::find($id);
        if (!$report) {
            return response()->json(['error' => 'Report não encontrado'], 404);
        }

        $report->estado = 'REJEITADO';
        $report->save();

        return response()->json($report);
    }
}
