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
    Schema::create('produtos_acabados', function (Blueprint $table) {
        $table->id();
        $table->string('sku')->unique();
        $table->string('nome');
        $table->integer('tempo_producao_dias')->default(1);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produto_acabados');
    }
};
