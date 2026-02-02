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
    Schema::table('ordens_producao', function (Blueprint $table) {
        // Indica se o professor forçou um erro nesta máquina
        $table->boolean('tem_refugo_forcado')->default(false);
        // Quantidade que o aluno SERÁ OBRIGADO a marcar como perda
        $table->integer('qtd_refugo_forcado')->default(0);
        // O motivo técnico (ex: Falha no molde, Superaquecimento)
        $table->string('motivo_refugo_forcado')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ordens_producao', function (Blueprint $table) {
            //
        });
    }
};
