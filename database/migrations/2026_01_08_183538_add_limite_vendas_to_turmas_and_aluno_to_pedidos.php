<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    // 1. Adiciona o limite na tabela da EMPRESA
    Schema::table('turmas', function (Blueprint $table) {
        $table->integer('limite_vendas_por_aluno')->default(10)->after('capacidade_producao'); // Padrão: 10 pedidos/dia
    });

    // 2. Adiciona a assinatura do aluno no PEDIDO
    Schema::table('pedidos_venda', function (Blueprint $table) {
        $table->unsignedBigInteger('aluno_id')->nullable()->after('turma_id');
        // Não vamos por foreign key restrita agora para não travar dados antigos, apenas o campo.
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            //
        });
    }
};
