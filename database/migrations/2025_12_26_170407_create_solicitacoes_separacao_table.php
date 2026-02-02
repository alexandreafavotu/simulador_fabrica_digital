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
        Schema::create('solicitacoes_separacao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ordem_producao_id')->constrained('ordens_producao')->onDelete('cascade');
            $table->foreignId('materia_prima_id')->constrained('materias_primas')->onDelete('cascade');
            $table->decimal('quantidade_solicitada', 10, 2); // Quanto a Produção precisa
            $table->foreignId('aluno_solicitante_id')->constrained('alunos'); // Quem pediu (o aluno da produção)
            $table->string('status')->default('Pendente'); // Pendente, Separado, Entregue
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitacoes_separacao');
    }
};
