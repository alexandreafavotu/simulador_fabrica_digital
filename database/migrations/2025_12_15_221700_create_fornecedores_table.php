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
        Schema::create('fornecedores', function (Blueprint $table) {
            $table->id();
            
            // Vínculo com a Turma (Empresa do Aluno)
            $table->foreignId('turma_id')->nullable()->constrained('turmas')->onDelete('cascade');

            // Especialidade: Qual material ele vende?
            $table->foreignId('materia_prima_id')->nullable()->constrained('materias_primas')->nullOnDelete();
            
            // Negociação
            $table->decimal('preco_unitario', 10, 2)->default(0.00); // Preço
            $table->integer('tempo_entrega_dias')->default(1);       // Lead Time (Novo!)

            // Dados Cadastrais
            $table->string('nome_razao_social'); 
            $table->string('cnpj_cpf')->nullable();
            $table->string('telefone')->nullable();
            $table->string('email')->nullable();
            
            // Endereço
            $table->string('cep')->nullable();
            $table->string('rua')->nullable();
            $table->string('numero')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fornecedores');
    }
};
