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
        // Define se o professor sabotou esta carga
        $table->boolean('tem_inconformidade')->default(false);
        // O motivo que o aluno verá (ex: Embalagem Molhada, Erro de Lote)
        $table->string('descricao_inconformidade')->nullable();
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
