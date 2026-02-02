<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Aluno;

class CheckSetorAluno
{
   public function handle(Request $request, Closure $next, $setorRequerido): Response
    {
        $user = Auth::user();

        // 1. Se não estiver logado, manda para o login
        if (!$user) {
            return redirect('/login');
        }

        // 2. Se for PROFESSOR, entra em qualquer lugar (Bypass Administrativo)
        if ($user->tipo == 'professor') {
            return $next($request);
        }

        // 3. LÓGICA PADRÃO DO ALUNO (Sem invenções de sessão)
        // Busca a turma ativa
        $aluno = Aluno::with('turma')
            ->where('user_id', $user->id)
            ->whereHas('turma', function($q) {
                $q->where('jogo_ativo', true);
            })
            ->first();

        // Fallback: Se não achar ativa, pega a última
        if (!$aluno) {
            $aluno = Aluno::with('turma')->where('user_id', $user->id)->latest('id')->first();
        }

        // Se o aluno não tem turma vinculada nenhuma, bloqueia
        if (!$aluno) {
            abort(403, 'Você não está vinculado a uma turma.');
        }

        // --- VERIFICAÇÃO DE PAUSA ---
        if ($aluno->turma && !$aluno->turma->fresh()->jogo_ativo) {
            return redirect()->route('aluno.jogo.pausado');
        }

        // 4. REGRA DE SETOR
        if ($aluno->setor == 'indefinido' || $aluno->setor == $setorRequerido) {
            return $next($request);
        }

        // 5. Se chegou aqui, setor errado
        abort(403, 'ACESSO NEGADO: Você pertence ao setor de ' . strtoupper($aluno->setor) . '.');
    } 

}