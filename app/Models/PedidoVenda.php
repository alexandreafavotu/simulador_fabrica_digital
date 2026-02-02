<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PedidoVenda extends Model
{
    use HasFactory, SoftDeletes;

    // Define o nome da tabela (boa prática para garantir)
    protected $table = 'pedidos_venda';

    protected $fillable = [
        'cliente_id',
        'turma_id', // Adicionamos este campo novo
        'aluno_id', // <--- ADICIONE ESTA LINHA AQUI
        'data_pedido',
        'data_entrega_solicitada',
        'status',
        'valor_total',
        'observacoes'
    ];

    /**
     * Relacionamento: Um pedido pertence a um Cliente.
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Relacionamento: Um pedido tem muitos Itens.
     */
    public function itens()
    {
        return $this->hasMany(PedidoVendaItem::class, 'pedido_venda_id');
    }

    /**
     * Relacionamento: Um pedido pertence a uma Turma.
     */
    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }
    // Relacionamento: Um pedido tem várias ordens de produção
    public function ordensProducao()
    {
        return $this->hasMany(OrdemProducao::class, 'pedido_venda_id');
    }
}