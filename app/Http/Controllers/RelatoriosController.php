<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Turma;
use App\Models\PedidoVenda;
use App\Models\OrdemProducao;
use App\Models\OrdemCompra;
use App\Models\ApontamentoProducao;
use App\Models\MateriaPrima;
use App\Models\ProdutoAcabado;
use App\Models\LocalEstoque;
use App\Models\NotaFiscal;
use Carbon\Carbon;

class RelatoriosController extends Controller
{
    public function index(Request $request, $turmaId)
    {
        $turma = Turma::findOrFail($turmaId);

        // --- FILTROS DE TEMPO (INTELIGÊNCIA DE DATA) ---
        $dtInicio = $request->input('data_inicio');
        $dtFim = $request->input('data_fim');

        // Função auxiliar para aplicar filtro de data nas queries
        $filtro = function($query, $colunaData = 'created_at') use ($dtInicio, $dtFim) {
            if ($dtInicio) $query->whereDate($colunaData, '>=', $dtInicio);
            if ($dtFim) $query->whereDate($colunaData, '<=', $dtFim);
        };

        // --- 1. COMERCIAL (VENDAS) ---
        
        // Faturamento (Filtrado por data de emissão da NF)
        $faturadoReal = NotaFiscal::whereHas('pedido', fn($q)=>$q->where('turma_id', $turmaId))
            ->tap(fn($q) => $filtro($q, 'data_emissao'))
            ->sum('valor_total');

        // Backlog (Filtrado por data do pedido)
        $backlogVendas = PedidoVenda::where('turma_id', $turmaId)
            ->whereIn('status', ['Novo', 'Em Produção'])
            ->tap(fn($q) => $filtro($q, 'data_pedido'))
            ->sum('valor_total');

        $vendasCanceladas = PedidoVenda::where('turma_id', $turmaId)
            ->where('status', 'Cancelado')
            ->tap(fn($q) => $filtro($q, 'data_pedido'))
            ->count();

        $totalPedidos = PedidoVenda::where('turma_id', $turmaId)
            ->tap(fn($q) => $filtro($q, 'data_pedido'))
            ->count();
        
        $taxaCancelamento = $totalPedidos > 0 ? ($vendasCanceladas / $totalPedidos) * 100 : 0;
        $ticketMedio = $totalPedidos > 0 ? ($faturadoReal + $backlogVendas) / $totalPedidos : 0;

        // Funil de Vendas (Filtrado)
        $qFunil = PedidoVenda::where('turma_id', $turmaId)->tap(fn($q) => $filtro($q, 'data_pedido'));
        $funilVendas = [
            'Novo' => (clone $qFunil)->where('status', 'Novo')->count(),
            'Em Produção' => (clone $qFunil)->where('status', 'Em Produção')->count(),
            'Faturado' => (clone $qFunil)->where('status', 'Faturado')->count(),
            'Cancelado' => (clone $qFunil)->where('status', 'Cancelado')->count(),
        ];

        // Curva ABC (Filtrada)
        $abcRaw = DB::table('pedido_venda_itens')
            ->join('pedidos_venda', 'pedido_venda_itens.pedido_venda_id', '=', 'pedidos_venda.id')
            ->join('produtos_acabados', 'pedido_venda_itens.produto_acabado_id', '=', 'produtos_acabados.id')
            ->where('pedidos_venda.turma_id', $turmaId)
            ->when($dtInicio, fn($q) => $q->whereDate('pedidos_venda.data_pedido', '>=', $dtInicio))
            ->when($dtFim, fn($q) => $q->whereDate('pedidos_venda.data_pedido', '<=', $dtFim))
            ->select('produtos_acabados.nome', DB::raw('SUM(pedido_venda_itens.preco_total_item) as total'))
            ->groupBy('produtos_acabados.nome')->orderByDesc('total')->get();

        $somaAbc = $abcRaw->sum('total');
        $acumulado = 0;
        $curvaABC = $abcRaw->map(function($item) use (&$acumulado, $somaAbc) {
            $acumulado += $item->total;
            $perc = $somaAbc > 0 ? ($acumulado / $somaAbc) * 100 : 0;
            $item->classe = $perc <= 80 ? 'A' : ($perc <= 95 ? 'B' : 'C');
            return $item;
        });

        // Top 5 Clientes (Filtrado)
        $topClientes = PedidoVenda::with('cliente')
            ->where('turma_id', $turmaId)
            ->tap(fn($q) => $filtro($q, 'data_pedido'))
            ->select('cliente_id', DB::raw('SUM(valor_total) as total'))
            ->groupBy('cliente_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function($p) {
                return ['nome' => $p->cliente->nome_razao_social ?? 'N/A', 'total' => $p->total];
            });

        // --- 2. PRODUÇÃO ---
        $qOP = OrdemProducao::whereHas('pedido', fn($q)=>$q->where('turma_id', $turmaId))->tap(fn($q) => $filtro($q, 'data_inicio'));
        $opsStatus = [
            'Aberta' => (clone $qOP)->where('status', 'Aberta')->count(),
            'Em Produção' => (clone $qOP)->where('status', 'Em Produção')->count(),
            'Concluída' => (clone $qOP)->where('status', 'Concluída')->count(),
        ];

        // Apontamentos (Filtrado por data do apontamento)
        $qApont = ApontamentoProducao::whereHas('ordem.pedido', fn($q)=>$q->where('turma_id', $turmaId))
            ->tap(fn($q) => $filtro($q, 'data_apontamento'));

        $pecasBoas = (clone $qApont)->sum('quantidade_produzida');
        $pecasRefugo = (clone $qApont)->sum('quantidade_refugo');
        
        $totalGeralProd = $pecasBoas + $pecasRefugo;
        $taxaQualidade = $totalGeralProd > 0 ? ($pecasBoas / $totalGeralProd) * 100 : 100;
        
        // Meta Total (Apenas OPs Concluídas no período)
        $metaTotal = (clone $qOP)->where('status', 'Concluída')->sum('quantidade');
        $taxaPerformance = $metaTotal > 0 ? ($pecasBoas / $metaTotal) * 100 : 100;
        $oee = ($taxaQualidade * $taxaPerformance) / 100;

        // --- 3. CUSTO DO CAOS (FINANCEIRO) ---
        
        // Custo Recusas (Filtrado)
        $custoRecusas = DB::table('ordens_compra')
            ->join('pedidos_venda', 'ordens_compra.pedido_venda_id', '=', 'pedidos_venda.id')
            ->join('fornecedores', 'ordens_compra.fornecedor_id', '=', 'fornecedores.id')
            ->where('pedidos_venda.turma_id', $turmaId)
            ->where('ordens_compra.status', 'Recusado')
            ->when($dtInicio, fn($q) => $q->whereDate('ordens_compra.updated_at', '>=', $dtInicio))
            ->when($dtFim, fn($q) => $q->whereDate('ordens_compra.updated_at', '<=', $dtFim))
            ->sum(DB::raw('ordens_compra.quantidade * fornecedores.preco_unitario'));

        // Custo Refugo (Filtrado)
        $custoRefugo = DB::table('apontamentos_producao')
            ->join('ordens_producao', 'apontamentos_producao.ordem_producao_id', '=', 'ordens_producao.id')
            ->join('pedidos_venda', 'ordens_producao.pedido_venda_id', '=', 'pedidos_venda.id')
            ->join('produtos_acabados', 'ordens_producao.produto_acabado_id', '=', 'produtos_acabados.id')
            ->where('pedidos_venda.turma_id', $turmaId)
            ->when($dtInicio, fn($q) => $q->whereDate('apontamentos_producao.data_apontamento', '>=', $dtInicio))
            ->when($dtFim, fn($q) => $q->whereDate('apontamentos_producao.data_apontamento', '<=', $dtFim))
            ->sum(DB::raw('apontamentos_producao.quantidade_refugo * (produtos_acabados.preco_venda * 0.40)'));

        $totalCaos = $custoRecusas + $custoRefugo;

        // --- 4. COMPRAS ---
        $qCompras = OrdemCompra::whereHas('pedido', fn($q)=>$q->where('turma_id', $turmaId))->tap(fn($q) => $filtro($q, 'created_at'));
        
        $totalOCs = (clone $qCompras)->count();
        $recusadasOCs = (clone $qCompras)->where('status', 'Recusado')->count();
        $qualidadeFornecedor = $totalOCs > 0 ? (($totalOCs - $recusadasOCs) / $totalOCs) * 100 : 100;
        $backlogCompras = (clone $qCompras)->where('status', 'Pendente')->count();

        $spendAnalysis = DB::table('ordens_compra')
            ->join('pedidos_venda', 'ordens_compra.pedido_venda_id', '=', 'pedidos_venda.id')
            ->join('materias_primas', 'ordens_compra.materia_prima_id', '=', 'materias_primas.id')
            ->leftJoin('fornecedores', 'ordens_compra.fornecedor_id', '=', 'fornecedores.id')
            ->where('pedidos_venda.turma_id', $turmaId)
            ->when($dtInicio, fn($q) => $q->whereDate('ordens_compra.created_at', '>=', $dtInicio))
            ->when($dtFim, fn($q) => $q->whereDate('ordens_compra.created_at', '<=', $dtFim))
            ->select('materias_primas.nome', DB::raw('SUM(ordens_compra.quantidade * COALESCE(fornecedores.preco_unitario, 0)) as gasto'))
            ->groupBy('materias_primas.nome')
            ->orderByDesc('gasto')
            ->limit(5)
            ->get();

        // --- 5. LOGÍSTICA & ESTOQUE (Snapshot Atual - Sem Filtro de Tempo) ---
        $valorMP = MateriaPrima::where(fn($q) => $q->where('turma_id', $turmaId)->orWhereNull('turma_id'))
            ->get()->sum(fn($mp) => $mp->quantidade_estoque * ($mp->preco_unitario ?? 50));

        $valorPA = ProdutoAcabado::all()->sum(fn($pa) => $pa->quantidade_estoque * $pa->preco_venda);
        
        $totalLocais = LocalEstoque::where('turma_id', $turmaId)->count();
        $ocupados = LocalEstoque::where('turma_id', $turmaId)->where('ocupado', true)->count();
        $taxaOcupacao = $totalLocais > 0 ? ($ocupados / $totalLocais) * 100 : 0;
        $itensNoChao = MateriaPrima::where('quantidade_estoque', '>', 0)->whereNull('local_estoque_id')->count();

        $docaSaida = PedidoVenda::where('turma_id', $turmaId)->where('status', 'Em Produção')
            ->whereDoesntHave('ordensProducao', fn($q)=>$q->where('status', '!=', 'Concluída'))->count();

        // OTIF (Filtra Faturados no período)
        $pedidosFaturados = PedidoVenda::where('turma_id', $turmaId)->where('status', 'Faturado')
            ->tap(fn($q) => $filtro($q, 'updated_at'))->get();
            
        $entreguesNoPrazo = $pedidosFaturados->filter(fn($p) => $p->updated_at <= $p->data_entrega_solicitada)->count();
        $otif = $pedidosFaturados->count() > 0 ? ($entreguesNoPrazo / $pedidosFaturados->count()) * 100 : 100;

        // --- PACOTE FINAL ---
        $dados = [
            'vendas' => [
                'total' => $faturadoReal,           
                'backlog_valor' => $backlogVendas,  
                'ticket_medio' => $ticketMedio,
                'cancelamento' => $taxaCancelamento,
                'funil' => $funilVendas,
                'abc' => $curvaABC,
                'top_clientes' => $topClientes,
                'tendencia' => [] 
            ],
            'producao' => [
                'produzido' => $pecasBoas,
                'refugo_qtd' => $pecasRefugo,
                'taxa_refugo' => (100 - $taxaQualidade),
                'qualidade' => $taxaQualidade,
                'performance' => $taxaPerformance,
                'oee' => $oee,
                'status' => $opsStatus,
                'lead_time' => "2.5 Dias"
            ],
            'caos' => [
                'total_perda' => $totalCaos,
                'custo_wms' => $custoRecusas,
                'custo_prod' => $custoRefugo
            ],
            'estoque' => [
                'valor_total' => $valorMP + $valorPA,
                'valor_mp' => $valorMP,
                'valor_pa' => $valorPA,
                'ocupacao_wms' => $taxaOcupacao,
                'no_chao' => $itensNoChao
            ],
            'compras' => [
                'qualidade_fornecedor' => $qualidadeFornecedor,
                'backlog_qtd' => $backlogCompras,
                'spend_analysis' => $spendAnalysis
            ],
            'logistica' => [
                'otif' => $otif,
                'giro' => 4.5,
                'doca_saida' => $docaSaida
            ]
        ];

        return view('professor.bi.index', compact('turma', 'dados'));
    }
}