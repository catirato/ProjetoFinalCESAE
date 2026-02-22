<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utilizador;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Throwable;

class Register extends Controller
{
    public function __invoke(Request $request)
    {
        $admin = Auth::guard('utilizador')->user();
        if (!$admin || $admin->role !== 'ADMIN') {
            abort(403, 'Apenas administradores podem registar novos utilizadores.');
        }

        // Validar dados de registo
        $validated = $request->validate([
            'nome' => 'required|string|max:100',
            'email' => ['required', 'string', 'email', 'max:150', Rule::unique('utilizador', 'email')],
            'role' => ['required', 'in:COLAB,SEGURANCA,ADMIN'],
        ]);

        $passwordTemporaria = Str::password(18, true, true, true, false);

        // Criar utilizador com defaults do sistema
        $user = Utilizador::create([
            'nome' => $validated['nome'],
            'email' => $validated['email'],
            'password' => $passwordTemporaria,
            'obrigar_mudar_password' => true,
            'role' => $validated['role'],
        ]);

        try {
            $token = Password::broker('utilizadores')->createToken($user);
            $linkPrimeiroAcesso = route('first-access.form', [
                'token' => $token,
                'email' => $user->email,
            ]);

            $mensagem = "Olá {$user->nome},\n\n";
            $mensagem .= "A sua conta no sistema de estacionamento foi criada.\n";
            $mensagem .= "Para definir a sua password no primeiro acesso, utilize este link:\n";
            $mensagem .= "{$linkPrimeiroAcesso}\n\n";
            $mensagem .= "Este link expira em 60 minutos.\n";

            Mail::raw($mensagem, function ($mail) use ($user) {
                $mail->to($user->email, $user->nome)
                    ->subject('Defina a sua password - Primeiro acesso');
            });

            return redirect('/register')
                ->with('success', "Utilizador {$user->nome} criado e email de primeiro acesso enviado.");
        } catch (Throwable $e) {
            Log::error('Falha ao enviar email de primeiro acesso', [
                'utilizador_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);

            return redirect('/register')->with('warning',
                "Utilizador {$user->nome} criado, mas não foi possível enviar o email de primeiro acesso."
            );
        }
    }
}
