<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nome_razao_social',
        'cnpj_cpf',
        'telefone',
        'cep',
        'rua',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'turma_id', // <--- ADICIONADO
    ];

    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }
}