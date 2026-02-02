<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ConfiguracaoSimulacaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Garante que o modelo está sendo usado e limpa a tabela
        \App\Models\ConfiguracaoSimulacao::truncate(); 

        $configuracoes = [
            ['chave' => 'aluno_cadastra_cliente', 'valor' => 0],
            ['chave' => 'aluno_cadastra_fornecedor', 'valor' => 0],
            ['chave' => 'aluno_cadastra_materia_prima', 'valor' => 0],
        ];

        foreach ($configuracoes as $config) {
            \App\Models\ConfiguracaoSimulacao::create($config);
        }
    }
}