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
        // 1. Tabela de Matrizes Pedagógicas (Rica em informações e alinhada ao plano de curso)
        Schema::create('competencias_cursos', function (Blueprint $table) {
            $table->id();
            $table->string('curso'); // ex: 'Auxiliar de Linha de Produção', 'Almoxarife', 'Técnico em Móveis'
            $table->string('unidade_curricular')->nullable(); // ex: 'Abastecimento da Produção', 'Técnicas de Armazenagem'
            $table->enum('categoria', ['tecnica', 'organizacional', 'socioemocional', 'conhecimento']);
            $table->string('nome'); // O nome da capacidade (ex: 'Registrar o abastecimento das máquinas')
            $table->text('descricao'); // O detalhamento fático de como isso se aplica no ERP
            $table->enum('tipo_avaliacao', ['automatica', 'manual'])->default('manual');
            
            // Define qual métrica interna do banco de dados a IA ou o sistema lerá para calcular a nota automática
            $table->string('metrica_chave')->nullable(); 
            
            $table->timestamps();
        });

        // 2. Tabela de Boletim / Avaliações dos Alunos
        Schema::create('aluno_avaliacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Aluno avaliado
            $table->foreignId('competencia_id')->constrained('competencias_cursos')->onDelete('cascade');
            
            // 'pendente' (não avaliado), 'conforme' (atingiu), 'nao_conforme' (não atingiu)
            $table->enum('status', ['pendente', 'conforme', 'nao_conforme'])->default('pendente'); 
            $table->text('evidencia_automatica')->nullable(); // Registro gerado pelo sistema (ex: "Faturou 15 pizzas, restam 0")
            $table->text('observacoes_professor')->nullable(); // Anotações manuais do Game Master
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aluno_avaliacoes');
        Schema::dropIfExists('competencias_cursos');
    }
};