<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona colunas apenas se elas não existirem.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Verifica se a coluna 'tipo' NÃO existe antes de criar
            if (!Schema::hasColumn('users', 'tipo')) {
                $table->string('tipo')->default('aluno')->after('email');
            }

            // Verifica 'curso'
            if (!Schema::hasColumn('users', 'curso')) {
                $table->string('curso')->nullable()->after('tipo');
            }

            // Verifica 'ano_letivo'
            if (!Schema::hasColumn('users', 'ano_letivo')) {
                $table->string('ano_letivo')->nullable()->after('curso');
            }

            // Verifica 'rg_finais'
            if (!Schema::hasColumn('users', 'rg_finais')) {
                $table->string('rg_finais', 3)->nullable()->after('ano_letivo');
            }
        });
    }

    /**
     * Remove as colunas apenas se elas existirem.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = ['tipo', 'curso', 'ano_letivo', 'rg_finais'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};