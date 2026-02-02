<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Turma;
use App\Models\Aluno;
use Illuminate\Support\Facades\Auth;
use App\Models\PedidoVenda;
use App\Models\OrdemProducao;
use App\Models\OrdemCompra;
use App\Models\MateriaPrima;
use App\Models\ProdutoAcabado;
use App\Models\Cliente;
use App\Models\LocalEstoque;
use App\Models\SolicitacaoSeparacao;
use App\Models\NotaFiscal;
use App\Models\ApontamentoProducao;

class MonitoramentoController extends Controller
{
    public function index($turmaId)
    {
        $turma = Turma::findOrFail($turmaId);
        return view('professor.monitoramento.hub', compact('turma'));
    }

    private function getAlunoSimulado($turmaId)
    {
        $aluno = new Aluno();
        $aluno->turma_id = $turmaId;
        $aluno->user = Auth::user(); 
        $aluno->nome = "PROFESSOR (MODO ESPIÃO)";
        $aluno->setor = 'monitoramento';
        $aluno->setRelation('turma', Turma::find($turmaId));
        return $aluno;
    }

    // --- PCP (CORRIGIDO: AGORA LÊ A URL) ---
    public function verPcp(Request $request, $turmaId)
    {
        $aluno = $this->getAlunoSimulado($turmaId);
        $dataJogo = $aluno->turma->data_jogo;
        $modo = $request->input('tabela', 'menu'); // Lê a URL (?tabela=vendas)

        $dados = [];

        switch ($modo) {
            case 'vendas':
                $query = PedidoVenda::with(['cliente', 'itens.produto'])
                            ->where('turma_id', $turmaId)
                            ->where('status', 'Novo');
                
                if ($request->filled('filtro_cliente')) $query->where('cliente_id', $request->filtro_cliente);
                if ($request->filled('filtro_produto')) {
                    $query->whereHas('itens.produto', function($q) use ($request) {
                        $q->where('id', $request->filtro_produto);
                    });
                }
                $dados = $query->orderBy('created_at', 'asc')->get();
                break;

            case 'producao':
                $query = OrdemProducao::with(['produto', 'pedido.cliente'])
                            ->whereHas('pedido', function($q) use ($turmaId) { $q->where('turma_id', $turmaId); });
                
                if ($request->filled('filtro_status_op')) $query->where('status', $request->filtro_status_op);
                $dados = $query->orderBy('created_at', 'desc')->get();
                break;

            case 'compras':
                $query = OrdemCompra::with(['materiaPrima', 'pedido'])
                            ->whereHas('pedido', function($q) use ($turmaId) { $q->where('turma_id', $turmaId); });
                
                if ($request->filled('filtro_status_compra')) $query->where('status', $request->filtro_status_compra);
                if ($request->filled('busca_material')) {
                    $termo = $request->busca_material;
                    $query->whereHas('materiaPrima', fn($q)=>$q->where('nome', 'like', "%{$termo}%"));
                }
                $dados = $query->orderBy('created_at', 'desc')->get();
                break;

            default: // MODO MENU
                $dados = [
                    'qtd_vendas' => PedidoVenda::where('turma_id', $turmaId)->where('status', 'Novo')->count(),
                    'qtd_producao' => OrdemProducao::whereHas('pedido', fn($q)=>$q->where('turma_id', $turmaId))->where('status', '!=', 'Concluída')->count(),
                    'qtd_compras' => OrdemCompra::whereHas('pedido', fn($q)=>$q->where('turma_id', $turmaId))->where('status', 'Pendente')->count(),
                ];
                break;
        }
        
        $clientes = Cliente::where('turma_id', $turmaId)->get();
        $produtos = ProdutoAcabado::all();

        return view('aluno.pcp.dashboard', compact('aluno', 'dados', 'modo', 'clientes', 'produtos', 'dataJogo'));
    }

    // --- COMPRAS (CORRIGIDO) ---
    public function verCompras(Request $request, $turmaId)
    {
        $aluno = $this->getAlunoSimulado($turmaId);
        $modo = $request->input('tela', 'menu'); 

        $dados = [];
        $stats = [];

        switch ($modo) {
            case 'cotacao':
                $query = OrdemCompra::with(['materiaPrima', 'pedido.cliente'])
                            ->whereHas('pedido', fn($q)=>$q->where('turma_id', $turmaId))
                            ->where('status', 'Pendente');
                if ($request->filled('busca')) {
                    $termo = $request->busca;
                    $query->whereHas('materiaPrima', fn($q)=>$q->where('nome', 'like', "%{$termo}%"));
                }
                $dados = $query->orderBy('urgente', 'desc')->orderBy('created_at', 'asc')->get();
                break;

            case 'recusados':
                $query = OrdemCompra::with(['materiaPrima', 'pedido', 'fornecedor'])
                            ->whereHas('pedido', fn($q)=>$q->where('turma_id', $turmaId))
                            ->where('status', 'Recusado');
                // Filtros
                if ($request->filled('filtro_fornecedor')) {
                    $termo = $request->filtro_fornecedor;
                    $query->whereHas('fornecedor', fn($q) => $q->where('nome_razao_social', 'like', "%{$termo}%"));
                }
                if ($request->filled('busca_material')) {
                    $termo = $request->busca_material;
                    $query->whereHas('materiaPrima', fn($q) => $q->where('nome', 'like', "%{$termo}%"));
                }
                if ($request->filled('filtro_data')) {
                    $query->whereDate('updated_at', $request->filtro_data);
                }
                $dados = $query->orderBy('updated_at', 'desc')->get();
                break;

            case 'historico':
                $query = OrdemCompra::with(['materiaPrima', 'pedido', 'fornecedor'])
                            ->whereHas('pedido', fn($q)=>$q->where('turma_id', $turmaId))
                            ->whereNotIn('status', ['Pendente', 'Recusado']);
                if ($request->filled('filtro_status')) $query->where('status', $request->filtro_status);
                if ($request->filled('busca')) {
                    $termo = $request->busca;
                    $query->whereHas('materiaPrima', fn($q)=>$q->where('nome', 'like', "%{$termo}%"));
                }
                $dados = $query->orderBy('updated_at', 'desc')->get();
                break;

            default:
                $stats = [
                    'pendentes' => OrdemCompra::whereHas('pedido', fn($q)=>$q->where('turma_id', $turmaId))->where('status', 'Pendente')->count(),
                    'chegando' => OrdemCompra::whereHas('pedido', fn($q)=>$q->where('turma_id', $turmaId))->where('status', 'Aguardando Entrega')->count(),
                    'recusados' => OrdemCompra::whereHas('pedido', fn($q)=>$q->where('turma_id', $turmaId))->where('status', 'Recusado')->where('recompra_gerada', 0)->count(),
                ];
                break;
        }

        return view('aluno.compras.dashboard', compact('aluno', 'modo', 'dados', 'stats'));
    }

    // --- ALMOXARIFADO (CORRIGIDO) ---
    public function verAlmoxarifado(Request $request, $turmaId)
    {
        $aluno = $this->getAlunoSimulado($turmaId);
        $modo = $request->input('tela', 'menu');
        $hojeNoJogo = $aluno->turma->data_jogo;

        $entregas = []; $solicitacoes = [];
        $stats = [];

        switch ($modo) {
            case 'separacao':
                $solicitacoes = SolicitacaoSeparacao::with(['materiaPrima', 'ordemProducao.produto'])
                    ->whereHas('ordemProducao.pedido', fn($q)=>$q->where('turma_id', $turmaId))
                    ->where('status', 'Pendente')
                    ->get();
                break;

            default:
                $entregas = OrdemCompra::with(['materiaPrima', 'pedido', 'fornecedor'])
                    ->whereHas('pedido', fn($q)=>$q->where('turma_id', $turmaId))
                    ->where('status', 'Aguardando Entrega')
                    ->whereDate('data_entrega_prevista', '<=', $hojeNoJogo)
                    ->orderBy('updated_at', 'asc')->get();

                $stats = ['qtd_separacao' => SolicitacaoSeparacao::whereHas('ordemProducao.pedido', fn($q)=>$q->where('turma_id', $turmaId))->where('status', 'Pendente')->count()];
                break;
        }

        return view('aluno.almoxarifado.dashboard', compact('entregas', 'solicitacoes', 'aluno', 'modo', 'stats'));
    }

    // --- PRODUÇÃO (CORRIGIDO) ---
    public function verProducao(Request $request, $turmaId)
    {
        $aluno = $this->getAlunoSimulado($turmaId);
        $dataJogo = $aluno->turma->data_jogo;
        $modo = $request->input('tela', 'producao');

        $ordensDisponiveis = [];
        $minhasOrdens = [];
        $historico = [];

        switch ($modo) {
            case 'historico':
                $queryHistorico = ApontamentoProducao::with(['ordem.produto', 'ordem.pedido.cliente', 'aluno'])
                    ->whereHas('ordem.pedido', fn($q)=>$q->where('turma_id', $turmaId));
                
                if ($request->filled('filtro_data')) $queryHistorico->whereDate('data_apontamento', $request->filtro_data);
                if ($request->filled('filtro_produto')) {
                    $termo = $request->filtro_produto;
                    $queryHistorico->whereHas('ordem.produto', fn($q)=>$q->where('nome', 'like', "%{$termo}%"));
                }
                $historico = $queryHistorico->orderBy('data_apontamento', 'desc')->get();
                break;

            default:
                // Carrega Receita (Materiais) para validar estoque na view
                $ordensDisponiveis = OrdemProducao::with(['produto.materiasPrimas', 'pedido.cliente'])
                    ->whereHas('pedido', fn($q)=>$q->where('turma_id', $turmaId))
                    ->where('status', 'Aberta')
                    ->whereNull('aluno_id')
                    ->orderBy('created_at', 'asc')->get();

                // Vê a linha toda da turma
                $minhasOrdens = OrdemProducao::with(['produto', 'pedido', 'aluno'])
                    ->whereHas('pedido', fn($q)=>$q->where('turma_id', $turmaId))
                    ->whereNotNull('aluno_id')
                    ->where('status', '!=', 'Concluída')
                    ->orderBy('status', 'desc')
                    ->get();
                break;
        }

        return view('aluno.producao.dashboard', compact('ordensDisponiveis', 'minhasOrdens', 'aluno', 'dataJogo', 'historico', 'modo'));
    }

    // --- EXPEDIÇÃO (CORRIGIDO) ---
    public function verExpedicao(Request $request, $turmaId)
    {
        $aluno = $this->getAlunoSimulado($turmaId);
        $modo = $request->input('tela', 'menu');
        $clientes = Cliente::where('turma_id', $turmaId)->get();

        $pedidosParaFaturar = [];
        $notasEmitidas = [];
        $stats = [];

        switch ($modo) {
            case 'faturar':
                $query = PedidoVenda::with(['cliente', 'itens.produto'])
                    ->where('turma_id', $turmaId)
                    ->where('status', 'Em Produção')
                    ->whereDoesntHave('ordensProducao', fn($q)=>$q->where('status', '!=', 'Concluída'));
                
                if ($request->filled('filtro_cliente')) $query->where('cliente_id', $request->filtro_cliente);
                $pedidosParaFaturar = $query->orderBy('data_entrega_solicitada', 'asc')->get();
                break;

            case 'historico':
                $query = NotaFiscal::with('pedido.cliente')->whereHas('pedido', fn($q)=>$q->where('turma_id', $turmaId));
                if ($request->filled('busca')) {
                    $termo = $request->busca;
                    $query->where(fn($q)=>$q->where('numero_nota', 'like', "%{$termo}%")->orWhereHas('pedido.cliente', fn($sq)=>$sq->where('nome_razao_social', 'like', "%{$termo}%")));
                }
                $notasEmitidas = $query->orderBy('created_at', 'desc')->get();
                break;

            default:
                $stats = [
                    'a_faturar' => PedidoVenda::where('turma_id', $turmaId)->where('status', 'Em Produção')->whereDoesntHave('ordensProducao', fn($q)=>$q->where('status', '!=', 'Concluída'))->count(),
                    'total_notas' => NotaFiscal::whereHas('pedido', fn($q)=>$q->where('turma_id', $turmaId))->count()
                ];
                break;
        }

        return view('aluno.expedicao.dashboard', compact('pedidosParaFaturar', 'notasEmitidas', 'aluno', 'modo', 'stats', 'clientes'));
    }

    // --- VENDAS (CORRIGIDO) ---
    public function verVendas(Request $request, $turmaId)
    {
        $aluno = $this->getAlunoSimulado($turmaId);
        $modo = $request->input('tela', 'menu');
        $dataSimulacao = $aluno->turma->data_jogo;

        $pedidos = [];
        $dadosStats = [];
        $clientes = Cliente::where('turma_id', $turmaId)->get();
        $produtos = ProdutoAcabado::all();

        switch ($modo) {
            case 'historico':
                $query = PedidoVenda::with(['cliente', 'turma', 'itens.produto'])->where('turma_id', $turmaId);
                if ($request->filled('filtro_cliente')) $query->where('cliente_id', $request->filtro_cliente);
                if ($request->filled('filtro_status')) $query->where('status', $request->filtro_status);
                $pedidos = $query->orderBy('created_at', 'desc')->get();
                break;

            case 'novo':
                // Professor não cria pedido, só vê formulário vazio (ou poderíamos bloquear)
                break;

            default:
                $dadosStats = [
                    'total_pedidos' => PedidoVenda::where('turma_id', $turmaId)->count(),
                    'total_faturado' => PedidoVenda::where('turma_id', $turmaId)->where('status', 'Faturado')->sum('valor_total'),
                    'pedidos_abertos' => PedidoVenda::where('turma_id', $turmaId)->whereIn('status', ['Novo', 'Em Produção'])->count(),
                ];
                break;
        }

        return view('aluno.vendas.index', compact('pedidos', 'clientes', 'produtos', 'aluno', 'dataSimulacao', 'modo', 'dadosStats'));
    }

    // --- WMS ---
    // --- WMS ---
    public function verMapa($turmaId)
    {
        // Carrega a turma explicitamente
        $turma = Turma::findOrFail($turmaId);
        
        $aluno = $this->getAlunoSimulado($turmaId);
        
        // Busca materiais e locais filtrando pela turma
        $materiaisSemLocal = MateriaPrima::where('quantidade_estoque', '>', 0)
                                         ->whereNull('local_estoque_id')
                                         ->get(); // Se a matéria-prima for global, ok. Se for por turma, adicionar where('turma_id', $turmaId)
                                         
        $locais = LocalEstoque::with('materiaPrima')
                              ->where('turma_id', $turmaId)
                              ->orderBy('rua')
                              ->get()
                              ->groupBy('rua');
                              
        return view('aluno.almoxarifado.mapa', compact('materiaisSemLocal', 'locais', 'aluno', 'turma'));
    }

    public function verEstoque(Request $request, $turmaId)
    {
        // Carrega a turma explicitamente
        $turma = Turma::findOrFail($turmaId);
        
        $aluno = $this->getAlunoSimulado($turmaId);
        
        $query = MateriaPrima::with('local')->where('quantidade_estoque', '>', 0);
        
        if ($request->filled('busca')) {
            $termo = $request->busca;
            $query->where(fn($q)=>$q->where('nome', 'like', "%{$termo}%")->orWhere('sku', 'like', "%{$termo}%"));
        }
        
        $estoque = $query->orderBy('nome')->get();
        
        return view('aluno.almoxarifado.estoque', compact('estoque', 'aluno', 'turma'));
    }
    public function verEmbalagem($turmaId)
    {
        // Cria um objeto Aluno simulado
        $alunoSimulado = $this->getAlunoSimulado($turmaId);

        // Busca os dados REAIS da embalagem para essa turma
        $ordensParaEmbalar = \App\Models\OrdemProducao::with(['produto.materiasPrimas', 'pedido.cliente', 'apontamentos', 'solicitacoesMaterial'])
            ->whereHas('pedido', function($q) use ($turmaId) {
                $q->where('turma_id', $turmaId);
            })
            ->where('status', 'Concluída')
            ->where('embalado', false)
            ->orderBy('data_fim', 'asc')
            ->get();

        // Retorna a view do aluno, mas injetando o aluno simulado (modo espião)
        return view('aluno.embalagem.dashboard', [
            'aluno' => $alunoSimulado,
            'ordensParaEmbalar' => $ordensParaEmbalar
        ]);
    }
}