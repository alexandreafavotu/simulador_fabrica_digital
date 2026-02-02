<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <--- 1. ADICIONE ESTA LINHA

class Aluno extends Model
{
    use HasFactory, SoftDeletes; // <--- 2. ADICIONE ", SoftDeletes" AQUI

    protected $fillable = [
    'user_id',
    'turma_id',
    'nome', // Adicione esta linha se não tiver
    'setor',
];

    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}