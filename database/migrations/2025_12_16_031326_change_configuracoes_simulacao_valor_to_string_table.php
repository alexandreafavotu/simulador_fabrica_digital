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
        Schema::table('configuracoes_simulacao', function (Blueprint $table) {
            // Altera a coluna para aceitar string (texto), que resolve datas e regras
            $table->string('valor')->change(); 
        });
    }

    public function down(): void
    {
        Schema::table('configuracoes_simulacao', function (Blueprint $table) {
            // Volta para integer/numeric no rollback (o que era antes)
            $table->integer('valor')->change(); 
        });
    }
};
