<?php

namespace App\Services;

use App\Models\ListaEspera;
use App\Models\Report;
use App\Models\Utilizador;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public static function notifyListaEspera(string $data, ?int $lugarId = null): void
    {
        $dataCarbon = Carbon::parse($data);
        $agora = now();

        $entradas = ListaEspera::with('utilizador')
            ->where('data', $dataCarbon->toDateString())
            ->where('estado', 'ATIVO')
            ->orderBy('prioridade')
            ->get();

        foreach ($entradas as $entrada) {
            $user = $entrada->utilizador;

            if (!$user || $user->role === 'SEGURANCA' || $user->pontos < 3) {
                continue;
            }

            $expiraEm = $dataCarbon->isToday()
                ? $dataCarbon->copy()->setTime(10, 0)
                : null;

            // Se a vaga for para hoje e já passou das 10h, não permite confirmação.
            if ($expiraEm && $expiraEm->lte($agora)) {
                $entrada->update([
                    'estado' => 'EXPIRADO',
                    'notification_token' => null,
                    'notificado_em' => $agora,
                    'expira_em' => $expiraEm,
                ]);
                continue;
            }

            $token = bin2hex(random_bytes(20));
            $entrada->update([
                'estado' => 'NOTIFICADO',
                'notification_token' => $token,
                'notificado_em' => $agora,
                'expira_em' => $expiraEm,
            ]);

            $urlConfirmacao = url('/lista-espera/' . $entrada->id . '/confirmar/' . $token);
            $dataFormatada = $dataCarbon->format('d/m/Y');
            $lugarTexto = $lugarId ? "Lugar {$lugarId}" : 'Um lugar';

            $mensagem = "{$lugarTexto} ficou disponível para {$dataFormatada}. "
                . "Para confirmar a reserva, aceda a: {$urlConfirmacao} . "
                . "Fica com a vaga quem confirmar primeiro.";

            if ($expiraEm) {
                $deadline = $expiraEm->format('H:i');
                $mensagem .= " Para vagas de hoje, o prazo de confirmação é até às {$deadline}.";
            }

            try {
                Mail::raw($mensagem, function ($mail) use ($user, $dataFormatada) {
                    $mail->to($user->email, $user->nome)
                        ->subject("Vaga disponível para {$dataFormatada}");
                });
            } catch (\Throwable $e) {
                Log::error('Falha no envio de email da lista de espera', [
                    'lista_espera_id' => $entrada->id,
                    'utilizador_id' => $user->id,
                    'erro' => $e->getMessage(),
                ]);
            }
        }
    }

    public static function notifyUser($userId, $mensagem): void
    {
        $user = Utilizador::find($userId);
        if (!$user) {
            return;
        }
        Log::info("Notificação a {$user->nome}: $mensagem");
    }

    public static function notifyAdminsAboutReport(Report $report): void
    {
        $report->loadMissing('utilizador');

        $admins = Utilizador::query()
            ->where('role', 'ADMIN')
            ->whereNotNull('email')
            ->get();

        if ($admins->isEmpty()) {
            Log::warning('Nenhum admin com email para notificar sobre relatório', [
                'report_id' => $report->id,
            ]);
            return;
        }

        $tipo = str_replace('_', ' ', (string) $report->tipo);
        $descricao = (string) $report->descricao;
        $autor = $report->utilizador?->nome ?? 'Utilizador desconhecido';
        $data = optional($report->created_at)->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i');
        $link = url('/admin/relatorios');

        $mensagem = "Novo relatório submetido no sistema.\n"
            . "Tipo: {$tipo}\n"
            . "Autor: {$autor}\n"
            . "Data: {$data}\n"
            . "Descrição: {$descricao}\n"
            . "Consultar: {$link}";

        $assunto = (string) ($report->tipo ?? 'Novo relatório');

        Log::info('A enviar email de relatório para admins', [
            'report_id' => $report->id,
            'admins_count' => $admins->count(),
        ]);

        foreach ($admins as $admin) {
            if (empty($admin->email)) {
                Log::warning('Admin sem email no envio de relatório', [
                    'report_id' => $report->id,
                    'admin_id' => $admin->id,
                ]);
                continue;
            }

            try {
                Mail::mailer('smtp')->raw($mensagem, function ($mail) use ($admin, $assunto) {
                    $mail->to($admin->email, $admin->nome)
                        ->subject($assunto);
                });
            } catch (\Throwable $e) {
                Log::error('Falha no envio de email do relatório para admin', [
                    'report_id' => $report->id,
                    'admin_id' => $admin->id,
                    'admin_email' => $admin->email,
                    'erro' => $e->getMessage(),
                ]);
            }
        }
    }
}


// lista de espera e notificações
