<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdemCompra extends Model
{
    use HasFactory;

    protected $table = 'ordens_compra';

    protected $fillable = [
        'pedido_venda_id',
        'materia_prima_id',
        'fornecedor_id',
        'quantidade',
        'status',
        'data_entrega_prevista',
        'motivo_recusa',
        'recompra_gerada', // <--- ADICIONADO (Para controle de recusas)
        'urgente',         // <--- ADICIONADO (O culpado! Agora vai salvar como urgente)
        'tem_inconformidade',        // <--- ADICIONADO PARA O PAINEL DO CAOS
        'descricao_inconformidade',  // <--- ADICIONADO PARA O PAINEL DO CAOS
    ];

    public function pedido()
    {
        return $this->belongsTo(PedidoVenda::class, 'pedido_venda_id');
    }

    public function materiaPrima()
    {
        return $this->belongsTo(MateriaPrima::class, 'materia_prima_id');
    }

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class, 'fornecedor_id');
    }
}