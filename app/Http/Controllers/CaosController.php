<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Turma;
use App\Models\OrdemProducao;
use App\Models\OrdemCompra;
use App\Models\PedidoVenda;
use Carbon\Carbon;

class CaosController extends Controller
{
    // 1. Exibe o Painel do Caos
    public function index($turmaId)
    {
        $turma = Turma::findOrFail($turmaId);
        
        // Carrega listas para os incidentes pontuais
        // Carrega OPs para sabotagem (Agora aceita 'Aberta' e 'Em Produção')
        $opsEmAndamento = OrdemProducao::with('produto')
            ->whereHas('pedido', fn($q) => $q->where('turma_id', $turmaId))
            ->whereIn('status', ['Aberta', 'Em Produção']) // <--- AQUI ESTÁ A MUDANÇA
            ->where('em_manutencao', false)
            ->get();

        $ocsPendentes = OrdemCompra::with('materiaPrima')
            ->whereHas('pedido', fn($q) => $q->where('turma_id', $turmaId))
            ->whereIn('status', ['Pendente', 'Aguardando Entrega'])
            ->get();

        $pedidosAtivos = PedidoVenda::with('cliente')
            ->where('turma_id', $turmaId)
            ->whereIn('status', ['Novo', 'Em Produção'])
            ->get();

        return view('professor.caos.index', compact('turma', 'opsEmAndamento', 'ocsPendentes', 'pedidosAtivos'));
    }

    // 2. Aplica Bloqueios Globais (Almoxarifado, Expedição, Sistema)
    public function aplicarBloqueio(Request $request, $turmaId)
    {
        $turma = Turma::findOrFail($turmaId);
        $dias = (int) $request->dias;
        $tipo = $request->tipo; // 'almoxarifado', 'expedicao', 'faturamento'

        // Calcula até quando vai o bloqueio (Data Jogo + Dias escolhidos)
        $dataFim = Carbon::parse($turma->data_jogo)->addDays($dias);

        if ($tipo == 'almoxarifado') $turma->bloqueio_almoxarifado_ate = $dataFim;
        if ($tipo == 'expedicao') $turma->bloqueio_expedicao_ate = $dataFim;
        if ($tipo == 'faturamento') $turma->bloqueio_faturamento_ate = $dataFim;

        $turma->save();

        return back()->with('error', "CAOS GERADO! O setor de $tipo ficará parado por $dias dias (até {$dataFim->format('d/m/Y')}).");
    }

    // 3. Aplica Quebra de Máquina
    public function quebrarMaquina(Request $request, $turmaId)
    {
        $request->validate([
            'ordem_producao_id' => 'required|exists:ordens_producao,id',
            'dias_manutencao' => 'required|integer|min:1', 
        ]);

        $turma = Turma::findOrFail($turmaId);
        $op = OrdemProducao::findOrFail($request->ordem_producao_id);
        
        // CORREÇÃO AQUI: Adicionei (int) para converter o texto em número
        $dias = (int) $request->dias_manutencao;

        // Calcula a data de fim da manutenção
        $dataFim = \Carbon\Carbon::parse($turma->data_jogo)->addDays($dias);
        
        $op->em_manutencao = true;
        $op->motivo_manutencao = "Quebra inesperada. Manutenção corretiva em andamento.";
        $op->previsao_conserto_ate = $dataFim; 
        $op->save();

        return back()->with('error', "CAOS GERADO: Máquina parada na OP #{$op->id} por {$dias} dias!");
    }

    // 5. Salva Mensagem Geral (Plantão)
    public function salvarMensagem(Request $request, $turmaId)
    {
        $turma = Turma::findOrFail($turmaId);
        $turma->mensagem_plantao_caos = $request->mensagem;
        $turma->save();

        return back()->with('success', 'Mensagem enviada para todos os alunos da turma.');
    }
    
    // 6. Limpar Bloqueios (Reset)
    public function limparCaos($turmaId)
    {
        $turma = Turma::findOrFail($turmaId);
        $turma->bloqueio_almoxarifado_ate = null;
        $turma->bloqueio_expedicao_ate = null;
        $turma->bloqueio_faturamento_ate = null;
        $turma->mensagem_plantao_caos = null;
        $turma->save();
        
        // Limpa manutenções
        OrdemProducao::whereHas('pedido', fn($q)=>$q->where('turma_id', $turmaId))
                     ->update(['em_manutencao' => false]);

        return back()->with('success', 'Tudo normalizado! O caos acabou.');
    }
    // 4. Aplica Atraso de Fornecedor (Compras)
    public function atrasarFornecedor(Request $request, $turmaId)
    {
        $request->validate([
            'ordem_compra_id' => 'required|exists:ordens_compra,id',
            'dias' => 'required|integer|min:1',
        ]);

        $oc = OrdemCompra::findOrFail($request->ordem_compra_id);
        $dias = (int) $request->dias;
        
        // Se já tiver data, adiciona. Se não, define a partir de hoje da turma.
        $baseData = $oc->data_entrega_prevista 
            ? \Carbon\Carbon::parse($oc->data_entrega_prevista) 
            : \Carbon\Carbon::parse(Turma::find($turmaId)->data_jogo);

        $novaData = $baseData->addDays($dias);
        
        $oc->data_entrega_prevista = $novaData;
        $oc->save();

        return back()->with('error', "CAOS GERADO: Fornecedor atrasou a entrega em {$dias} dias! Nova data: {$novaData->format('d/m/Y')}.");
    }
    public function sabotarCarga(Request $request, $turmaId)
    {
        $request->validate([
            'ordem_compra_id' => 'required|exists:ordens_compra,id',
            'descricao_inconformidade' => 'required|string',
        ]);

        $oc = \App\Models\OrdemCompra::findOrFail($request->ordem_compra_id);
        
        $oc->tem_inconformidade = true;
        $oc->descricao_inconformidade = $request->descricao_inconformidade;
        $oc->save();

        return back()->with('error', "INCONFORMIDADE GERADA: A OC #{$oc->id} agora possui uma falha técnica programada!");
    }

    // 8. Sabotar Produção (Refugo Programado)
    public function sabotarProducao(Request $request, $turmaId)
    {
        $request->validate([
            'ordem_producao_id' => 'required|exists:ordens_producao,id',
            'qtd_refugo_forcado' => 'required|integer|min:1',
            'motivo_refugo_forcado' => 'required|string',
        ]);

        $op = \App\Models\OrdemProducao::findOrFail($request->ordem_producao_id);
        
        // Grava a ordem de sabotagem no banco
        $op->tem_refugo_forcado = true;
        $op->qtd_refugo_forcado = $request->qtd_refugo_forcado;
        $op->motivo_refugo_forcado = $request->motivo_refugo_forcado;
        $op->save();

        return back()->with('error', "FALHA INJETADA: A OP #{$op->id} foi programada para ter {$request->qtd_refugo_forcado} un de refugo!");
    }
}