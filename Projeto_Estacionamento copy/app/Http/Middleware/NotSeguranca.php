<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotSeguranca
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth('utilizador')->check()) {
            return redirect('/login');
        }

        if (auth('utilizador')->user()->role === 'SEGURANCA') {
            abort(403, 'Esta área não está disponível para segurança.');
        }

        return $next($request);
    }
}
