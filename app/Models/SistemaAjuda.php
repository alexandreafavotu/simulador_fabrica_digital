<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SistemaAjuda extends Model
{
    use HasFactory;

    protected $table = 'sistema_ajuda';

    protected $fillable = ['rota', 'titulo', 'conteudo'];
}