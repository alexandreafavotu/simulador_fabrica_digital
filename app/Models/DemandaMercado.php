<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandaMercado extends Model
{
    protected $table = 'demandas_mercado';
    protected $fillable = ['turma_id', 'cliente_id', 'data_entrega_solicitada', 'status', 'aluno_id', 'data_jogo_emissao'];

    // Relacionamento: Uma demanda tem muitos itens
    public function itens() {
        return $this->hasMany(DemandaMercadoItem::class);
    }

    // Relacionamento: Uma demanda pertence a um cliente
    public function cliente() {
        return $this->belongsTo(Cliente::class);
    }
}