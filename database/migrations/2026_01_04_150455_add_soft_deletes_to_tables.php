<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona a coluna 'deleted_at' para permitir a "Lixeira" (Soft Delete).
     */
    public function up(): void
    {
        $tabelas = [
            'clientes',
            'fornecedores',
            'materias_primas',
            'produtos_acabados',
            'pedidos_venda',
            'pedido_venda_itens'
        ];

        foreach ($tabelas as $tabela) {
            Schema::table($tabela, function (Blueprint $table) {
                $table->softDeletes(); // Cria a coluna deleted_at
            });
        }
    }

    /**
     * Remove a coluna caso precise desfazer.
     */
    public function down(): void
    {
        $tabelas = [
            'clientes',
            'fornecedores',
            'materias_primas',
            'produtos_acabados',
            'pedidos_venda',
            'pedido_venda_itens'
        ];

        foreach ($tabelas as $tabela) {
            Schema::table($tabela, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};