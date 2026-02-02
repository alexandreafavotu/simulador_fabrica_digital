<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class Fornecedor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fornecedores';

    protected $fillable = [
        'nome_razao_social',
        'cnpj_cpf',
        'telefone',
        'email',
        'cep',
        'rua',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'preco_unitario',
        'tempo_entrega_dias',
        'materia_prima_id',
        'turma_id', // <--- ADICIONADO PARA FUNCIONAR O ISOLAMENTO
    ];

    // --- RELACIONAMENTOS ---

    public function materiaPrima()
    {
        return $this->belongsTo(MateriaPrima::class);
    }

    // O relacionamento que estava faltando
    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }
}