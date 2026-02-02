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
            // Cria um campo de texto para escrever o motivo (pode ser longo)
            $table->text('motivo_recusa')->nullable()->after('data_entrega_prevista');
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
