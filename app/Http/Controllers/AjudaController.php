<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SistemaAjuda;

class AjudaController extends Controller
{
    // Função para traduzir rotas de monitoramento para rotas de aluno
    private function mapearRota($rota)
    {
        $mapa = [
            'professor.monitoramento.vendas'       => 'aluno.vendas.index',
            'professor.monitoramento.pcp'          => 'aluno.pcp.dashboard',
            'professor.monitoramento.compras'      => 'aluno.compras.dashboard',
            'professor.monitoramento.almoxarifado' => 'aluno.almoxarifado.dashboard',
            'professor.monitoramento.producao'     => 'aluno.producao.dashboard',
            'professor.monitoramento.expedicao'    => 'aluno.expedicao.dashboard',
        ];

        // Se a rota que veio estiver no mapa, retorna a do aluno. Se não, retorna ela mesma.
        return $mapa[$rota] ?? $rota;
    }

    public function getConteudo(Request $request)
    {
        // Traduz o endereço antes de buscar no banco
        $rotaUnificada = $this->mapearRota($request->rota);
        
        $ajuda = SistemaAjuda::where('rota', $rotaUnificada)->first();

        return response()->json([
            'titulo' => $ajuda ? $ajuda->titulo : 'Instruções do Setor',
            'conteudo' => $ajuda ? $ajuda->conteudo : '<p class="text-gray-400 italic">O professor ainda não cadastrou instruções para esta tela.</p>',
            'rota_usada' => $rotaUnificada // Debug para sabermos o que está acontecendo
        ]);
    }

    public function salvarConteudo(Request $request)
    {
        if ($request->user()->tipo !== 'professor') {
            abort(403);
}

        //Traduz o endereço antes de salvar
        $rotaUnificada = $this->mapearRota($request->rota);

        SistemaAjuda::updateOrCreate(
            ['rota' => $rotaUnificada],
            [
                'titulo' => $request->titulo,
                'conteudo' => $request->conteudo
            ]
        );

        return response()->json(['status' => 'success']);
    }
}