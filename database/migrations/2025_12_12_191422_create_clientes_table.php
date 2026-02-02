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
    Schema::create('clientes', function (Blueprint $table) {
        $table->id();
        $table->string('nome_razao_social');
        $table->string('cnpj_cpf')->nullable()->unique();
        $table->string('telefone')->nullable();
        $table->string('email')->nullable();
        $table->string('rua')->nullable();
        $table->string('numero')->nullable();
        $table->string('bairro')->nullable();
        $table->string('cidade')->nullable();
        $table->string('estado', 2)->nullable(); // Limita a 2 caracteres (ex: SP)
        $table->string('cep', 9)->nullable();    // Limita a 9 caracteres (ex: 12345-678)
        $table->timestamps();
    });
}
  

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
