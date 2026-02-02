<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracaoSimulacao extends Model
{
    use HasFactory;

    protected $table = 'configuracoes_simulacao';

    protected $fillable = [
        'chave',
        'valor',
    ];
}