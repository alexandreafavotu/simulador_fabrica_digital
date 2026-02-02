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
        Schema::create('ordens_compra', function (Blueprint $table) {
            $table->id();
            // Liga a compra ao Pedido de Venda original (para sabermos o motivo da compra)
            $table->foreignId('pedido_venda_id')->constrained('pedidos_venda'); 
            
            // Qual material comprar
            $table->foreignId('materia_prima_id')->constrained('materias_primas'); 
            
            // Quantidade (usamos decimal porque pode ser 1.5 Litros, por exemplo)
            $table->decimal('quantidade', 10, 2); 
            
            // Status do processo de compra
            $table->string('status')->default('Pendente'); // Pendente -> Aprovada -> Recebida
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordens_compra');
    }
};
