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
        Schema::create('sistema_ajuda', function (Blueprint $table) {
            $table->id();
            
            // A chave mágica: Qual tela é essa? (Ex: 'aluno.pcp.dashboard')
            $table->string('rota')->unique(); 
            
            // O conteúdo do manual
            $table->string('titulo')->nullable();
            $table->longText('conteudo')->nullable(); // Aceita textos longos e HTML básico
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sistema_ajuda');
    }
};