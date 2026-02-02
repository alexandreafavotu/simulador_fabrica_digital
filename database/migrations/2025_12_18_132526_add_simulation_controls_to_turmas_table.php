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
        Schema::table('turmas', function (Blueprint $table) {
            // Relógio individual da turma (Data e Hora)
            // Default: Data de hoje real
            $table->dateTime('data_jogo')->default(now());

            // Status do Jogo para esta turma
            // 0 = Pausado/Não Iniciado (Aluno só vê, não edita)
            // 1 = Rodando (Aluno joga)
            $table->boolean('jogo_ativo')->default(0); 
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('turmas', function (Blueprint $table) {
            //
        });
    }
};
