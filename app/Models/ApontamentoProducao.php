<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApontamentoProducao extends Model
{
    use HasFactory;

    protected $table = 'apontamentos_producao';

    protected $fillable = [
        'ordem_producao_id',
        'aluno_id',
        'data_apontamento',      // Data do Jogo
        'quantidade_produzida',
        'quantidade_refugo',
    ];

    public function ordem()
    {
        return $this->belongsTo(OrdemProducao::class, 'ordem_producao_id');
    }

    public function aluno()
    {
        return $this->belongsTo(Aluno::class, 'aluno_id');
    }
}