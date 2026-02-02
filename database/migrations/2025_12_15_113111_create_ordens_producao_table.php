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
        Schema::create('ordens_producao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_venda_id')->constrained('pedidos_venda'); // Liga ao pedido original
            $table->foreignId('produto_acabado_id')->constrained('produtos_acabados'); // O que será feito
            $table->integer('quantidade'); // Quantas unidades
            $table->string('status')->default('Aberta'); // Aberta, Em Produção, Concluída
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordens_producao');
    }
};
