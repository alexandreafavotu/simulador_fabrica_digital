<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabela Pai: Cabeçalho da Solicitação da IA
        Schema::create('demandas_mercado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('turma_id')->constrained('turmas')->onDelete('cascade');
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->date('data_entrega_solicitada');
            // Pendente: Aguardando aluno digitar | Atendido: Sucesso
            $table->string('status')->default('Pendente'); 
            $table->timestamps();
        });

        // 2. Tabela Filho: Itens da Solicitação (Múltiplos produtos por pedido)
        Schema::create('demanda_mercado_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demanda_mercado_id')->constrained('demandas_mercado')->onDelete('cascade');
            $table->foreignId('produto_acabado_id')->constrained('produtos_acabados')->onDelete('cascade');
            $table->integer('quantidade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demanda_mercado_itens');
        Schema::dropIfExists('demandas_mercado');
    }
};