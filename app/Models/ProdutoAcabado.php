<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class ProdutoAcabado extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'produtos_acabados';

    protected $fillable = [
        'sku',
        'nome',
        'preco_venda',
        'tempo_producao_dias',
        'quantidade_estoque',
        'turma_id',
    ];

    // --- RELACIONAMENTOS ---

    public function materiasPrimas()
    {
        // CORREÇÃO: Definindo os nomes exatos das colunas na tabela pivô 'estrutura_produto'
        // 1. Tabela Pivô
        // 2. Chave deste modelo na pivô (produto_acabado_id)
        // 3. Chave do outro modelo na pivô (materia_prima_id)
        return $this->belongsToMany(MateriaPrima::class, 'estrutura_produto', 'produto_acabado_id', 'materia_prima_id')
                    ->withPivot('quantidade');
    }

    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }
}