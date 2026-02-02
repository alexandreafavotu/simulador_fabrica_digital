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
        // 1. Criar a Tabela de Locais (O Mapa WMS)
        Schema::create('locais_estoque', function (Blueprint $table) {
            $table->id();
            // Vincula à turma para separar estoques de empresas diferentes
            $table->foreignId('turma_id')->constrained('turmas')->onDelete('cascade');
            
            // Coordenadas para o Mapa Visual
            $table->string('codigo_visual'); // Ex: R01-C05-V02-N03
            $table->integer('rua');
            $table->integer('coluna');
            $table->integer('vao');   // <--- O Vão que você solicitou
            $table->integer('nivel');
            
            // Status visual (Verde/Vermelho)
            $table->boolean('ocupado')->default(false);
            
            $table->timestamps();
        });

        // 2. Atualizar Matéria-Prima (Adicionar Saldo e Endereço)
        Schema::table('materias_primas', function (Blueprint $table) {
            // Se a coluna não existir, cria (segurança extra)
            if (!Schema::hasColumn('materias_primas', 'quantidade_estoque')) {
                $table->decimal('quantidade_estoque', 10, 2)->default(0.00)->after('unidade_medida');
            }
            if (!Schema::hasColumn('materias_primas', 'local_estoque_id')) {
                $table->foreignId('local_estoque_id')->nullable()->after('quantidade_estoque')->constrained('locais_estoque')->nullOnDelete();
            }
        });

        // 3. Atualizar Produto Acabado (Adicionar Saldo e Endereço)
        Schema::table('produtos_acabados', function (Blueprint $table) {
            if (!Schema::hasColumn('produtos_acabados', 'quantidade_estoque')) {
                $table->integer('quantidade_estoque')->default(0)->after('tempo_producao_dias');
            }
            if (!Schema::hasColumn('produtos_acabados', 'local_estoque_id')) {
                $table->foreignId('local_estoque_id')->nullable()->after('quantidade_estoque')->constrained('locais_estoque')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('almoxarifado_structure_tables');
    }
};
