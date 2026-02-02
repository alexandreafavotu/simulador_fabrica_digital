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
    Schema::table('turmas', function (Blueprint $table) {
        $table->integer('capacidade_producao')->default(3); // Começa com 3 por padrão
    });
}

public function down(): void
{
    Schema::table('turmas', function (Blueprint $table) {
        $table->dropColumn('capacidade_producao');
    });
}
};
