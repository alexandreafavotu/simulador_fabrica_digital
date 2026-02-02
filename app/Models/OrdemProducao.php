<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdemProducao extends Model
{
    use HasFactory;

    protected $table = 'ordens_producao';

    protected $fillable = [
        'pedido_venda_id',
        'produto_acabado_id',
        'aluno_id',           // <--- QUEM ESTÁ PRODUZINDO
        'quantidade',
        'quantidade_perda',   // <--- REFUGO
        'status',
        'status_material',    // <--- CONTROLE DO ALMOXARIFADO
        'data_inicio',
        'data_inicio_real',   // <--- DATA DO RELÓGIO QUANDO DEU START
        'data_fim',
        'tem_refugo_forcado',    // <--- ADICIONADO PARA O CAOS
        'qtd_refugo_forcado',    // <--- ADICIONADO PARA O CAOS
        'motivo_refugo_forcado', // <--- ADICIONADO PARA O CAOS

    ];

    public function pedido()
    {
        return $this->belongsTo(PedidoVenda::class, 'pedido_venda_id');
    }

    public function produto()
    {
        return $this->belongsTo(ProdutoAcabado::class, 'produto_acabado_id');
    }

    // Relação: Quem é o operador responsável?
    public function aluno()
    {
        return $this->belongsTo(Aluno::class, 'aluno_id');
    }

    // Relação: Histórico de apontamentos (Relatório)
    public function apontamentos()
    {
        return $this->hasMany(ApontamentoProducao::class);
    }
    /**
     * Relacionamento com as solicitações de separação (Picking) desta OP.
     */
    public function solicitacoesMaterial()
    {
        return $this->hasMany(SolicitacaoSeparacao::class, 'ordem_producao_id');
    }
}