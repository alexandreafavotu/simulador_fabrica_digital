<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Turma;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CheckRelogio
{
    public function handle($request, Closure $next)
    {
        // Se houver alguém logado, o sistema verifica o relógio
        if (Auth::check()) {
            $turmasAtivas = Turma::where('relogio_ativo', true)
                                 ->where('relogio_proximo_avanco', '<=', now())
                                 ->get();

            foreach ($turmasAtivas as $turma) {
                // Avança 1 dia na data do jogo
                $turma->data_jogo = Carbon::parse($turma->data_jogo)->addDay();
                
                // Define a próxima data de avanço (agora + o intervalo definido pelo professor)
                $turma->relogio_proximo_avanco = now()->addMinutes((int)$turma->relogio_intervalo);
                
                $turma->save();
            }
        }

        return $next($request);
    }
}