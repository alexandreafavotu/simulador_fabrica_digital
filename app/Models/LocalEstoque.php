<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalEstoque extends Model
{
    use HasFactory;

    protected $table = 'locais_estoque';

    protected $fillable = [
        'turma_id',
        'codigo_visual', // Ex: R1-C2-V1-N3
        'rua',
        'coluna',
        'vao',
        'nivel',
        'ocupado'
    ];

    // Relação: Um local pode ter uma matéria-prima guardada nele
    public function materiaPrima()
    {
        return $this->hasOne(MateriaPrima::class, 'local_estoque_id');
    }

    // Relação: Um local pode ter um produto acabado guardado nele
    public function produtoAcabado()
    {
        return $this->hasOne(ProdutoAcabado::class, 'local_estoque_id');
    }
}