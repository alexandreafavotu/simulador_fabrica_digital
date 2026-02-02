<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class MateriaPrima extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'materias_primas';

    protected $fillable = [
        'sku',
        'nome',
        'unidade_medida',
        'quantidade_estoque',
        'local_estoque_id',
        'turma_id', // <--- ADICIONADO
    ];

    // --- RELACIONAMENTOS ---

    public function local()
    {
        return $this->belongsTo(LocalEstoque::class, 'local_estoque_id');
    }

    // O relacionamento que estava faltando
    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }
}