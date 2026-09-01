<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Adiciona o autor da ação na nota fiscal (Quem faturou na Expedição)
        Schema::table('notas_fiscais', function (Blueprint $table) {
            $table->foreignId('aluno_id')->nullable()->after('pedido_venda_id')->constrained('users')->onDelete('set null');
        });

        // Adiciona o autor da ação no recebimento (Quem recebeu em Compras/WMS)
        Schema::table('ordens_compra', function (Blueprint $table) {
            $table->foreignId('aluno_id')->nullable()->after('fornecedor_id')->constrained('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('ordens_compra', function (Blueprint $table) {
            $table->dropForeign(['aluno_id']);
            $table->dropColumn('aluno_id');
        });

        Schema::table('notas_fiscais', function (Blueprint $table) {
            $table->dropForeign(['aluno_id']);
            $table->dropColumn('aluno_id');
        });
    }
};