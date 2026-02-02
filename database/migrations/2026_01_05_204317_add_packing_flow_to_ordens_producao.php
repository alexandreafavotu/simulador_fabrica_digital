<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ordens_producao', function (Blueprint $table) {
            // Define se a OP já passou pela embalagem
            // false = Pendente de embalagem | true = Embalado/Pronto para Expedição
            $table->boolean('embalado')->default(false)->after('status');
            $table->timestamp('data_embalagem')->nullable()->after('embalado');
        });
    }

    public function down(): void
    {
        Schema::table('ordens_producao', function (Blueprint $table) {
            $table->dropColumn(['embalado', 'data_embalagem']);
        });
    }
};