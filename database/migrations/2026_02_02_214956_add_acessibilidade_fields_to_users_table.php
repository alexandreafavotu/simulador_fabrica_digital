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
        Schema::table('users', function (Blueprint $table) {
            // 1. Acessibilidade Visual (Alto Contraste / Ícones de Reforço)
            $table->boolean('acessibilidade_visual')->default(false)->after('password');
            
            // 2. Acessibilidade Motora (Navegação Teclado / Botões Maiores)
            $table->boolean('acessibilidade_motora')->default(false)->after('acessibilidade_visual');
            
            // 3. Acessibilidade Cognitiva (Linguagem Simples / Guias / Botão Pulsante)
            // OBS: O Módulo Caos continua ATIVO para este aluno.
            $table->boolean('acessibilidade_cognitiva')->default(false)->after('acessibilidade_motora');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'acessibilidade_visual',
                'acessibilidade_motora',
                'acessibilidade_cognitiva'
            ]);
        });
    }
};