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
        Schema::table('ordens_compra', function (Blueprint $table) {
            // Adiciona a coluna fornecedor_id, podendo ser nula (pois nasce sem fornecedor)
            $table->foreignId('fornecedor_id')->nullable()->after('materia_prima_id')->constrained('fornecedores');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ordens_compra', function (Blueprint $table) {
            //
        });
    }
};
