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
    Schema::create('pedido_venda_itens', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pedido_venda_id')->constrained('pedidos_venda');
        $table->foreignId('produto_acabado_id')->constrained('produtos_acabados');
        $table->integer('quantidade');
        $table->decimal('preco_unitario', 8, 2);
        $table->decimal('preco_total_item', 10, 2);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido_venda_items');
    }
};
