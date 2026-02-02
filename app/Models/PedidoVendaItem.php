<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class PedidoVendaItem extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'pedido_venda_itens';

    protected $fillable = [
        'pedido_venda_id',
        'produto_acabado_id',
        'quantidade',
        'preco_unitario',
        'preco_total_item',
    ];

    /**
     * Define o relacionamento: um Item de Pedido pertence a um Produto Acabado.
     */
    public function produto()
    {
        return $this->belongsTo(ProdutoAcabado::class, 'produto_acabado_id');
    }
    public function pedido()
{
    return $this->belongsTo(PedidoVenda::class, 'pedido_venda_id');
}
}