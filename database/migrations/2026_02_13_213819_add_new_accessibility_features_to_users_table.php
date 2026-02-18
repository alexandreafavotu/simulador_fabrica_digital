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
    Schema::table('users', function (Blueprint $table) {
        // Criando os 3 novos interruptores independentes
        $table->boolean('acessibilidade_audio')->default(false);       
        $table->boolean('acessibilidade_pictogramas')->default(false); 
        $table->boolean('acessibilidade_libras')->default(false);      
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['acessibilidade_audio', 'acessibilidade_pictogramas', 'acessibilidade_libras']);
    });
}
};
