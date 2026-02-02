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
        Schema::create('notas_fiscais', function (Blueprint $table) {
            $table->id();
            
            // Liga a nota ao pedido original
            $table->foreignId('pedido_venda_id')->constrained('pedidos_venda')->onDelete('cascade');
            
            // Dados Fiscais (Simulados)
            $table->string('numero_nota'); // Ex: 000543
            $table->string('serie')->default('1');
            $table->decimal('valor_total', 12, 2);
            $table->date('data_emissao'); // Data do relógio do jogo
            
            // Chave de acesso fictícia (para parecer real no PDF depois)
            $table->string('chave_acesso')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notas_fiscais');
    }
};
