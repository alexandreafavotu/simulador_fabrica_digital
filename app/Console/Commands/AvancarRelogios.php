<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Turma;
use Carbon\Carbon;

class AvancarRelogios extends Command
{
    protected $signature = 'app:avancar-relogios';
    protected $description = 'Avança o dia das turmas com relógio automático ligado';

    public function handle()
    {
        // Busca turmas onde o relógio está ligado e já passou da hora de avançar
        $turmas = Turma::where('relogio_ativo', true)
                       ->where('relogio_proximo_avanco', '<=', now())
                       ->get();

        foreach ($turmas as $turma) {
            // 1. Avança 1 dia na data do jogo
            $turma->data_jogo = Carbon::parse($turma->data_jogo)->addDay();
            
            // 2. Calcula quando será a próxima virada (agora + intervalo definido)
            $turma->relogio_proximo_avanco = now()->addMinutes($turma->relogio_intervalo);
            
            $turma->save();

            $this->info("Tempo avançado para a empresa: {$turma->nome_empresa}");
        }
    }
}