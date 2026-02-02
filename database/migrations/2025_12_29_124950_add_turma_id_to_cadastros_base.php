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
        // 1. CORREÇÃO DE MATÉRIAS-PRIMAS
        Schema::table('materias_primas', function (Blueprint $table) {
            // Tenta remover a trava de SKU único (se der erro, ignoramos com try/catch depois, mas aqui tentamos direto)
            // Em MySQL, dropIndex costuma não falhar se for feito separado, mas vamos focar na coluna primeiro.
        });
        
        // Separamos em blocos para garantir
        if (!Schema::hasColumn('materias_primas', 'turma_id')) {
            Schema::table('materias_primas', function (Blueprint $table) {
                $table->foreignId('turma_id')->nullable()->after('id')->constrained('turmas')->onDelete('cascade');
            });
        }
        
        // Tenta remover unique do SKU (Colocamos em um bloco try-catch genérico via PHP puro se fosse necessário, mas o Schema do Laravel geralmente lida bem)
        try {
            Schema::table('materias_primas', function (Blueprint $table) {
                $table->dropUnique(['sku']); 
            });
        } catch (\Exception $e) { /* Índice já não existe, segue o jogo */ }


        // 2. CORREÇÃO DE PRODUTOS ACABADOS
        if (!Schema::hasColumn('produtos_acabados', 'turma_id')) {
            Schema::table('produtos_acabados', function (Blueprint $table) {
                $table->foreignId('turma_id')->nullable()->after('id')->constrained('turmas')->onDelete('cascade');
            });
        }

        try {
            Schema::table('produtos_acabados', function (Blueprint $table) {
                $table->dropUnique(['sku']);
            });
        } catch (\Exception $e) { /* Índice já não existe */ }


        // 3. CORREÇÃO DE CLIENTES (Onde deu o erro antes)
        if (!Schema::hasColumn('clientes', 'turma_id')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->foreignId('turma_id')->nullable()->after('id')->constrained('turmas')->onDelete('cascade');
            });
        }

        try {
            Schema::table('clientes', function (Blueprint $table) {
                $table->dropUnique(['cnpj_cpf']);
            });
        } catch (\Exception $e) { /* Índice já não existe */ }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não vamos reverter estrutura crítica
    }
};