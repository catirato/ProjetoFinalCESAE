<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsSeguranca
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth('utilizador')->check()) {
            return redirect('/login');
        }

        if (auth('utilizador')->user()->role !== 'SEGURANCA') {
            abort(403, 'Apenas segurança pode aceder.');
        }

        return $next($request);
    }
}
