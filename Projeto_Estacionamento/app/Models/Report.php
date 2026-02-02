<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Utilizador;
use App\Services\PointsService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // 1️⃣ Submeter report (feito pelo colaborador)
    public function store(Request $request)
    {
        $report = Report::create([
            'utilizador_id' => $request->utilizador_id, // quem faz o report
            'tipo' => $request->tipo, // ex: 'LUGAR_OCUPADO'
            'descricao' => $request->descricao,
            'estado' => 'PENDENTE'
        ]);

        return response()->json($report);
    }

    // 2️⃣ Listar reports pendentes (para ADMIN analisar)
    public function pendentes()
    {
        return Report::where('estado','PENDENTE')->get();
    }

    // 3️⃣ Validar report (feito pelo ADMIN)
    public function validar(Request $request, $id)
    {
        $report = Report::find($id);
        if (!$report) return response()->json(['error'=>'Report não encontrado'],404);

        $report->estado = 'VALIDADO';
        $report->save();

        // Aplicar penalização dependendo do tipo de report
        $usuario = Utilizador::find($request->utilizador_afetado_id); // o colaborador envolvido no report

        if ($report->tipo === 'LUGAR_OCUPADO' || $report->tipo === 'VEICULO_SEM_RESERVA') {
            // Penalização: retira 5 pontos (exemplo)
            PointsService::addPoints($usuario, -5);
        } elseif ($report->tipo === 'ACIDENTE_ENTRE_VIATURAS') {
            PointsService::addPoints($usuario, -10);
        }

        // Notificar utilizador envolvido
        NotificationService::notifyUser($usuario->id, "Report validado: {$report->descricao}");

        return response()->json($report);
    }

    // 4️⃣ Rejeitar report
    public function rejeitar($id)
    {
        $report = Report::find($id);
        if (!$report) return response()->json(['error'=>'Report não encontrado'],404);

        $report->estado = 'REJEITADO';
        $report->save();

        return response()->json($report);
    }
}
