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
            // Colunas de Bloqueio Global (Guardam a data/hora do jogo em que o bloqueio acaba)
            // Se estiver NULL, não tem bloqueio.
            
            $table->dateTime('bloqueio_almoxarifado_ate')->nullable(); // Inventário
            $table->dateTime('bloqueio_expedicao_ate')->nullable();    // Greve/Falta de Transporte
            $table->dateTime('bloqueio_faturamento_ate')->nullable();  // Queda de Sistema (TI)
            
            // Coluna para a mensagem que vai aparecer na tela do aluno
            // Ex: "URGENTE: O sistema caiu! Previsão de retorno em 2 dias."
            $table->text('mensagem_plantao_caos')->nullable();
        });

        // Para os incidentes pontuais (Máquina e Atraso), vamos alterar as tabelas específicas
        
        // 1. Na Produção: Adicionar flag de manutenção
        if (!Schema::hasColumn('ordens_producao', 'em_manutencao')) {
            Schema::table('ordens_producao', function (Blueprint $table) {
                $table->boolean('em_manutencao')->default(false);
                $table->text('motivo_manutencao')->nullable();
            });
        }
        
        // 2. Nas Vendas: Adicionar motivo de cancelamento
        if (!Schema::hasColumn('pedidos_venda', 'motivo_cancelamento')) {
            Schema::table('pedidos_venda', function (Blueprint $table) {
                $table->text('motivo_cancelamento')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('turmas', function (Blueprint $table) {
            $table->dropColumn([
                'bloqueio_almoxarifado_ate', 
                'bloqueio_expedicao_ate', 
                'bloqueio_faturamento_ate',
                'mensagem_plantao_caos'
            ]);
        });
        
        Schema::table('ordens_producao', function (Blueprint $table) {
            $table->dropColumn(['em_manutencao', 'motivo_manutencao']);
        });

        Schema::table('pedidos_venda', function (Blueprint $table) {
            $table->dropColumn('motivo_cancelamento');
        });
    }
};