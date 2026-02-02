<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckProfessor
{
    public function handle(Request $request, Closure $next): Response
    {
        // Se não for professor, bloqueia!
        if (!Auth::check() || Auth::user()->tipo !== 'professor') {
            abort(403, 'Acesso restrito ao Professor (Game Master).');
        }

        return $next($request);
    }
}