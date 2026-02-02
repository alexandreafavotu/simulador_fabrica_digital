<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Turma extends Model
{
    use HasFactory;

    protected $table = 'turmas';

    protected $fillable = [
        'professor_id', // <--- NOVO: O dono da turma
        'nome',
        'nome_empresa',
        'setor',       // <--- NOVO
        'segmento',    // <--- NOVO
        'resumo',      // <--- NOVO
        'cnpj',        // <--- NOVO
        'telefone',    // <--- NOVO
        'rua',         // <--- NOVO
        'numero',      // <--- NOVO
        'bairro',      // <--- NOVO
        'cidade',      // <--- NOVO
        'estado',      // <--- NOVO
        'data_jogo',
        'jogo_ativo',
        'capacidade_producao',
        'arquivada',
        // Campos do Painel de Caos (Adicionei para garantir funcionamento)
        'bloqueio_almoxarifado_ate',
        'bloqueio_expedicao_ate',
        'bloqueio_faturamento_ate',
        'mensagem_plantao_caos'
    ];

    protected $casts = [
        'data_jogo' => 'datetime',
        'jogo_ativo' => 'boolean',
        'arquivada' => 'boolean',
        // Garante que o Laravel entenda isso como datas
        'bloqueio_almoxarifado_ate' => 'datetime',
        'bloqueio_expedicao_ate' => 'datetime',
        'bloqueio_faturamento_ate' => 'datetime',
    ];

    // --- RELACIONAMENTOS ---

    // Uma Turma tem muitos Alunos
    public function alunos()
    {
        return $this->hasMany(Aluno::class);
    }
    
    // Uma Turma pertence a UM Professor
    public function professor()
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    // --- ESCOPO INTELIGENTE (A Regra do Admin) ---
    
    /**
     * Filtra automaticamente as turmas.
     * Uso no Controller: Turma::doProfessor()->get();
     */
    public function scopeDoProfessor($query)
    {
        $user = Auth::user();

        // Segurança básica: se ninguém tá logado, não retorna nada (ou retorna tudo se for script)
        if (!$user) return $query;

        // 1. REGRA DO SUPER ADMIN (VOCÊ)
        // Se o ID for 1, ele vê TODAS as turmas de todo mundo.
        // Assim você pode filtrar na tela depois.
        if ($user->id === 1) {
            return $query; 
        }

        // 2. REGRA DOS OUTROS PROFESSORES
        // Só veem as turmas que eles criaram (onde professor_id é igual ao ID deles)
        if ($user->tipo === 'professor') {
            return $query->where('professor_id', $user->id);
        }

        return $query;
    }
}