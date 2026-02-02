<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    public function handle(Request $request, Closure $next): Response
    {
        // Se o usuário está logado E a coluna 'ativo' for falsa (0)
        if (Auth::check() && !Auth::user()->ativo) {
            
            // Faz logout forçado
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Manda de volta pro login com aviso
            return redirect()->route('login')->withErrors([
                'email' => 'ACESSO BLOQUEADO: Seu usuário foi inativado pelo administrador.',
            ]);
        }

        return $next($request);
    }
}