<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Turma;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CheckRelogio
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Trava de execução global em cache por 5 segundos para evitar concorrência simultânea
            Cache::lock('processamento_relogio_turmas', 5)->get(function () {
                $now = now();

                // Busca turmas pendentes de avanço
                $turmasPendentes = Turma::where('arquivada', false)
                    ->where('jogo_ativo', true)
                    ->where('relogio_ativo', 1)
                    ->where('relogio_proximo_avanco', '<=', $now)
                    ->get();

                foreach ($turmasPendentes as $item) {
                    DB::transaction(function () use ($item) {
                        // Trava no nível de linha do MySQL
                        $turma = Turma::where('id', $item->id)->lockForUpdate()->first();

                        if ($turma && !$turma->arquivada && $turma->jogo_ativo && $turma->relogio_ativo && $turma->relogio_proximo_avanco <= now()) {
                            // Avança 1 dia na data de jogo
                            $turma->data_jogo = \Carbon\Carbon::parse($turma->data_jogo)->addDays(1);

                            // Define o próximo avanço
                            $intervalo = (int) ($turma->relogio_intervalo ?: 5);
                            $turma->relogio_proximo_avanco = now()->addMinutes($intervalo);

                            $turma->save();
                        }
                    });
                }
            });
        } catch (\Exception $e) {
            logger()->error('Erro no processamento do CheckRelogio: ' . $e->getMessage());
        }

        return $next($request);
    }
}