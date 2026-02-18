<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Login extends Controller
{
    public function __invoke(Request $request)
    {
        // Validar os dados do formulário
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Tentar autenticar usando o guard "utilizador"
        if (Auth::guard('utilizador')->attempt($credentials, $request->boolean('remember'))) {
            
            // Regenerar sessão por segurança
            $request->session()->regenerate();

            // Redirecionar para dashboard (ou intended)
            return redirect()->intended('/dashboard')
                ->with('success', 'Sessão iniciada com sucesso.');
        }

        // Se falhar
        return back()
            ->withErrors([
                'email' => 'Credenciais inválidas.',
            ])
            ->onlyInput('email');
    }
}
