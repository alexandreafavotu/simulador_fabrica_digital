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
        // 1. Atualizar a tabela de Ordens de Produção (Adicionar controles)
        Schema::table('ordens_producao', function (Blueprint $table) {
            // Quem assumiu essa ordem? (Link com o Aluno)
            $table->foreignId('aluno_id')->nullable()->after('id')->constrained('alunos');

            // Status do Material (Trava do processo)
            // Valores: 'Pendente', 'Solicitado', 'Separacao', 'Entregue'
            $table->string('status_material')->default('Pendente')->after('status');
            
            // Controle de Tempo Real (Quando o aluno clicou em INICIAR)
            $table->dateTime('data_inicio_real')->nullable()->after('data_inicio');
            
            // Registro de Perdas/Refugo (Qualidade)
            $table->integer('quantidade_perda')->default(0)->after('quantidade');
        });

        // 2. Criar tabela de Histórico/Apontamentos (Para os Relatórios)
        Schema::create('apontamentos_producao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ordem_producao_id')->constrained('ordens_producao')->onDelete('cascade');
            $table->foreignId('aluno_id')->constrained('alunos'); // Quem fez
            
            $table->date('data_apontamento'); // Data do Relógio do Jogo
            $table->integer('quantidade_produzida');
            $table->integer('quantidade_refugo');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producao_structure_tables');
    }
};
