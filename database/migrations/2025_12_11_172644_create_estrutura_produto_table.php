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
    Schema::create('estrutura_produto', function (Blueprint $table) {
        $table->id();
        $table->foreignId('produto_acabado_id')->constrained('produtos_acabados');
        $table->foreignId('materia_prima_id')->constrained('materias_primas');
        $table->decimal('quantidade', 8, 2);
        $table->timestamps();
    });
}
   

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estrutura_produto');
    }
};
