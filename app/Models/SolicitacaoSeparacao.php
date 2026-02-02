<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitacaoSeparacao extends Model
{
    use HasFactory;

    protected $table = 'solicitacoes_separacao';

    protected $fillable = [
        'ordem_producao_id',
        'materia_prima_id',
        'quantidade_solicitada',
        'aluno_solicitante_id',
        'status', // Pendente, Separado, Entregue
    ];

    public function ordemProducao()
    {
        return $this->belongsTo(OrdemProducao::class, 'ordem_producao_id');
    }

    public function materiaPrima()
    {
        return $this->belongsTo(MateriaPrima::class, 'materia_prima_id');
    }
}