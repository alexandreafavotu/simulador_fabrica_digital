<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Aluno;
use App\Models\PedidoVenda;
use App\Models\OrdemProducao;
use App\Models\OrdemCompra;
use App\Models\Fornecedor;
use App\Models\MateriaPrima;
use App\Models\Cliente;
use App\Models\ConfiguracaoSimulacao;
use App\Models\ProdutoAcabado;
use App\Models\LocalEstoque;
use App\Models\SolicitacaoSeparacao;
use App\Models\ApontamentoProducao;
use App\Models\NotaFiscal;
use App\Models\DemandaMercado;
use App\Models\DemandaMercadoItem;

class AlunoController extends Controller
{
    // --- GESTÃO DE CLIENTES (ALUNO) ---
    public function gerenciarClientes()
    {
        $permissao = \App\Models\ConfiguracaoSimulacao::where('chave', 'aluno_cadastra_cliente')->first()->valor;
        if (!$permissao) abort(403, 'O Professor desativou o cadastro de clientes para alunos.');

        $aluno = $this->getAlunoAtivo(); // <--- AQUI A MÁGICA
        
        // CORREÇÃO: Traz os da turma OU os globais (nulos)
        $clientes = Cliente::where(function($query) use ($aluno) {
            $query->where('turma_id', $aluno->turma_id)
                  ->orWhereNull('turma_id');
        })->get();

        return view('aluno.clientes.index', compact('clientes', 'aluno'));
    }

    public function salvarCliente(Request $request)
    {
        // 1. Verifica Permissão (Lógica Original Mantida)
        $permissao = \App\Models\ConfiguracaoSimulacao::where('chave', 'aluno_cadastra_cliente')->first()->valor;
        if (!$permissao) abort(403, 'Acesso Negado.');

        // 2. Identifica o Aluno e a Turma ATIVA (Ajuste Crítico para Isolamento)
        // Isso garante que o cliente seja salvo na empresa que está rodando agora
        $aluno = $this->getAlunoAtivo();
        if (!$aluno) abort(403, 'Vínculo com a turma não encontrado.');

        // 3. Validação (Mantida sua estrutura original)
        $dados = $request->validate([
            'nome_razao_social' => 'required|string|max:255',
            'cnpj_cpf' => 'nullable|string|max:20',
            'telefone' => 'nullable|string|max:20',
            'cep' => 'nullable|string|max:9',
            'rua' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string',
            'estado' => 'nullable|string|max:2',
        ]);

        // 4. FORÇA VÍNCULO COM A TURMA ATIVA (Garante a segregação de dados)
        $dados['turma_id'] = $aluno->turma_id;

        Cliente::create($dados);

        return redirect()->route('aluno.clientes.index')
                         ->with('success', 'Cliente cadastrado na carteira da empresa: ' . $aluno->turma->nome_empresa);
    }

    // --- GESTÃO DE FORNECEDORES (ALUNO) ---
    public function gerenciarFornecedores()
    {
        $permissao = \App\Models\ConfiguracaoSimulacao::where('chave', 'aluno_cadastra_fornecedor')->first()->valor;
        if (!$permissao) abort(403, 'O Professor desativou o cadastro de fornecedores.');

        $aluno = $this->getAlunoAtivo(); // <--- AQUI A MÁGICA
        
        // CORREÇÃO: Traz os da turma OU os globais
        $fornecedores = Fornecedor::with('materiaPrima')
            ->where(function($query) use ($aluno) {
                $query->where('turma_id', $aluno->turma_id)
                      ->orWhereNull('turma_id');
            })->get();

        // Para o formulário, também precisamos filtrar as matérias-primas
        $materiasPrimas = MateriaPrima::where('turma_id', $aluno->turma_id)
                                      ->orWhereNull('turma_id')
                                      ->get(); 

        return view('aluno.fornecedores.index', compact('fornecedores', 'materiasPrimas', 'aluno'));
    }

    public function salvarFornecedor(Request $request)
    {
        // 1. Verifica Permissão (Lógica Original Mantida)
        $permissao = \App\Models\ConfiguracaoSimulacao::where('chave', 'aluno_cadastra_fornecedor')->first()->valor;
        if (!$permissao) abort(403, 'Acesso Negado.');

        // 2. Identifica o Aluno e a Turma ATIVA (Correção Crítica)
        // Isso garante que o fornecedor seja salvo na empresa correta do "Start"
        $aluno = $this->getAlunoAtivo();
        if (!$aluno) abort(403, 'Vínculo com a turma não encontrado.');

        // 3. Validação (Mantida 100% sua estrutura original)
        $dados = $request->validate([
            'nome_razao_social' => 'required|string|max:255',
            'cnpj_cpf' => 'nullable|string|max:20',
            'telefone' => 'nullable|string|max:20',
            'materia_prima_id' => 'nullable|exists:materias_primas,id',
            'preco_unitario' => 'required|numeric',
            'tempo_entrega_dias' => 'required|integer',
            'cep' => 'nullable|string|max:9',
            'rua' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string',
            'estado' => 'nullable|string|max:2',
        ]);

        // 4. FORÇA VÍNCULO COM A TURMA ATIVA (Sincronização de Dados)
        $dados['turma_id'] = $aluno->turma_id;

        Fornecedor::create($dados);

        // Retorno com feedback do nome da empresa
        return redirect()->route('aluno.fornecedores.index')
                         ->with('success', 'Fornecedor cadastrado para a empresa: ' . $aluno->turma->nome_empresa);
    }


    public function dashboardPcp(Request $request)
    {
        $user = Auth::user();
        $aluno = $this->getAlunoAtivo(); // <--- AQUI A MÁGICA
        if (!$aluno) abort(403, 'Aluno sem turma.');

        // 1. DADOS PADRÃO (Para os filtros)
        $clientes = Cliente::where('turma_id', $aluno->turma_id)->get();
        $produtos = ProdutoAcabado::where('turma_id', $aluno->turma_id)->get();;
        $dataJogo = $aluno->turma->data_jogo;

        // 2. DESCOBRE O MODO DE VISUALIZAÇÃO (Menu, Vendas, Produção ou Compras)
        $modo = $request->input('tabela', 'menu'); // Se não tiver nada, é 'menu'

        // 3. LOGICA SEPARADA POR TELA
        switch ($modo) {
            case 'vendas':
                // Carrega só vendas com filtro
                $query = PedidoVenda::with(['cliente', 'itens.produto'])
                            ->where('turma_id', $aluno->turma_id)
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
                // Carrega só produção com filtro
                $query = OrdemProducao::with(['produto', 'pedido.cliente'])
                            ->whereHas('pedido', function($q) use ($aluno) { $q->where('turma_id', $aluno->turma_id); });
                
                if ($request->filled('filtro_status_op')) $query->where('status', $request->filtro_status_op);
                
                $dados = $query->orderBy('created_at', 'desc')->get();
                break;

            case 'compras':
                // Inicia a busca baseada na turma
                $query = OrdemCompra::with(['materiaPrima', 'pedido'])
                            ->whereHas('pedido', function($q) use ($aluno) { $q->where('turma_id', $aluno->turma_id); });
                
                // 1. Filtro de Status
                if ($request->filled('filtro_status_compra')) {
                    $query->where('status', $request->filtro_status_compra);
                }

                // 2. Filtro de Nome do Material
                if ($request->filled('busca_material')) {
                    $termo = $request->busca_material;
                    $query->whereHas('materiaPrima', function($q) use ($termo) {
                        $q->where('nome', 'like', '%' . $termo . '%');
                    });
                }

                $dados = $query->orderBy('created_at', 'desc')->get();
                break;

            default: // MODO 'MENU' (Calcula apenas as contagens para os cards)
                $dados = [
                    'qtd_vendas' => PedidoVenda::where('turma_id', $aluno->turma_id)->where('status', 'Novo')->count(),
                    'qtd_producao' => OrdemProducao::whereHas('pedido', fn($q)=>$q->where('turma_id', $aluno->turma_id))->where('status', '!=', 'Concluída')->count(),
                    'qtd_compras' => OrdemCompra::whereHas('pedido', fn($q)=>$q->where('turma_id', $aluno->turma_id))->where('status', 'Pendente')->count(),
                ];
                break;
        }

        return view('aluno.pcp.dashboard', compact('aluno', 'dados', 'modo', 'clientes', 'produtos', 'dataJogo'));
    }



    

    public function analiseMRP($id)
{
    // 1. Identifica o Aluno e a Turma Ativa (Âncora de Segurança)
    $aluno = $this->getAlunoAtivo();
    if (!$aluno) abort(403, 'Acesso Negado: Vínculo com a turma não encontrado.');

    // 2. Busca o pedido, mas GARANTE que ele pertence à turma do aluno logado
    // Adicionamos a trava 'where' antes do findOrFail
    $pedido = PedidoVenda::with(['cliente', 'itens.produto.materiasPrimas'])
        ->where('turma_id', $aluno->turma_id) // <--- BLINDAGEM DE ISOLAMENTO
        ->findOrFail($id);

    // 3. Array para guardar o total de materiais necessários (Sua lógica original mantida)
    $necessidades = [];

    // 4. O Grande Loop do MRP (Sua lógica original mantida 100%)
    foreach ($pedido->itens as $itemPedido) {
        // Pega a quantidade vendida (Ex: 1000 canetas)
        $qtdProduto = $itemPedido->quantidade;

        // Olha a receita desse produto (se tiver)
        if ($itemPedido->produto->materiasPrimas) {
            foreach ($itemPedido->produto->materiasPrimas as $mp) {
                // Pega quanto vai de material em UMA unidade (vem da tabela pivo)
                $qtdPorUnidade = $mp->pivot->quantidade; 
                
                // Calcula o total (1000 * qtd_unitaria)
                $totalNecessario = $qtdProduto * $qtdPorUnidade;

                // Se já existe na lista, soma. Se não, cria.
                if (isset($necessidades[$mp->id])) {
                    $necessidades[$mp->id]['quantidade'] += $totalNecessario;
                } else {
                    $necessidades[$mp->id] = [
                        'nome' => $mp->nome,
                        'unidade' => $mp->unidade_medida,
                        'quantidade' => $totalNecessario,
                        'sku' => $mp->sku
                    ];
                }
            }
        }
    }

    // 5. Envia tudo para a view (agora mandamos $necessidades também)
    return view('detalhe', compact('pedido', 'necessidades'));
}

    public function gerarOrdens($id)
    {
        // 1. Identifica o Aluno e a Turma Ativa (A lógica que unifica o sistema)
        $aluno = $this->getAlunoAtivo();
        if (!$aluno) abort(403, 'Vínculo com a turma não encontrado.');

        // 2. Busca o pedido, mas GARANTE que ele pertence à turma do aluno logado
        $pedido = PedidoVenda::with(['itens.produto.materiasPrimas'])
            ->where('turma_id', $aluno->turma_id) // <--- TRAVA DE SEGURANÇA
            ->findOrFail($id);

        // 3. Verifica se já não foi processado (Lógica original preservada)
        if ($pedido->status != 'Novo') {
            return redirect()->back()->with('error', 'Este pedido já foi processado!');
        }

        foreach ($pedido->itens as $item) {
            // 4. Gera a Ordem de Produção (OP) - Para a Fábrica
            OrdemProducao::create([
                'pedido_venda_id' => $pedido->id,
                'produto_acabado_id' => $item->produto_acabado_id,
                'quantidade' => $item->quantidade,
                'status' => 'Aberta'
            ]);

            // 5. Gera as Ordens de Compra (OC) - Para o Almoxarifado/Compras
            if ($item->produto->materiasPrimas) {
                foreach ($item->produto->materiasPrimas as $mp) {
                    // Calcula: Qtd de Produtos * Qtd de Material por unidade
                    $qtdNecessaria = $item->quantidade * $mp->pivot->quantidade;

                    OrdemCompra::create([
                        'pedido_venda_id' => $pedido->id,
                        'materia_prima_id' => $mp->id,
                        'quantidade' => $qtdNecessaria,
                        'status' => 'Pendente' 
                    ]);
                }
            }
        }

        // 6. Atualiza status do Pedido (Lógica original preservada)
        $pedido->status = 'Em Produção';
        $pedido->save();

        return redirect()->route('aluno.pcp.dashboard')->with('success', 'Ordens de Produção e Compra geradas com sucesso!');
    }

    /**
     * Dashboard específico para o Aluno de Compras
     */
    public function dashboardCompras(Request $request)
    {
        $user = Auth::user();
        $aluno = $this->getAlunoAtivo(); // <--- AQUI A MÁGICA
        if (!$aluno) abort(403, 'Aluno não encontrado.');

        $modo = $request->input('tela', 'menu'); 
        $dados = [];
        $stats = [];

        switch ($modo) {
            case 'cotacao':
                $query = OrdemCompra::with(['materiaPrima', 'pedido.cliente'])
                            ->whereHas('pedido', function($q) use ($aluno) { $q->where('turma_id', $aluno->turma_id); })
                            ->where('status', 'Pendente');
                
                if ($request->filled('busca')) {
                    $termo = $request->busca;
                    $query->whereHas('materiaPrima', fn($q) => $q->where('nome', 'like', "%{$termo}%"));
                }
                // Urgentes primeiro
                $dados = $query->orderBy('urgente', 'desc')->orderBy('created_at', 'asc')->get();
                break;

            case 'recusados':
                $query = OrdemCompra::with(['materiaPrima', 'pedido', 'fornecedor'])
                            ->whereHas('pedido', function($q) use ($aluno) { $q->where('turma_id', $aluno->turma_id); })
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
                // NOVO: Filtro de Data
                if ($request->filled('filtro_data')) {
                    $query->whereDate('updated_at', $request->filtro_data);
                }

                $dados = $query->orderBy('updated_at', 'desc')->get();
                break;

            case 'historico':
                $query = OrdemCompra::with(['materiaPrima', 'pedido', 'fornecedor'])
                            ->whereHas('pedido', function($q) use ($aluno) { $q->where('turma_id', $aluno->turma_id); })
                            ->whereNotIn('status', ['Pendente', 'Recusado']);

                if ($request->filled('filtro_status')) $query->where('status', $request->filtro_status);
                if ($request->filled('busca')) {
                    $termo = $request->busca;
                    $query->whereHas('materiaPrima', fn($q) => $q->where('nome', 'like', "%{$termo}%"));
                }
                $dados = $query->orderBy('updated_at', 'desc')->get();
                break;

            default: // MODO MENU
                $stats = [
                    'pendentes' => OrdemCompra::whereHas('pedido', fn($q)=>$q->where('turma_id', $aluno->turma_id))->where('status', 'Pendente')->count(),
                    'chegando' => OrdemCompra::whereHas('pedido', fn($q)=>$q->where('turma_id', $aluno->turma_id))->where('status', 'Aguardando Entrega')->count(),
                    
                    // CORREÇÃO DO CONTADOR: Só conta se recompra_gerada for false (0)
                    'recusados' => OrdemCompra::whereHas('pedido', fn($q)=>$q->where('turma_id', $aluno->turma_id))
                                              ->where('status', 'Recusado')
                                              ->where('recompra_gerada', 0) 
                                              ->count(),
                ];
                break;
        }

        return view('aluno.compras.dashboard', compact('aluno', 'modo', 'dados', 'stats'));
    }

    // --- NOVOS MÉTODOS PARA COMPRAS ---

    /**
     * Passo 1 da Compra: Exibe a tela de seleção de fornecedor
     */
    /**
     * Passo 1 da Compra: Exibe a tela de seleção de fornecedor com filtros
     */
    /**
     * Passo 1 da Compra: Exibe a tela de seleção de fornecedor com filtros
     */
    public function confirmarCompra(Request $request, $id)
{
    // --- 1. BUSCA O ALUNO E A TURMA ATIVA (Âncora de Segurança) ---
    $aluno = $this->getAlunoAtivo(); 
    if (!$aluno) {
        abort(403, 'Aluno não encontrado ou sem turma ativa.');
    }

    // --- 2. BUSCA A ORDEM DE COMPRA COM TRAVA DE ISOLAMENTO ---
    // Agora o sistema verifica se o Pedido vinculado a esta OC pertence à turma do aluno
    $compra = OrdemCompra::with(['materiaPrima', 'pedido.cliente'])
        ->whereHas('pedido', function($q) use ($aluno) {
            $q->where('turma_id', $aluno->turma_id); // <--- BLINDAGEM HIERÁRQUICA
        })
        ->findOrFail($id);
    
    $turmaId = $aluno->turma_id;

    // 3. Query Base: Busca fornecedores ligados à TURMA DELE (ou Globais)
    // Mantida sua lógica, garantindo que veja os da turma ou os do sistema (null)
    $fornecedoresQuery = Fornecedor::where(function($q) use ($turmaId) {
                                        $q->where('turma_id', $turmaId)
                                          ->orWhereNull('turma_id');
                                    }) 
                                    ->where(function($query) use ($compra) {
                                        // Filtra se vende o material específico OU é genérico
                                        $query->where('materia_prima_id', $compra->materia_prima_id)
                                              ->orWhereNull('materia_prima_id');
                                    });
                                    
    // 4. Filtro Inteligente (Sua lógica original mantida 100%)
    if ($request->filled('filtro_nome')) {
        $termo = $request->filtro_nome;
        $fornecedoresQuery->where(function($q) use ($termo) {
            $q->where('nome_razao_social', 'like', '%' . $termo . '%')
              ->orWhereHas('materiaPrima', function($q2) use ($termo) {
                  $q2->where('nome', 'like', '%' . $termo . '%');
              });
        });
    }

    // 5. Ordenação (Sua lógica original mantida 100%)
    $ordenarPor = $request->input('ordenar_por', 'preco_unitario');
    $direcao = $request->input('direcao', 'asc');

    if ($ordenarPor == 'preco_unitario') {
        $fornecedoresQuery->orderBy('preco_unitario', $direcao);
    } elseif ($ordenarPor == 'tempo_entrega_dias') {
        $fornecedoresQuery->orderBy('tempo_entrega_dias', $direcao);
    }
    
    // 6. Executa a busca
    $fornecedores = $fornecedoresQuery->get();

    return view('aluno.compras.confirmar', compact('compra', 'fornecedores'));
}

    // --- NOVO: LÓGICA DE RE-COMPRA (RECUPERAÇÃO DE RECUSADOS) ---
    public function recomprarMaterial($id)
{
    // 1. Identifica o Aluno e a Turma Ativa (Âncora de Segurança)
    $aluno = $this->getAlunoAtivo();
    if (!$aluno) abort(403, 'Aluno não encontrado ou sem turma ativa.');

    // 2. Busca a compra recusada com TRAVA DE ISOLAMENTO
    // Validamos através do relacionamento com o pedido se a compra é desta turma
    $compraRecusada = OrdemCompra::with('materiaPrima')
        ->whereHas('pedido', function($q) use ($aluno) {
            $q->where('turma_id', $aluno->turma_id); // <--- BLINDAGEM DE POSSE
        })
        ->findOrFail($id);

    // 3. Validação de Status (Sua lógica original mantida)
    if ($compraRecusada->status != 'Recusado') {
        return redirect()->back()->with('error', 'Apenas itens recusados podem ser processados.');
    }

    // 4. Cria a NOVA solicitação (URGENTE) 
    // A segurança aqui é herdada do pedido_venda_id da compra anterior que já validamos
    $novaCompra = OrdemCompra::create([
        'pedido_venda_id' => $compraRecusada->pedido_venda_id,
        'materia_prima_id' => $compraRecusada->materia_prima_id,
        'quantidade' => $compraRecusada->quantidade,
        'status' => 'Pendente',
        'urgente' => true,
    ]);

    // 5. Marca a ANTIGA como "Resolvida" (Sua lógica original mantida)
    $compraRecusada->recompra_gerada = true;
    $compraRecusada->save();

    // 6. CORREÇÃO DO REDIRECIONAMENTO (Sua lógica original mantida)
    return redirect()->route('aluno.compras.confirmar', $novaCompra->id)
        ->with('success', "Recompra iniciada! Selecione o fornecedor urgente.");
}

    /**
     * Passo 2 da Compra: Salva o fornecedor e muda o status
     */
    
    public function efetivarCompra(Request $request, $id)
    {
        // 1. Identifica o Aluno e a Turma ATIVA (Sincronização total)
        $aluno = $this->getAlunoAtivo();
        if (!$aluno) abort(403, 'Vínculo com a turma não encontrado.');

        $request->validate([
            'fornecedor_id' => 'required|exists:fornecedores,id',
        ]);

        try {
            return DB::transaction(function () use ($request, $id, $aluno) {
                // 2. Busca a Ordem de Compra (OC), com trava e garantindo a posse da turma
                $compra = OrdemCompra::whereHas('pedido', function($q) use ($aluno) {
                        $q->where('turma_id', $aluno->turma_id);
                    })
                    ->lockForUpdate()
                    ->findOrFail($id);

                // Evita que a mesma compra seja efetivada duas vezes se o aluno clicar 2x rápido
                if ($compra->status != 'Pendente') {
                    return redirect()->route('aluno.compras.dashboard')
                        ->with('error', 'Esta ordem de compra já foi processada.');
                }

                // 3. Busca o Fornecedor (da turma ou Global)
                $fornecedor = Fornecedor::where(function($q) use ($aluno) {
                        $q->where('turma_id', $aluno->turma_id)->orWhereNull('turma_id');
                    })->findOrFail($request->fornecedor_id);

                // 4. Trava Financeira com trava no banco (saldo atualizado da turma)
                $turma = \App\Models\Turma::where('id', $aluno->turma_id)->lockForUpdate()->first();
                $custoTotal = $fornecedor->preco_unitario * $compra->quantidade;

                if ($turma->capital_atual < $custoTotal) {
                    return redirect()->back()->with('error', '❌ OPERAÇÃO CANCELADA: Saldo insuficiente em caixa para realizar esta compra. Custo: R$ ' . number_format($custoTotal, 2, ',', '.') . ' | Saldo: R$ ' . number_format($turma->capital_atual, 2, ',', '.'));
                }

                // Debita do caixa da empresa
                $turma->decrement('capital_atual', $custoTotal);

                // 5. Lógica de Tempo
                $dataJogo = $turma->data_jogo; 
                $prazoDias = (int)$fornecedor->tempo_entrega_dias;
                $dataEntrega = \Carbon\Carbon::parse($dataJogo)->addDays($prazoDias);

                // 6. Atualização segura da compra
                $compra->fornecedor_id = $request->fornecedor_id;
                
                // Grava aluno_id APENAS se a coluna existir no banco de dados do servidor
                if (Schema::hasColumn('ordens_compra', 'aluno_id')) {
                    $compra->aluno_id = $aluno->id;
                }
                
                $compra->data_entrega_prevista = $dataEntrega;
                $compra->status = 'Aguardando Entrega';
                $compra->save();

                return redirect()->route('aluno.compras.dashboard')->with('success', 
                    "Compra confirmada! Previsão de chegada: " . $dataEntrega->format('d/m/Y'));
            });
        } catch (\Exception $e) {
            logger()->error('Erro ao efetivar compra: ' . $e->getMessage());
            return redirect()->route('aluno.compras.dashboard')
                ->with('error', 'Ocorreu um erro ao processar a compra: ' . $e->getMessage());
        }
    }

    // --- GESTÃO DE MATÉRIAS-PRIMAS (ALUNO) ---

    public function gerenciarMateriasPrimas()
    {
        $permissao = \App\Models\ConfiguracaoSimulacao::where('chave', 'aluno_cadastra_materia_prima')->first()->valor;
        if (!$permissao) abort(403, 'Acesso Negado.');
        
        $aluno = $this->getAlunoAtivo(); // <--- AQUI A MÁGICA

        // CORREÇÃO: Filtra estritamente
        $materiasPrimas = MateriaPrima::where(function($query) use ($aluno) {
                $query->where('turma_id', $aluno->turma_id)
                      ->orWhereNull('turma_id');
            })->get();
        
        return view('aluno.materias-primas.index', compact('materiasPrimas'));
    }

    public function salvarMateriaPrima(Request $request)
    {
        // 1. Verifica Permissão (Lógica Original Mantida)
        $permissao = ConfiguracaoSimulacao::where('chave', 'aluno_cadastra_materia_prima')->first()->valor;
        if (!$permissao) abort(403, 'Acesso Negado.');

        // 2. Identifica o Aluno e a Turma ATIVA (Ajuste Crítico para Isolamento)
        $aluno = $this->getAlunoAtivo();
        if (!$aluno) abort(403, 'Aluno sem turma ativa ou vínculo não encontrado.');

        // 3. Valida (Lógica Original Mantida)
        $dados = $request->validate([
            'sku' => 'required|string|max:255', 
            'nome' => 'required|string|max:255',
            'unidade_medida' => 'required|string|max:10',
        ]);

        // 4. FORÇA O VÍNCULO COM A TURMA ATIVA (Sincronizado com o cabeçalho)
        $dados['turma_id'] = $aluno->turma_id;

        MateriaPrima::create($dados);

        return redirect()->route('aluno.materias-primas.index')
                         ->with('success', 'Material cadastrado para a empresa: ' . $aluno->turma->nome_empresa);
    }

    public function excluirMateriaPrima(MateriaPrima $materiaPrima)
    {
        // Verifica Permissão
        $permissao = ConfiguracaoSimulacao::where('chave', 'aluno_cadastra_materia_prima')->first()->valor;
        if (!$permissao) abort(403, 'Acesso Negado.');

        $materiaPrima->delete();
        return redirect()->route('aluno.materias-primas.index')->with('success', 'Material removido.');
    }
    // --- MÓDULO DE VENDAS (ALUNO) ---

    public function gerenciarVendas(Request $request)
    {
        // 1. Identifica o Aluno
        $aluno = $this->getAlunoAtivo(); // <--- AQUI A MÁGICA
        if (!$aluno) abort(403, 'Aluno sem turma.');

        // 2. Dados Globais
        $dataSimulacao = $aluno->turma->data_jogo;
        
        // 3. Define o Modo (Menu, Novo ou Histórico)
        $modo = $request->input('tela', 'menu'); // Padrão é 'menu'

        $clientes = [];
        $produtos = [];
        $pedidos = [];
        $dadosStats = [];

        // --- ETAPA 1: LÓGICA DE FILA E FOCO DO MURAL (MERCADO IA) ---
        
        // 1. Verifica se ESTE aluno já possui alguma reserva ativa dele pendente
        $minhaReservaAtiva = DemandaMercado::with(['cliente', 'itens.produto'])
            ->where('turma_id', $aluno->turma_id)
            ->where('aluno_id', $aluno->id)
            ->where('status', 'Pendente')
            ->first();

        if ($minhaReservaAtiva) {
            // Se ele já tem uma reserva dele, mostre APENAS ela (esconde todas as outras)
            $demandasMercado = collect([$minhaReservaAtiva]);
        } else {
            // Se não tem reserva, mostre apenas os pedidos do mercado que estão VAGOS (aluno_id nulo)
            // Isso impede que dois alunos cliquem ou vejam o mesmo pedido ao mesmo tempo!
            $demandasMercado = DemandaMercado::with(['cliente', 'itens.produto'])
                ->where('turma_id', $aluno->turma_id)
                ->whereNull('aluno_id')
                ->where('status', 'Pendente')
                ->get();
        }

        // Envia para a View
        view()->share('demandasMercado', $demandasMercado);
        // -----------------------------------------------------------

        switch ($modo) {
            case 'novo':
                // Carrega dados para o formulário
                $clientes = Cliente::where('turma_id', $aluno->turma_id)->get();
                
                // Filtro de Produtos
                $produtos = ProdutoAcabado::where(function($query) use ($aluno) {
                    $query->where('turma_id', $aluno->turma_id)
                          ->orWhereNull('turma_id');
                })->get();

                // --- CÁLCULO DO LIMITE PARA A VIEW (NOVO) ---
                $limite = $aluno->turma->limite_vendas_por_aluno ?? 10;
                $jáVendeu = \App\Models\PedidoVenda::where('turma_id', $aluno->turma_id)
                    ->where('aluno_id', $aluno->id)
                    ->whereDate('data_pedido', $dataSimulacao)
                    ->count();
                
                // Injetamos essas variáveis no array de stats para passar para a view
                $dadosStats['limite_info'] = [
                    'bloqueado' => $jáVendeu >= $limite,
                    'usado' => $jáVendeu,
                    'total' => $limite
                ];
                break;

            case 'historico':
                // Carrega dados para os filtros e a tabela
                $clientes = Cliente::where('turma_id', $aluno->turma_id)->get(); // Para o select de filtro
                
                // --- CORREÇÃO: ISOLAMENTO NO FILTRO TAMBÉM ---
                $produtos = ProdutoAcabado::where(function($query) use ($aluno) {
                    $query->where('turma_id', $aluno->turma_id)
                          ->orWhereNull('turma_id');
                })->get();

                $query = PedidoVenda::with(['cliente', 'turma', 'itens.produto'])
                            ->where('turma_id', $aluno->turma_id);

                // --- Filtros ---
                if ($request->filled('filtro_cliente')) {
                    $query->where('cliente_id', $request->filtro_cliente);
                }
                if ($request->filled('filtro_status')) {
                    $query->where('status', $request->filtro_status);
                }
                
                $pedidos = $query->orderBy('created_at', 'desc')->get();
                break;

            default: // MODO MENU
                // Estatísticas rápidas para os cards
                $dadosStats = [
                    'total_pedidos' => PedidoVenda::where('turma_id', $aluno->turma_id)->count(),
                    'total_faturado' => PedidoVenda::where('turma_id', $aluno->turma_id)->where('status', 'Faturado')->sum('valor_total'),
                    'pedidos_abertos' => PedidoVenda::where('turma_id', $aluno->turma_id)->whereIn('status', ['Novo', 'Em Produção'])->count(),
                ];
                break;
        }

        return view('aluno.vendas.index', compact('pedidos', 'clientes', 'produtos', 'aluno', 'dataSimulacao', 'modo', 'dadosStats'));
    }

    public function salvarVenda(Request $request)
    {
        $aluno = $this->getAlunoAtivo();
        if (!$aluno) {
            return redirect()->back()->with('error', 'Erro: Vínculo com a turma não encontrado.');
        }

        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'data_entrega_solicitada' => 'required|date',
            'itens' => 'required|array|min:1',
            'itens.*.produto_id' => 'required|exists:produtos_acabados,id',
            'itens.*.quantidade' => 'required|integer|min:1',
        ]);

        // 1. Limite diário por aluno
        $limiteDiario = $aluno->turma->limite_vendas_por_aluno ?? 10;
        
        $vendasHoje = 0;
        if (Schema::hasColumn('pedidos_venda', 'aluno_id')) {
            $vendasHoje = PedidoVenda::where('turma_id', $aluno->turma_id)
                ->where('aluno_id', $aluno->id)
                ->whereDate('data_pedido', $aluno->turma->data_jogo)
                ->count();
        }

        if ($vendasHoje >= $limiteDiario) {
            return redirect()->back()->withInput()->with('error', "⛔ MERCADO FECHADO PARA VOCÊ: Seu limite diário é de {$limiteDiario} pedidos. Avance o tempo para vender mais.");
        }

        // 2. Se a tabela demandas_mercado existir e houver demanda, valida usando comparação segura de datas (whereDate)
        $demandaCorrespondente = null;
        if (Schema::hasTable('demandas_mercado')) {
            $demandaQuery = DemandaMercado::where('cliente_id', $request->cliente_id)
                ->whereDate('data_entrega_solicitada', $request->data_entrega_solicitada)
                ->where('status', 'Pendente')
                ->where(function($q) use ($aluno) {
                    $q->where('aluno_id', $aluno->id)
                      ->orWhere('turma_id', $aluno->turma_id);
                })
                ->with('itens');

            $demandaCorrespondente = $demandaQuery->first();

            if ($demandaCorrespondente) {
                // Valida se a quantidade de produtos no formulário confere com a demanda
                if (count($request->itens) != $demandaCorrespondente->itens->count()) {
                    return redirect()->back()->withInput()->with('error', '❌ ERRO DE COMPOSIÇÃO: O número de itens informados não confere com a solicitação do mercado.');
                }

                // Valida produto e quantidade de cada item
                foreach ($request->itens as $itemDigitado) {
                    $itemCorreto = $demandaCorrespondente->itens()
                        ->where('produto_acabado_id', $itemDigitado['produto_id'])
                        ->where('quantidade', $itemDigitado['quantidade'])
                        ->exists();

                    if (!$itemCorreto) {
                        return redirect()->back()->withInput()->with('error', '❌ ERRO DE DADOS: O Produto selecionado ou a Quantidade informada está incorreta para este pedido.');
                    }
                }
            }
        }

        try {
            return DB::transaction(function () use ($request, $aluno, $demandaCorrespondente) {
                $dataSimulacao = $aluno->turma->data_jogo;
                $valorTotal = 0;
                $itensParaSalvar = [];

                foreach ($request->itens as $itemData) {
                    $produto = ProdutoAcabado::findOrFail($itemData['produto_id']);
                    $totalItem = $produto->preco_venda * $itemData['quantidade'];
                    $valorTotal += $totalItem;

                    $itensParaSalvar[] = [
                        'produto_acabado_id' => $itemData['produto_id'],
                        'quantidade' => $itemData['quantidade'],
                        'preco_unitario' => $produto->preco_venda,
                        'preco_total_item' => $totalItem,
                    ];
                }

                $dadosPedido = [
                    'cliente_id' => $request->cliente_id,
                    'turma_id' => $aluno->turma_id,
                    'data_pedido' => $dataSimulacao,
                    'data_entrega_solicitada' => $request->data_entrega_solicitada,
                    'valor_total' => $valorTotal,
                    'status' => 'Novo',
                ];

                if (Schema::hasColumn('pedidos_venda', 'aluno_id')) {
                    $dadosPedido['aluno_id'] = $aluno->id;
                }

                $pedido = PedidoVenda::create($dadosPedido);
                $pedido->itens()->createMany($itensParaSalvar);

                // Marca a demanda no mural como Atendida
                if ($demandaCorrespondente) {
                    $demandaCorrespondente->update(['status' => 'Atendido']);
                }

                return redirect()->route('aluno.vendas.index')
                    ->with('success', 'Pedido de Venda realizado na empresa: ' . $aluno->turma->nome_empresa);
            });
        } catch (\Exception $e) {
            logger()->error('Erro ao salvar venda: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Erro ao registrar venda: ' . $e->getMessage());
        }
    }
    // --- MÓDULO ALMOXARIFADO (WMS) ---

    public function dashboardAlmoxarifado(Request $request)
    {
        $user = Auth::user();
        $aluno = $this->getAlunoAtivo(); // <--- AQUI A MÁGICA
        if (!$aluno) abort(403, 'Aluno sem turma.');

        $hojeNoJogo = $aluno->turma->data_jogo;
        $modo = $request->input('tela', 'menu'); // Padrão: Menu (Recebimento)

        // Variáveis iniciais
        $entregas = [];
        $solicitacoes = [];
        $stats = [];

        switch ($modo) {
            case 'separacao':
                // Carrega a lista de Picking (O que a produção pediu)
                $solicitacoes = SolicitacaoSeparacao::with(['materiaPrima', 'ordemProducao.produto'])
                    ->whereHas('ordemProducao.pedido', function($q) use ($aluno) {
                        $q->where('turma_id', $aluno->turma_id);
                    })
                    ->where('status', 'Pendente')
                    ->get();
                break;

            default: // MODO MENU (Recebimento + Card de Acesso à Separação)
                // 1. INBOUND: Busca o que está chegando (Caminhão) para as colunas de cima
                $entregas = OrdemCompra::with(['materiaPrima', 'pedido', 'fornecedor'])
                    ->whereHas('pedido', function($q) use ($aluno) {
                        $q->where('turma_id', $aluno->turma_id);
                    })
                    ->where('status', 'Aguardando Entrega')
                    ->whereDate('data_entrega_prevista', '<=', $hojeNoJogo)
                    ->orderBy('updated_at', 'asc')
                    ->get();

                // 2. Contagem para o Card de Separação
                $qtdSeparacao = SolicitacaoSeparacao::whereHas('ordemProducao.pedido', function($q) use ($aluno) {
                        $q->where('turma_id', $aluno->turma_id);
                    })->where('status', 'Pendente')->count();

                $stats = ['qtd_separacao' => $qtdSeparacao];
                break;
        }

        return view('aluno.almoxarifado.dashboard', compact('entregas', 'solicitacoes', 'aluno', 'modo', 'stats'));
    }

    public function receberMaterial($id)
    {
        // 1. Identifica o Aluno e a Turma
        $aluno = $this->getAlunoAtivo();
        if (!$aluno) abort(403, 'Aluno não encontrado ou sem turma ativa.');

        // 2. Trava de Caos
        if ($this->verificarBloqueio($aluno, 'almoxarifado')) {
            return redirect()->back()->with('error', 'AÇÃO BLOQUEADA: O Almoxarifado está fechado para inventário/auditoria.');
        }

        try {
            return DB::transaction(function () use ($id, $aluno) {
                // 3. Busca a Ordem de Compra com trava de banco
                $compra = OrdemCompra::whereHas('pedido', function($q) use ($aluno) {
                        $q->where('turma_id', $aluno->turma_id); 
                    })
                    ->lockForUpdate()
                    ->findOrFail($id);

                // 4. Validação (impede duplo recebimento se 2 alunos clicarem juntos)
                if ($compra->status != 'Aguardando Entrega') {
                    return redirect()->back()->with('error', 'Esta compra já foi recebida ou não está pronta.');
                }

                // 5. ATUALIZAÇÃO DE ESTOQUE (ENTRADA) COM TRAVA NA MATÉRIA-PRIMA
                $materiaPrima = MateriaPrima::where('id', $compra->materia_prima_id)->lockForUpdate()->first();
                if ($materiaPrima) {
                    $materiaPrima->increment('quantidade_estoque', $compra->quantidade);
                }

                // 6. FINALIZA A COMPRA
                $compra->status = 'Concluído'; 
                
                if (Schema::hasColumn('ordens_compra', 'aluno_id')) {
                    $compra->aluno_id = $aluno->id;
                }

                $compra->save();

                return redirect()->back()->with('success', 'Material recebido e armazenado! Verifique a lista de Picking para atender a produção.');
            });
        } catch (\Exception $e) {
            logger()->error('Erro ao receber material: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao processar recebimento: ' . $e->getMessage());
        }
    }
    // --- LÓGICA DE RECUSA DE MATERIAL ---

    // 1. Exibe o formulário de justificativa
    public function formRecusa($id)
{
    // 1. Identifica o Aluno e a Turma Ativa (Âncora de Segurança)
    $aluno = $this->getAlunoAtivo();
    if (!$aluno) abort(403, 'Aluno não encontrado ou sem turma ativa.');

    // 2. Busca a Ordem de Compra com TRAVA DE ISOLAMENTO
    // Isso impede que um aluno abra o formulário de recusa de uma carga de outra turma
    $compra = OrdemCompra::with(['materiaPrima', 'fornecedor'])
        ->whereHas('pedido', function($q) use ($aluno) {
            $q->where('turma_id', $aluno->turma_id); // <--- BLINDAGEM DE POSSE
        })
        ->findOrFail($id);

    return view('aluno.almoxarifado.recusar', compact('compra'));
}

    // 2. Processa a recusa
    public function salvarRecusa(Request $request, $id)
{
    // --- 1. IDENTIFICA O ALUNO E A TURMA ATIVA (Âncora de Segurança) ---
    $aluno = $this->getAlunoAtivo();
    if (!$aluno) abort(403, 'Aluno não encontrado ou sem turma ativa.');

    // --- 2. TRAVA DE SEGURANÇA (CAOS) ---
    // Agora usando o $aluno garantido pela âncora inteligente
    if ($this->verificarBloqueio($aluno, 'almoxarifado')) {
        return redirect()->route('aluno.almoxarifado.dashboard')
            ->with('error', 'AÇÃO BLOQUEADA: O Almoxarifado está fechado para inventário/auditoria.');
    }
    // ---------------------------------

    $request->validate([
        'motivo_recusa' => 'required|string|max:500',
    ]);

    // --- 3. BUSCA A ORDEM DE COMPRA COM TRAVA DE ISOLAMENTO ---
    // Garante que o aluno só possa recusar materiais da sua própria turma
    $compra = OrdemCompra::whereHas('pedido', function($q) use ($aluno) {
            $q->where('turma_id', $aluno->turma_id); // <--- BLINDAGEM DE POSSE
        })->findOrFail($id);
    
    // 4. Aplica a Recusa (Sua lógica original mantida)
    $compra->status = 'Recusado'; 
    $compra->motivo_recusa = $request->motivo_recusa;

    // --- 5. LÓGICA DE DATA DO JOGO (Sua regra de ouro preservada) ---
    // Forçamos a data da ocorrência para ser a data do simulador
    $compra->updated_at = $aluno->turma->data_jogo;
    
    // Importante: Desligamos o relógio automático do Laravel
    // para não sobrescrever com a data real do servidor
    $compra->timestamps = false; 
    // -----------------------------------------------

    $compra->save();

    // Mensagem original mantida
    return redirect()->route('aluno.almoxarifado.dashboard')
        ->with('success', 'Material recusado. A notificação foi enviada ao setor de Compras.');
}
    // --- MAPA DE ARMAZENAGEM (WMS) ---

    // --- MAPA DE ARMAZENAGEM (WMS) ---
public function mapaEstoque()
{
    // 1. Identifica o aluno e a turma ativa (Sincronizado)
    $aluno = $this->getAlunoAtivo(); 
    if (!$aluno) abort(403);

    // 2. Materiais "no chão" - AJUSTADO PARA INCLUIR GLOBAIS
    // Agora o sistema olha para o que é da turma DELE ou o que é GLOBAL
    $materiaisSemLocal = MateriaPrima::where(function($q) use ($aluno) {
                                        $q->where('turma_id', $aluno->turma_id)
                                          ->orWhereNull('turma_id'); // <--- ESSENCIAL: Permite ver materiais padrão
                                     })
                                     ->where('quantidade_estoque', '>', 0)
                                     ->whereNull('local_estoque_id')
                                     ->get();

    // 3. O Mapa Completo (Locais da Turma)
    // Blindagem tripla: Local é da turma, e o material dentro dele também deve ser filtrado
    $locais = LocalEstoque::with(['materiaPrima' => function($q) use ($aluno) {
                                // Garante que se houver um material no local, ele seja da turma ou global
                                $q->where('turma_id', $aluno->turma_id)
                                  ->orWhereNull('turma_id'); 
                              }])
                              ->where('turma_id', $aluno->turma_id) // Só prateleiras desta empresa
                              ->orderBy('rua')
                              ->orderBy('coluna')
                              ->orderBy('vao')
                              ->orderBy('nivel')
                              ->get()
                              ->groupBy('rua');

    // Retorna a view garantindo que o objeto $aluno vá junto
    return view('aluno.almoxarifado.mapa', compact('materiaisSemLocal', 'locais', 'aluno'));
}
    

    public function armazenarMaterial(Request $request, $id)
    {
        $aluno = $this->getAlunoAtivo();
        if (!$aluno) abort(403, 'Aluno não encontrado ou sem turma ativa.');

        if ($this->verificarBloqueio($aluno, 'almoxarifado')) {
            return redirect()->back()->with('error', 'AÇÃO BLOQUEADA: O armazém está fechado para movimentação (Inventário).');
        }

        $request->validate([
            'local_id' => 'required|exists:locais_estoque,id'
        ]);

        try {
            return DB::transaction(function () use ($request, $id, $aluno) {
                $material = MateriaPrima::where(function($q) use ($aluno) {
                        $q->where('turma_id', $aluno->turma_id)
                          ->orWhereNull('turma_id');
                    })
                    ->lockForUpdate()
                    ->findOrFail($id);

                $local = LocalEstoque::where('turma_id', $aluno->turma_id)
                    ->lockForUpdate()
                    ->findOrFail($request->local_id);

                if ($local->ocupado) {
                    return redirect()->back()->with('error', 'Este local já está ocupado!');
                }

                $material->local_estoque_id = $local->id;
                $material->save();

                $local->ocupado = true;
                $local->save();

                return redirect()->back()->with('success', "Material armazenado em {$local->codigo_visual}!");
            });
        } catch (\Exception $e) {
            logger()->error('Erro ao armazenar material: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao armazenar material: ' . $e->getMessage());
        }
    }
    // --- CONSULTA DE ESTOQUE (INVENTÁRIO) ---
    public function estoqueAlmoxarifado(Request $request)
{
    // 1. Identifica o aluno e a turma ativa PRIMEIRO
    $aluno = $this->getAlunoAtivo();
    if (!$aluno) abort(403, 'Vínculo não encontrado.');

    // 2. Busca materiais com saldo positivo, mas RESTRITO à turma do aluno
    $query = MateriaPrima::with('local')
                         ->where('turma_id', $aluno->turma_id) // <--- O ISOLAMENTO ESTÁ AQUI
                         ->where('quantidade_estoque', '>', 0);

    // 3. Filtro de Busca (Nome ou SKU) - Mantido 100% igual
    if ($request->filled('busca')) {
        $termo = $request->busca;
        $query->where(function($q) use ($termo) {
            $q->where('nome', 'like', "%{$termo}%")
              ->orWhere('sku', 'like', "%{$termo}%");
        });
    }

    $estoque = $query->orderBy('nome')->get();

    // 4. Retorno para a View com os dados isolados
    return view('aluno.almoxarifado.estoque', compact('estoque', 'aluno'));
}

    // --- MÓDULO DE PRODUÇÃO (CHÃO DE FÁBRICA) ---

    public function dashboardProducao(Request $request)
    {
        $user = Auth::user();
        $aluno = $this->getAlunoAtivo(); // <--- AQUI A MÁGICA
        if (!$aluno) abort(403, 'Aluno sem turma.');

        $dataJogo = $aluno->turma->data_jogo;
        $modo = $request->input('tela', 'producao'); 

        // --- NOVO: AUTO-REPARO DE MÁQUINAS (CAOS) ---
        // Verifica se existem máquinas quebradas nesta turma
        $opsQuebradas = OrdemProducao::where('em_manutencao', true)
            ->whereHas('pedido', function($q) use ($aluno) {
                $q->where('turma_id', $aluno->turma_id);
            })
            ->get();

        foreach ($opsQuebradas as $opQ) {
            // Se a data do jogo for MAIOR ou IGUAL a previsão de conserto...
            if ($opQ->previsao_conserto_ate && \Carbon\Carbon::parse($dataJogo)->gte($opQ->previsao_conserto_ate)) {
                // ... conserta a máquina automaticamente.
                $opQ->em_manutencao = false;
                $opQ->motivo_manutencao = null;
                $opQ->previsao_conserto_ate = null;
                $opQ->save();
            }
        }
        // ---------------------------------------------

        $ordensDisponiveis = [];
        $minhasOrdens = [];
        $historico = [];

        switch ($modo) {
            case 'historico':
                // (Mantido igual - Lógica de relatório)
                $queryHistorico = ApontamentoProducao::with(['ordem.produto', 'ordem.pedido.cliente', 'aluno'])
                    ->whereHas('ordem.pedido', function($q) use ($aluno) {
                        $q->where('turma_id', $aluno->turma_id);
                    });
                
                if ($request->filled('filtro_data')) $queryHistorico->whereDate('data_apontamento', $request->filtro_data);
                if ($request->filled('filtro_produto')) {
                    $termo = $request->filtro_produto;
                    $queryHistorico->whereHas('ordem.produto', fn($q)=>$q->where('nome', 'like', "%{$termo}%"));
                }
                if ($request->filled('filtro_operador')) {
                    $termo = $request->filtro_operador;
                    $queryHistorico->whereHas('aluno', fn($q)=>$q->where('nome', 'like', "%{$termo}%"));
                }
                $historico = $queryHistorico->orderBy('data_apontamento', 'desc')->get();
                break;

            default: 
                // 1. Ordens Disponíveis (Pool) - Carregando Materiais para validação de estoque
                $ordensDisponiveis = OrdemProducao::with(['produto.materiasPrimas', 'pedido.cliente'])
                    ->whereHas('pedido', function($q) use ($aluno) {
                        $q->where('turma_id', $aluno->turma_id);
                    })
                    ->where('status', 'Aberta')
                    ->whereNull('aluno_id') 
                    ->orderBy('created_at', 'asc')
                    ->get();

                // 2. Linha Ativa (FÁBRICA INTEIRA)
                // O aluno vê tudo o que está acontecendo na turma (para bater com o painel do professor)
                $minhasOrdens = OrdemProducao::with(['produto', 'pedido', 'aluno'])
                    ->whereHas('pedido', function($q) use ($aluno) {
                        $q->where('turma_id', $aluno->turma_id);
                    })
                    ->whereNotNull('aluno_id') // Alguém pegou
                    ->where('status', '!=', 'Concluída') // Não acabou
                    ->orderBy('status', 'desc') // Em produção primeiro
                    ->get();
                break;
        }

        return view('aluno.producao.dashboard', compact('ordensDisponiveis', 'minhasOrdens', 'aluno', 'dataJogo', 'historico', 'modo'));
    }
    // --- AÇÕES DA PRODUÇÃO ---

    public function assumirOrdem($id)
{
    // --- 1. IDENTIFICA O ALUNO E A TURMA ATIVA (Âncora de Segurança) ---
    $aluno = $this->getAlunoAtivo();
    if (!$aluno) abort(403, 'Aluno não encontrado ou sem turma ativa.');

    // 2. CONTAGEM ATUAL (Suas ordens em andamento)
    // Mantida sua lógica: Limite por Aluno para não travar a fábrica toda
    $minhasOrdensAtivas = OrdemProducao::where('aluno_id', $aluno->id)
                                       ->where('status', '!=', 'Concluída')
                                       ->count();

    // 3. PEGA O LIMITE DEFINIDO PELO PROFESSOR NA TURMA ATIVA
    $limite = $aluno->turma->capacidade_producao;

    if ($minhasOrdensAtivas >= $limite) {
        return redirect()->back()->with('error', "Capacidade cheia! O limite atual é de {$limite} ordens por aluno.");
    }

    // --- 4. BUSCA A ORDEM (OP) COM TRAVA DE ISOLAMENTO ---
    // Agora o sistema valida se o Pedido desta OP pertence à turma do aluno
    $op = OrdemProducao::whereHas('pedido', function($q) use ($aluno) {
            $q->where('turma_id', $aluno->turma_id); // <--- BLINDAGEM DE POSSE
        })->findOrFail($id);

    // 5. Verificação de Disponibilidade (Sua lógica original mantida)
    if ($op->aluno_id != null) {
        return back()->with('error', 'Já pegaram essa!');
    }

    // 6. Vincula o Aluno à Ordem e Salva
    $op->aluno_id = $aluno->id;
    $op->save();

    return redirect()->back()->with('success', 'Ordem assumida!');
}
    public function solicitarMaterial($id)
{
    // --- 1. IDENTIFICA O ALUNO E A TURMA ATIVA (MANTIDO) ---
    $aluno = $this->getAlunoAtivo();
    if (!$aluno) abort(403, 'Aluno não encontrado ou sem turma ativa.');

    // --- 2. BUSCA A ORDEM (OP) (MANTIDO) ---
    $op = \App\Models\OrdemProducao::with(['produto.materiasPrimas', 'apontamentos'])
        ->whereHas('pedido', function($q) use ($aluno) {
            $q->where('turma_id', $aluno->turma_id);
        })
        ->findOrFail($id);

    // 3. Verificação de Status (MANTIDO)
    // Nota: Aceitamos se for 'Pendente' ou se o sistema de refugo tiver deixado em 'Aguardando'
    if ($op->status_material != 'Pendente' && $op->status_material != 'Aguardando') {
        return redirect()->back()->with('error', 'Material já foi solicitado.');
    }

    // --- 4. CÁLCULO DO SALDO REAL (MANTIDO) ---
    $totalBoasAteAgora = $op->apontamentos->sum('quantidade_produzida');
    $saldoAFazer = $op->quantidade - $totalBoasAteAgora;

    if ($saldoAFazer <= 0) {
        return redirect()->back()->with('error', 'Esta ordem já foi concluída ou não possui saldo pendente.');
    }

    // --- 5. GERA AS SOLICITAÇÕES (COM TRAVA ANTI-DUPLICIDADE) ---
    $novasSolicitacoes = 0;

    foreach ($op->produto->materiasPrimas as $mp) {
        $qtdNecessaria = $saldoAFazer * $mp->pivot->quantidade;

        // >>> AQUI ESTÁ A CORREÇÃO <<<
        // Verifica se JÁ EXISTE uma solicitação aberta para este item nesta OP
        // (Isso evita duplicar o que o processo de refugo já gerou automaticamente)
        $jaExiste = \App\Models\SolicitacaoSeparacao::where('ordem_producao_id', $op->id)
            ->where('materia_prima_id', $mp->id)
            ->where('status', '!=', 'Entregue') // Se não foi entregue, ainda está valendo
            ->exists();

        if (!$jaExiste) {
            \App\Models\SolicitacaoSeparacao::create([
                'ordem_producao_id' => $op->id,
                'materia_prima_id' => $mp->id,
                'quantidade_solicitada' => $qtdNecessaria,
                'aluno_solicitante_id' => $aluno->id,
                'status' => 'Pendente'
            ]);
            $novasSolicitacoes++;
        }
    }

    // 6. Muda o status visual da OP (MANTIDO)
    $op->status_material = 'Solicitado';
    $op->save();

    // Feedback ajustado para ser mais preciso
    if ($novasSolicitacoes == 0) {
        return redirect()->back()->with('success', "Status atualizado! As solicitações de material já haviam sido geradas pelo processo de refugo.");
    }

    return redirect()->back()->with('success', "Solicitação enviada! Pedimos material para repor as {$saldoAFazer} unidade(s) pendente(s).");
}

    // --- LÓGICA DE PICKING (SEPARAÇÃO) ---

public function iniciarSeparacao($id)
    {
        // 1. Identifica o aluno e a turma ativa PRIMEIRO
        $aluno = $this->getAlunoAtivo();
        if (!$aluno) abort(403);

        // 2. Busca a solicitação, mas GARANTE que ela pertence à turma do aluno
        // Isso impede que um aluno acesse o picking de outra turma pelo ID na URL
        $solicitacao = \App\Models\SolicitacaoSeparacao::with('materiaPrima.local')
            ->whereHas('ordemProducao.pedido', function($q) use ($aluno) {
                $q->where('turma_id', $aluno->turma_id);
            })
            ->findOrFail($id);

        // 3. Carrega o Mapa filtrando estritamente pela turma do aluno
        $locais = \App\Models\LocalEstoque::with(['materiaPrima' => function($q) use ($aluno) {
                                    $q->where('turma_id', $aluno->turma_id); 
                                  }])
                              ->where('turma_id', $aluno->turma_id)
                              ->orderBy('rua')->orderBy('coluna')->orderBy('vao')->orderBy('nivel')
                              ->get()
                              ->groupBy('rua');

        // 4. Define o ALVO e a RUA ALVO
        $alvoLocalId = $solicitacao->materiaPrima->local_estoque_id;

        if (!$alvoLocalId) {
            return redirect()->back()->with('error', 'ERRO: Este material ainda não foi endereçado (está no chão). Guarde-o primeiro!');
        }

        $localAlvo = \App\Models\LocalEstoque::find($alvoLocalId);
        $ruaAlvo = $localAlvo->rua;

        // Mantemos a coleção vazia para a View não dar erro de variável indefinida
        $materiaisSemLocal = collect([]); 

        // 5. Retorna a view com a garantia de que o $aluno correto está sendo passado
        return view('aluno.almoxarifado.mapa', compact('locais', 'solicitacao', 'alvoLocalId', 'materiaisSemLocal', 'ruaAlvo', 'aluno'));
    }

    public function confirmarSeparacao(Request $request, $id)
    {
        $aluno = $this->getAlunoAtivo();
        if (!$aluno) abort(403, 'Aluno não encontrado ou sem turma ativa.');

        if ($this->verificarBloqueio($aluno, 'almoxarifado')) {
            return redirect()->back()->with('error', 'AÇÃO BLOQUEADA: Separação suspensa por auditoria/inventário.');
        }

        try {
            return DB::transaction(function () use ($id, $aluno) {
                $solicitacao = SolicitacaoSeparacao::with(['materiaPrima', 'ordemProducao'])
                    ->whereHas('ordemProducao.pedido', function($q) use ($aluno) {
                        $q->where('turma_id', $aluno->turma_id);
                    })
                    ->lockForUpdate()
                    ->findOrFail($id);

                if ($solicitacao->status == 'Entregue') {
                    return redirect()->route('aluno.almoxarifado.dashboard')->with('error', 'Esta solicitação já foi entregue.');
                }

                $material = MateriaPrima::where('id', $solicitacao->materia_prima_id)->lockForUpdate()->first();

                if (!$material || $material->quantidade_estoque < $solicitacao->quantidade_solicitada) {
                    return redirect()->back()->with('error', 'Saldo insuficiente no endereço!');
                }

                $material->quantidade_estoque -= $solicitacao->quantidade_solicitada;
                $material->save();

                $solicitacao->status = 'Entregue';
                $solicitacao->save();

                $op = OrdemProducao::where('id', $solicitacao->ordem_producao_id)->lockForUpdate()->first();

                $pendencias = SolicitacaoSeparacao::where('ordem_producao_id', $op->id)
                    ->where('status', '!=', 'Entregue')
                    ->count();

                if ($pendencias == 0) {
                    $op->status_material = 'Entregue';
                    $op->save();
                    $msg = 'Todos os materiais foram separados! A produção está autorizada a iniciar.';
                } else {
                    $msg = "Item separado. Faltam {$pendencias} outros itens para liberar a máquina.";
                }

                return redirect()->route('aluno.almoxarifado.dashboard')->with('success', $msg);
            });
        } catch (\Exception $e) {
            logger()->error('Erro ao confirmar separação: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao confirmar separação: ' . $e->getMessage());
        }
    }

    public function iniciarProducao($id)
    {
        $aluno = $this->getAlunoAtivo();
        if (!$aluno) abort(403, 'Aluno não encontrado ou sem turma ativa.');

        try {
            return DB::transaction(function () use ($id, $aluno) {
                $op = OrdemProducao::whereHas('pedido', function($q) use ($aluno) {
                        $q->where('turma_id', $aluno->turma_id);
                    })
                    ->lockForUpdate()
                    ->findOrFail($id);

                if ($op->em_manutencao) {
                    return redirect()->back()->with('error', 'MÁQUINA EM MANUTENÇÃO: Falha técnica detectada. Chame a manutenção (Professor).');
                }

                if ($op->status_material != 'Entregue') {
                    return redirect()->back()->with('error', 'Você precisa do material para começar!');
                }

                if ($op->status == 'Em Produção') {
                    return redirect()->back()->with('error', 'A máquina já está em produção!');
                }

                $op->status = 'Em Produção';
                $op->data_inicio_real = $aluno->turma->data_jogo;
                
                if (Schema::hasColumn('ordens_producao', 'aluno_id')) {
                    $op->aluno_id = $aluno->id;
                }

                $op->save();

                return redirect()->back()->with('success', 'Máquina iniciada! Acompanhe o progresso.');
            });
        } catch (\Exception $e) {
            logger()->error('Erro ao iniciar produção: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao iniciar produção: ' . $e->getMessage());
        }
    }

    // --- APONTAMENTO DE PRODUÇÃO ---

    public function formApontamento($id)
    {
        $aluno = $this->getAlunoAtivo();
        if (!$aluno) abort(403, 'Aluno não encontrado ou sem turma ativa.');

        $op = OrdemProducao::with('produto')
            ->whereHas('pedido', function($q) use ($aluno) {
                $q->where('turma_id', $aluno->turma_id);
            })
            ->findOrFail($id);
        
        if ($op->status != 'Em Produção') {
            return redirect()->route('aluno.producao.dashboard')
                ->with('error', 'Esta ordem não pode ser apontada agora.');
        }

        return view('aluno.producao.apontar', compact('op'));
    }

    public function salvarApontamento(Request $request, $id)
    {
        $aluno = $this->getAlunoAtivo();
        if (!$aluno) abort(403, 'Aluno não encontrado ou sem turma ativa.');

        $request->validate([
            'quantidade_produzida' => 'required|integer|min:0',
            'quantidade_refugo' => 'required|integer|min:0',
        ]);

        try {
            return DB::transaction(function () use ($request, $id, $aluno) {
                $op = OrdemProducao::with(['produto.materiasPrimas', 'pedido'])
                    ->whereHas('pedido', function($q) use ($aluno) {
                        $q->where('turma_id', $aluno->turma_id);
                    })
                    ->lockForUpdate()
                    ->findOrFail($id);

                if ($op->status != 'Em Produção') {
                    return redirect()->route('aluno.producao.dashboard')
                        ->with('error', 'Esta ordem não pode ser apontada agora.');
                }

                // 3. REGISTRA O APONTAMENTO
                ApontamentoProducao::create([
                    'ordem_producao_id' => $op->id,
                    'aluno_id' => $aluno->id,
                    'data_apontamento' => $aluno->turma->data_jogo,
                    'quantidade_produzida' => $request->quantidade_produzida,
                    'quantidade_refugo' => $request->quantidade_refugo,
                ]);

                // 4. CÁLCULO DE SALDO
                $totalBoasAteAgora = $op->apontamentos()->sum('quantidade_produzida');
                $faltamParaMeta = $op->quantidade - $totalBoasAteAgora;

                // 5. SE TEVE REFUGO E AINDA FALTA PRODUZIR -> GERA REPOSIÇÃO
                if ($request->quantidade_refugo > 0 && $faltamParaMeta > 0) {
                    $receitaUnica = $op->produto->materiasPrimas->groupBy('id');

                    foreach ($receitaUnica as $idMp => $colecaoMateriais) {
                        $mp = $colecaoMateriais->first();
                        $qtdNaReceita = $colecaoMateriais->sum(fn($m) => $m->pivot->quantidade);
                        $qtdPerdida = $request->quantidade_refugo * $qtdNaReceita;

                        // 1. CRIA O PICKING (SOLICITAÇÃO WMS)
                        SolicitacaoSeparacao::create([
                            'ordem_producao_id' => $op->id,
                            'materia_prima_id' => $mp->id,
                            'quantidade_solicitada' => $qtdPerdida,
                            'aluno_solicitante_id' => $aluno->id,
                            'status' => 'Pendente'
                        ]);

                        // 2. VERIFICA ESTOQUE E GERA COMPRA SE NECESSÁRIO
                        if ($mp->quantidade_estoque >= $qtdPerdida) {
                            $op->status_material = 'Solicitado';
                            
                            OrdemCompra::create([
                                'pedido_venda_id' => $op->pedido_venda_id,
                                'materia_prima_id' => $mp->id,
                                'quantidade' => $qtdPerdida,
                                'status' => 'Pendente',
                                'urgente' => false
                            ]);
                        } else {
                            OrdemCompra::create([
                                'pedido_venda_id' => $op->pedido_venda_id,
                                'materia_prima_id' => $mp->id,
                                'quantidade' => $qtdPerdida,
                                'status' => 'Pendente',
                                'urgente' => true
                            ]);
                            
                            $op->status_material = 'Pendente'; 
                        }
                    }
                }

                // 6. ATUALIZAÇÃO DE STATUS DA OP
                if ($totalBoasAteAgora >= $op->quantidade) {
                    $op->status = 'Concluída';
                    $op->data_fim = $aluno->turma->data_jogo;
                    $op->embalado = false;
                    $msg = "Produção finalizada! Lote enviado para conferência na Embalagem.";
                } else {
                    $op->status = 'Aberta'; 
                    $op->data_inicio_real = null;
                    
                    if ($op->status_material == 'Solicitado') {
                        $msg = "Refugo registrado. Solicitamos {$request->quantidade_refugo} un de material ao Almoxarifado para reposição.";
                    } else if ($request->quantidade_refugo > 0) {
                        $msg = "Refugo registrado. Sem estoque! Solicitamos compra urgente de {$request->quantidade_refugo} un de material.";
                    } else {
                        $msg = "Apontamento registrado com sucesso!";
                    }
                }

                $op->quantidade_perda += $request->quantidade_refugo;
                
                // --- LIMPEZA DA SABOTAGEM (O aluno cumpriu a ordem, então destravamos a máquina) ---
                if (isset($op->tem_refugo_forcado) && $op->tem_refugo_forcado) {
                    $op->tem_refugo_forcado = false;
                    $op->qtd_refugo_forcado = 0;
                    $op->motivo_refugo_forcado = null;
                }

                $op->save();

                return redirect()->route('aluno.producao.dashboard')->with('success', $msg);
            });
        } catch (\Exception $e) {
            logger()->error('Erro ao salvar apontamento: ' . $e->getMessage());
            return redirect()->route('aluno.producao.dashboard')
                ->with('error', 'Erro ao registrar apontamento: ' . $e->getMessage());
        }
    }
    // --- MÓDULO EXPEDIÇÃO (FATURAMENTO) ---

    public function dashboardExpedicao(Request $request)
    {
        $user = Auth::user();
        $aluno = $this->getAlunoAtivo(); // <--- AQUI A MÁGICA
        if (!$aluno) abort(403, 'Aluno sem turma.');

        $modo = $request->input('tela', 'menu'); 
        
        $pedidosParaFaturar = [];
        $notasEmitidas = [];
        $stats = [];
        $clientes = Cliente::where('turma_id', $aluno->turma_id)->get();

        switch ($modo) {
           case 'faturar':
                // 1. Pedidos Prontos (Status 'Em Produção')
                $query = PedidoVenda::with(['cliente', 'itens.produto', 'ordensProducao'])
                    ->where('turma_id', $aluno->turma_id)
                    ->where('status', 'Em Produção')
                    // REGRA 1: O pedido precisa ter pelo menos uma OP gerada (evita pedidos vazios)
                    ->whereHas('ordensProducao') 
                    // REGRA 2: O pedido NÃO pode ter nenhuma OP que ainda não foi embalada
                    ->whereDoesntHave('ordensProducao', function ($q) {
                        // Verificamos se é falso OU se é nulo (proteção para dados antigos)
                        $q->where('embalado', false)->orWhereNull('embalado');
                    });

                if ($request->filled('filtro_cliente')) {
                    $query->where('cliente_id', $request->filtro_cliente);
                }

                $pedidosParaFaturar = $query->orderBy('data_entrega_solicitada', 'asc')->get();
                break;

            case 'historico':
                $query = NotaFiscal::with('pedido.cliente')
                     ->whereHas('pedido', function($q) use ($aluno) {
                          $q->where('turma_id', $aluno->turma_id);
                     });

                if ($request->filled('busca')) {
                    $termo = $request->busca;
                    $query->where(function($q) use ($termo) {
                        $q->where('numero_nota', 'like', "%{$termo}%")
                          ->orWhereHas('pedido.cliente', fn($sq) => $sq->where('nome_razao_social', 'like', "%{$termo}%"));
                    });
                }

                $notasEmitidas = $query->orderBy('created_at', 'desc')->get();
                break;

            default: 
                // Atualização do contador para refletir apenas pedidos 100% embalados
                $countFaturar = PedidoVenda::where('turma_id', $aluno->turma_id)
                    ->where('status', 'Em Produção')
                    ->whereDoesntHave('ordensProducao', function ($query) {
                        $query->where('embalado', false);
                    })->count();

                $countNotas = NotaFiscal::whereHas('pedido', function($q) use ($aluno) {
                        $q->where('turma_id', $aluno->turma_id);
                    })->count();

                $stats = [
                    'a_faturar' => $countFaturar,
                    'total_notas' => $countNotas
                ];
                break;
        }

        return view('aluno.expedicao.dashboard', compact('pedidosParaFaturar', 'notasEmitidas', 'aluno', 'modo', 'stats', 'clientes'));
    }
    // --- TELA DE CONFERÊNCIA (PRÉ-FATURAMENTO) ---
    public function conferirExpedicao($id)
{
    // --- 1. IDENTIFICA O ALUNO E A TURMA ATIVA (Âncora de Segurança) ---
    $aluno = $this->getAlunoAtivo();
    if (!$aluno) abort(403, 'Aluno não encontrado ou sem turma ativa.');

    // --- 2. BUSCA O PEDIDO COM TRAVA DE ISOLAMENTO ---
    // Garante que o Pedido de Venda pertence à turma do aluno logado
    $pedido = PedidoVenda::with('itens.produto')
        ->where('turma_id', $aluno->turma_id) // <--- BLINDAGEM DE POSSE
        ->findOrFail($id);
    
    // 3. Verifica se tem estoque para cada item (Sua lógica original mantida)
    foreach($pedido->itens as $item) {
        // Adiciona uma propriedade temporária para a tela saber se falta material
        $item->estoque_atual = $item->produto->quantidade_estoque;
        $item->tem_saldo = $item->produto->quantidade_estoque >= $item->quantidade;
    }

    return view('aluno.expedicao.conferencia', compact('pedido'));
}

    // --- NOVA FUNÇÃO: FATURAR PEDIDO (COM BAIXA DE ESTOQUE) ---
    public function faturarPedido(Request $request, $id)
    {
        // 1. IDENTIFICA O ALUNO E A TURMA ATIVA
        $aluno = $this->getAlunoAtivo();
        if (!$aluno) abort(403, 'Aluno não encontrado ou sem turma ativa.');

        // 2. TRAVAS DE SEGURANÇA (CAOS)
        if ($this->verificarBloqueio($aluno, 'expedicao')) {
            return redirect()->back()->with('error', 'AÇÃO BLOQUEADA: Expedição suspensa (Greve de Transportes/Logística).');
        }

        if ($this->verificarBloqueio($aluno, 'faturamento')) {
            return redirect()->back()->with('error', 'AÇÃO BLOQUEADA: Sistema de Faturamento fora do ar (Erro de TI).');
        }

        try {
            return DB::transaction(function () use ($request, $id, $aluno) {
                // 3. BUSCA O PEDIDO COM TRAVA DE BANCO
                $pedido = PedidoVenda::with('itens.produto')
                    ->where('turma_id', $aluno->turma_id)
                    ->lockForUpdate()
                    ->findOrFail($id);

                if ($pedido->status == 'Faturado') {
                    return redirect()->route('aluno.expedicao.dashboard')
                        ->with('error', 'Este pedido já foi faturado anteriormente.');
                }

                $qtdFaturar = $request->input('qtd_faturar', []); 
                $valorTotalNota = 0;

                // 4. Baixa o Estoque com Trava
                foreach ($pedido->itens as $item) {
                    $qtdSaida = $qtdFaturar[$item->id] ?? 0;

                    if ($qtdSaida > 0) {
                        $produto = ProdutoAcabado::where('id', $item->produto_acabado_id)->lockForUpdate()->first();
                        if (!$produto || $produto->quantidade_estoque < $qtdSaida) {
                            throw new \Exception("Estoque insuficiente de {$item->produto->nome} para faturar.");
                        }

                        $produto->decrement('quantidade_estoque', $qtdSaida);
                        $valorTotalNota += ($qtdSaida * $item->preco_unitario);
                    }
                }

                if ($valorTotalNota == 0) {
                    return redirect()->back()->with('error', 'Nenhum item foi selecionado para faturamento.');
                }

                // Crédito no caixa da empresa
                $turma = \App\Models\Turma::where('id', $aluno->turma_id)->lockForUpdate()->first();
                $turma->increment('capital_atual', $valorTotalNota);

                // 5. Gera a Nota Fiscal (com verificação de coluna para o banco de dados)
                $dadosNota = [
                    'pedido_venda_id' => $pedido->id,
                    'numero_nota' => str_pad($pedido->id + 5000, 6, '0', STR_PAD_LEFT),
                    'serie' => '1',
                    'valor_total' => $valorTotalNota,
                    'data_emissao' => $turma->data_jogo,
                    'chave_acesso' => fake()->numerify('3523##123456780001##55001000000###12345678'),
                ];

                if (Schema::hasColumn('notas_fiscais', 'aluno_id')) {
                    $dadosNota['aluno_id'] = $aluno->id;
                }

                NotaFiscal::create($dadosNota);

                // 6. Encerra o Pedido
                $pedido->status = 'Faturado';
                $pedido->save();

                return redirect()->route('aluno.expedicao.dashboard')->with('success', 'Nota Fiscal emitida com sucesso! Estoque baixado.');
            });
        } catch (\Exception $e) {
            logger()->error('Erro ao faturar pedido: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Falha no faturamento: ' . $e->getMessage());
        }
    }
    // --- VISUALIZAR NOTA FISCAL (DANFE) ---
  
    public function visualizarNota($id)
{
    // --- 1. IDENTIFICA O ALUNO E A TURMA ATIVA (Âncora de Segurança) ---
    $aluno = $this->getAlunoAtivo();
    if (!$aluno) abort(403, 'Aluno não encontrado ou sem turma ativa.');

    // --- 2. BUSCA A NOTA FISCAL COM TRAVA DE ISOLAMENTO ---
    // Garante que a Nota pertence a um Pedido desta Turma
    $nota = NotaFiscal::with(['pedido.cliente', 'pedido.itens.produto', 'pedido.turma'])
                      ->whereHas('pedido', function($q) use ($aluno) {
                          $q->where('turma_id', $aluno->turma_id); // <--- BLINDAGEM DE POSSE
                      })
                      ->findOrFail($id);

    // 3. GERAÇÃO DO CÓDIGO DE BARRAS REAL EM SVG (Sua lógica original mantida)
    $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
    
    // Gera o código de barras diretamente como código SVG
    // 2 = largura das barras, 30 = altura
    $barcode = $generator->getBarcode($nota->chave_acesso, $generator::TYPE_CODE_128);

    return view('aluno.expedicao.nota_fiscal', compact('nota', 'barcode'));
}
    // =========================================================================
    //  FUNÇÃO AUXILIAR DE SEGURANÇA (O GUARDIÃO DO CAOS)
    //  Cole isso no final do arquivo, antes da última chave }
    // =========================================================================
    private function verificarBloqueio($aluno, $setor)
    {
        $turma = $aluno->turma;
        if (!$turma) return false;

        // Usa a data do jogo, não a data real
        $dataJogo = \Carbon\Carbon::parse($turma->data_jogo);
        $colunaBloqueio = null;

        // Mapeia qual coluna do banco olhar
        if ($setor == 'almoxarifado') $colunaBloqueio = 'bloqueio_almoxarifado_ate';
        if ($setor == 'expedicao')    $colunaBloqueio = 'bloqueio_expedicao_ate';
        if ($setor == 'faturamento')  $colunaBloqueio = 'bloqueio_faturamento_ate';

        $dataDesbloqueio = $turma->$colunaBloqueio;

        // Se existe uma data de bloqueio definida...
        // E a data atual do jogo ainda é MENOR que a data de desbloqueio...
        if ($dataDesbloqueio && $dataJogo->lt(\Carbon\Carbon::parse($dataDesbloqueio))) {
            return true; // RETORNA VERDADEIRO: ESTÁ TRAVADO!
        }

        return false; // RETORNA FALSO: ESTÁ LIVRE
    }
    public function verPerfilEmpresa()
    {
        $user = Auth::user();

        // 1. Tenta pegar a turma que está ATIVA
        $aluno = Aluno::where('user_id', $user->id)
            ->whereHas('turma', function($q) {
                $q->where('jogo_ativo', true);
            })
            ->with('turma')
            ->first();

        // 2. Fallback: Se não achar ativa, pega a última
        if (!$aluno) {
            $aluno = Aluno::where('user_id', $user->id)
                ->with('turma')
                ->latest('id')
                ->first();
        }
        
        if (!$aluno || !$aluno->turma) {
            abort(403, 'ALUNO NÃO VINCULADO A UMA EMPRESA OU TURMA INVÁLIDA.');
        }

        $turma = $aluno->turma;
        
        return view('perfil_empresa', compact('turma'));
    }
// =========================================================================
    //  MÓDULO DE EMBALAGEM (PACKING & CONSOLIDAÇÃO)
    // =========================================================================

    /**
     * Dashboard do Embalador: Mostra lotes que saíram da produção
     * mas ainda não foram consolidados para venda.
     */
    public function dashboardEmbalagem(Request $request)
{
    $aluno = $this->getAlunoAtivo(); // <--- AQUI A MÁGICA
    if (!$aluno) abort(403);

    // Buscamos as OPs concluídas com os dados de quem as produziu e os materiais entregues
    $ordensParaEmbalar = OrdemProducao::with([
            'produto.materiasPrimas', 
            'pedido.cliente', 
            'apontamentos',
            'solicitacoesMaterial' // Carrega as solicitações de material desta OP
        ])
        ->whereHas('pedido', function($q) use ($aluno) {
            $q->where('turma_id', $aluno->turma_id);
        })
        ->where('status', 'Concluída')
        ->where('embalado', false)
        ->get();

    return view('aluno.embalagem.dashboard', compact('aluno', 'ordensParaEmbalar'));
}

    /**
     * Finaliza o processo de embalagem, soma o produto no estoque
     * e libera o pedido para o Dock de Saída (Expedição).
     */
    public function confirmarEmbalagem(Request $request, $id)
    {
        $aluno = $this->getAlunoAtivo();
        if (!$aluno) abort(403, 'Aluno não encontrado ou sem turma ativa.');

        try {
            return DB::transaction(function () use ($id, $aluno) {
                $op = OrdemProducao::with(['produto', 'apontamentos'])
                    ->whereHas('pedido', function($q) use ($aluno) {
                        $q->where('turma_id', $aluno->turma_id);
                    })
                    ->lockForUpdate()
                    ->findOrFail($id);

                if ($op->embalado) {
                    return redirect()->back()->with('error', 'Este lote já foi embalado anteriormente.');
                }

                $op->embalado = true;
                $op->data_embalagem = $aluno->turma->data_jogo;
                $op->save();

                $qtdFinalProduzida = $op->apontamentos->sum('quantidade_produzida');

                $produto = ProdutoAcabado::where('id', $op->produto_id)->lockForUpdate()->first();
                if ($produto) {
                    $produto->quantidade_estoque += $qtdFinalProduzida;
                    $produto->save();
                }

                return redirect()->route('aluno.embalagem.dashboard')
                    ->with('success', "Lote OP #{$op->id} consolidado! {$qtdFinalProduzida} unidades entraram no estoque final.");
            });
        } catch (\Exception $e) {
            logger()->error('Erro ao confirmar embalagem: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao embalar lote: ' . $e->getMessage());
        }
    }
    /**
     * FUNÇÃO CENTRALIZADA PARA PEGAR O ALUNO CORRETO
     * Prioriza a turma com 'jogo_ativo = true'.
     * Se não houver, pega a última criada.
     */
    private function getAlunoAtivo()
    {
        $user = Auth::user();
        
        // 1. Prioridade: Turma Ativa
        $aluno = Aluno::where('user_id', $user->id)
            ->whereHas('turma', function($q) {
                $q->where('jogo_ativo', true);
            })
            ->with('turma')
            ->first();

        // 2. Fallback: Última Turma
        if (!$aluno) {
            $aluno = Aluno::where('user_id', $user->id)
                ->with('turma')
                ->latest('id')
                ->first();
        }

        return $aluno;
    }
    
public function painelGestaoVista($turma_id = null)
{
    $user = Auth::user();
    if ($user->tipo == 'professor' && $turma_id) {
        $turma = \App\Models\Turma::findOrFail($turma_id);
        $aluno = (object)['turma_id' => $turma->id, 'turma' => $turma];
    } else {
        $aluno = $this->getAlunoAtivo();
    }
    if (!$aluno) abort(403);

    $dataJogo = $aluno->turma->data_jogo;

    // 1. PEDIDOS ATIVOS
    $pedidos = PedidoVenda::with([
            'cliente', 'itens.produto.materiasPrimas', 
            'ordensProducao.apontamentos', 'ordensProducao.solicitacoesMaterial'
        ])
        ->where('turma_id', $aluno->turma_id)
        ->whereNotIn('status', ['Faturado', 'Cancelado'])
        ->orderBy('id', 'desc')
        ->get();

    foreach ($pedidos as $p) {
        $entrega = \Carbon\Carbon::parse($p->data_entrega_solicitada);
        $p->dias_restantes = (int) $dataJogo->diffInDays($entrega, false);
        $cores = ['bg-blue-50', 'bg-green-50', 'bg-yellow-50', 'bg-purple-50', 'bg-pink-50', 'bg-orange-50'];
        $p->cor_grupo = $cores[$p->id % count($cores)];
        // Garante que compras seja sempre uma coleção, mesmo que vazia
        $p->compras = OrdemCompra::with('materiaPrima')->where('pedido_venda_id', $p->id)->get() ?? collect();
    }

    // 2. HISTÓRICO (CONCLUÍDOS)
    $historico = PedidoVenda::with([
            'cliente', 'itens.produto.materiasPrimas', 
            'ordensProducao.apontamentos'
        ])
        ->where('turma_id', $aluno->turma_id)
        ->where('status', 'Faturado')
        ->orderBy('updated_at', 'desc')
        ->take(10)
        ->get();

    foreach ($historico as $h) {
        $h->cor_grupo = 'bg-gray-100';
        $h->compras = OrdemCompra::with('materiaPrima')->where('pedido_venda_id', $h->id)->get() ?? collect();
    }

    return view('aluno.gestao_vista', compact('aluno', 'pedidos', 'historico', 'dataJogo'));
}
// 7. Forçar Inconformidade em uma Carga (WMS)
    public function sabotarCarga(Request $request, $turmaId)
    {
        $request->validate([
            'ordem_compra_id' => 'required|exists:ordens_compra,id',
            'motivo' => 'required|string',
        ]);

        $oc = OrdemCompra::findOrFail($request->ordem_compra_id);
        
        // Ativa a bandeira de erro e salva a pista para o aluno
        $oc->tem_inconformidade = true;
        $oc->descricao_inconformidade = $request->motivo;
        $oc->save();

        return back()->with('error', "INCONFORMIDADE GERADA: A OC #{$oc->id} agora está com defeito programado!");
    }

    public function assumirDemanda($id)
    {
        $aluno = $this->getAlunoAtivo();
        if (!$aluno) abort(403, 'Aluno não encontrado ou sem turma ativa.');

        if (!Schema::hasTable('demandas_mercado')) {
            return back()->with('error', 'O módulo de Demandas de Mercado não está ativo no banco de dados.');
        }

        try {
            return DB::transaction(function () use ($id, $aluno) {
                $jaVendeuHoje = 0;
                if (Schema::hasColumn('pedidos_venda', 'aluno_id')) {
                    $jaVendeuHoje = PedidoVenda::where('aluno_id', $aluno->id)
                        ->whereDate('data_pedido', $aluno->turma->data_jogo)
                        ->count();
                }
                
                $jaAssumiuMasNaoLancou = DemandaMercado::where('aluno_id', $aluno->id)
                    ->where('status', 'Pendente')
                    ->count();

                if (($jaVendeuHoje + $jaAssumiuMasNaoLancou) >= ($aluno->turma->limite_vendas_por_aluno ?? 10)) {
                    return back()->with('error', 'Sua cota de trabalho diária está cheia. Finalize os pendentes ou aguarde o próximo dia.');
                }

                $demanda = DemandaMercado::lockForUpdate()->findOrFail($id);
                
                if ($demanda->aluno_id) {
                    return back()->with('error', 'Este pedido já foi assumido por outro vendedor!');
                }

                $demanda->update(['aluno_id' => $aluno->id]);
                return back()->with('success', 'Pedido reservado! Agora você pode lançá-lo no sistema.');
            });
        } catch (\Exception $e) {
            logger()->error('Erro ao assumir demanda: ' . $e->getMessage());
            return back()->with('error', 'Erro ao assumir demanda: ' . $e->getMessage());
        }
    }
}