<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandaMercadoItem extends Model
{
    protected $table = 'demanda_mercado_itens';
    protected $fillable = ['demanda_mercado_id', 'produto_acabado_id', 'quantidade'];

    // Relacionamento: O item refere-se a um produto acabado
    public function produto() {
        return $this->belongsTo(ProdutoAcabado::class, 'produto_acabado_id');
    }
}