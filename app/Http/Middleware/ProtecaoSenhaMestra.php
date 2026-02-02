<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProtecaoSenhaMestra
{
    public function handle(Request $request, Closure $next): Response
    {
        // Pega a senha do arquivo .env (Se não tiver lá, a senha é Senai2025)
        $senhaCorreta = env('MASTER_PASSWORD', 'Senai2025');

        if ($request->input('senha_mestra') !== $senhaCorreta) {
            return redirect()->back()->with('error', '⛔ SENHA MESTRA INCORRETA! Ação cancelada por segurança.');
        }

        return $next($request);
    }
}