<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona a coluna professor_id na tabela turmas.
     */
    public function up(): void
    {
        Schema::table('turmas', function (Blueprint $table) {
            // Cria a coluna 'professor_id' logo após a coluna 'id'
            // É 'nullable' para não quebrar turmas antigas
            // Se um professor for deletado, a turma também será (cascade)
            $table->foreignId('professor_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('users')
                  ->onDelete('cascade'); 
        });
    }

    /**
     * Remove a coluna caso precisemos desfazer (Rollback).
     */
    public function down(): void
    {
        Schema::table('turmas', function (Blueprint $table) {
            $table->dropForeign(['professor_id']);
            $table->dropColumn('professor_id');
        });
    }
};