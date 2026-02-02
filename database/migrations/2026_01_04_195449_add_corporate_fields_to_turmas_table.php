<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('turmas', function (Blueprint $table) {
            // Campos de Identificação e Negócio
            $table->string('setor')->nullable()->after('nome_empresa');
            $table->string('segmento')->nullable()->after('setor');
            $table->text('resumo')->nullable()->after('segmento');
            $table->string('cnpj')->nullable()->after('resumo');
            $table->string('telefone')->nullable()->after('cnpj');

            // Campos de Endereço
            $table->string('rua')->nullable()->after('telefone');
            $table->string('numero')->nullable()->after('rua');
            $table->string('bairro')->nullable()->after('numero');
            $table->string('cidade')->nullable()->after('bairro');
            $table->string('estado', 2)->nullable()->after('cidade');
        });
    }

    public function down(): void
    {
        Schema::table('turmas', function (Blueprint $table) {
            $table->dropColumn([
                'setor', 'segmento', 'resumo', 'cnpj', 
                'telefone', 'rua', 'numero', 'bairro', 
                'cidade', 'estado'
            ]);
        });
    }
};