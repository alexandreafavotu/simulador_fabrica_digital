<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Turma;
use App\Models\Aluno;
use App\Models\MateriaPrima;
use App\Models\ProdutoAcabado;
use App\Models\ConfiguracaoSimulacao;
use App\Models\Cliente;
use App\Models\PedidoVenda;
use App\Models\PedidoVendaItem;
use App\Models\Fornecedor;
use Carbon\Carbon;
use App\Models\LocalEstoque;
use Illuminate\Support\Facades\Schema;

class ProfessorController extends Controller
{
    // --- PAINEL DE CONTROLE (DASHBOARD) ---
    public function index(Request $request)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);
        
        $configuracoes = ConfiguracaoSimulacao::all()->pluck('valor', 'chave');
        
        // 1. Lógica do Buscador de Turmas
        $query = Turma::doProfessor()->withCount('alunos');

        // --- NOVA LINHA: Oculta turmas arquivadas deste painel ---
        $query->where('arquivada', false);
        // --------------------------------------------------------

        if ($request->filled('busca_turma')) {
            $termo = $request->busca_turma;
            $query->where(function($q) use ($termo) {
                $q->where('nome', 'like', "%{$termo}%")
                  ->orWhere('nome_empresa', 'like', "%{$termo}%");
            });
        }

        // Ordenação: Ativas primeiro, depois por data
        $turmas = $query->orderBy('jogo_ativo', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->get();

        // 2. Cálculo Real das Estatísticas
        // Ajustei para contar apenas as que não estão arquivadas, para bater com a visualização
        $stats = [
            'total_alunos' => User::where('tipo', 'aluno')->count(),
            
            'turmas_ativas' => Turma::doProfessor()
                                    ->where('jogo_ativo', true)
                                    ->where('arquivada', false) // Só conta se não estiver arquivada
                                    ->count(),
                                    
            'total_turmas' => Turma::doProfessor()
                                   ->where('arquivada', false) // Só conta se não estiver arquivada
                                   ->count(),
        ];

        return view('professor.dashboard', compact('configuracoes', 'turmas', 'stats'));
    }

    // =========================================================================
    //  GESTÃO GLOBAL DE ALUNOS (BANCO DE TALENTOS)
    // =========================================================================

    public function gerenciarCadastroGlobal()
    {
        if (Auth::user()->tipo == 'aluno') abort(403);
        $todosAlunos = User::where('tipo', 'aluno')->orderBy('name')->get();
        return view('professor.global.alunos_index', compact('todosAlunos'));
    }

    public function salvarAlunoGlobal(Request $request)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            // Validação 'email:rfc,dns' verifica se o formato é válido E se o domínio (ex: gmail.com) existe de verdade
            'email' => 'required|email:rfc,dns|max:255|unique:users', 
            'curso' => 'required|string|max:255',
            'ano_letivo' => 'required|string|max:10',
            // Mudamos o nome do campo na validação para ser genérico (3 dígitos)
            'rg_finais' => 'required|string|size:3', 
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'tipo' => 'aluno',
            'curso' => $request->curso,
            'ano_letivo' => $request->ano_letivo,
            'rg_finais' => $request->rg_finais, // Guardamos os 3 dígitos para referência
            // A senha nasce: Senai + os 3 números escolhidos
            'password' => Hash::make('Senai' . $request->rg_finais), 
        ]);

        return redirect()->route('professor.global.alunos')->with('success', 'Aluno cadastrado! Senha inicial: Senai' . $request->rg_finais);
    }

    public function resetarSenhaAluno(User $user)
    {
        // NOVA REGRA: Resetar para a senha padrão fixa "Senaisp"
        $user->password = Hash::make('Senaisp');
        $user->save();
        
        return back()->with('success', 'Senha resetada para o padrão: Senaisp');
    }

    public function excluirAlunoGlobal(User $user)
    {
        Aluno::where('user_id', $user->id)->delete();
        $user->delete();
        return back()->with('success', 'Aluno removido do sistema.');
    }

    // =========================================================================
    //  GESTÃO DE EQUIPE DA TURMA
    // =========================================================================

    public function gerenciarEquipeTurma($turmaId)
    {
        $turma = Turma::findOrFail($turmaId);
        $alunosNaTurma = Aluno::with('user')->where('turma_id', $turmaId)->get();
        $idsNaTurma = $alunosNaTurma->pluck('user_id');

        $disponiveis = User::where('tipo', 'aluno')
                           ->where('ativo', true) // <--- ÚNICA MUDANÇA: FILTRA APENAS OS ATIVOS
                           ->whereNotIn('id', $idsNaTurma)
                           ->orderBy('name')
                           ->get();

        return view('professor.turmas.equipe', compact('turma', 'alunosNaTurma', 'disponiveis'));
    }

    public function vincularAluno(Request $request, $turmaId)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $usuario = User::findOrFail($request->user_id);

        Aluno::create([
            'user_id'  => $usuario->id,
            'turma_id' => $turmaId,
            'nome'     => $usuario->name, 
            'setor'    => 'indefinido',
        ]);

        return back()->with('success', 'Aluno adicionado à turma com sucesso!');
    }

    public function definirSetor(Request $request, $alunoId)
    {
        $aluno = Aluno::findOrFail($alunoId);
        $aluno->setor = $request->setor;
        $aluno->save();
        return back()->with('success', "Setor atualizado!");
    }

    public function desvincularAluno($alunoId)
    {
        $aluno = Aluno::findOrFail($alunoId);
        $aluno->delete(); 
        return back()->with('success', 'Aluno removido da turma.');
    }

    // =========================================================================
    //  MÉTODOS DE GESTÃO (TURMAS, PRODUTOS, ETC)
    // =========================================================================

    public function salvarRegras(Request $request)
    {
        $regras = ['aluno_cadastra_cliente', 'aluno_cadastra_fornecedor', 'aluno_cadastra_materia_prima'];
        foreach ($regras as $regra) {
            ConfiguracaoSimulacao::where('chave', $regra)->update(['valor' => $request->has($regra)]);
        }
        return redirect()->route('professor.dashboard');
    }

    public function gerenciarTurmas()
    {
        if (Auth::user()->tipo == 'aluno') abort(403);
        
        // Busca TODAS as turmas do professor
        $todasTurmas = Turma::doProfessor()
                       ->withCount('alunos')
                       ->orderBy('created_at', 'desc')
                       ->get();

        // Separa em duas listas
        $turmasAtivas = $todasTurmas->where('arquivada', false);
        $turmasArquivadas = $todasTurmas->where('arquivada', true);

        return view('professor.turmas_index', compact('turmasAtivas', 'turmasArquivadas'));
    }

    public function arquivarTurma(Turma $turma)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);

        // Inverte o status (Se dorme, acorda. Se acordada, dorme).
        $turma->arquivada = !$turma->arquivada;
        
        // Se for adormecer, forçamos o jogo a pausar também por segurança
        if($turma->arquivada) {
            $turma->jogo_ativo = false;
        }

        $turma->save();

        $status = $turma->arquivada ? 'adormecida (arquivada)' : 'reativada';
        return back()->with('success', "A fábrica foi {$status}!");
    }

    public function salvarTurma(Request $request)
    {
        // 1. VALIDAÇÃO DOS DADOS
        $request->validate([
            'nome' => 'required|string|max:255',
            'nome_empresa' => 'required|string|max:255',
            'setor' => 'nullable|string|max:255',
            'segmento' => 'nullable|string|max:255',
            'resumo' => 'nullable|string',
            'cnpj' => 'nullable|string|max:20',
            'telefone' => 'nullable|string|max:20',
            'rua' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
        ]);

        // 2. CRIAÇÃO COM OS NOVOS CAMPOS
        Turma::create([
            'professor_id' => Auth::id(),
            'nome' => $request->nome,
            'nome_empresa' => $request->nome_empresa,
            'setor' => $request->setor,
            'segmento' => $request->segmento,
            'resumo' => $request->resumo,
            'cnpj' => $request->cnpj,
            'telefone' => $request->telefone,
            'rua' => $request->rua,
            'numero' => $request->numero,
            'bairro' => $request->bairro,
            'cidade' => $request->cidade,
            'estado' => $request->estado,
            'data_jogo' => now(), 
            'jogo_ativo' => false,
            'arquivada' => false,
            'capacidade_producao' => 10
        ]);

        return redirect()->route('professor.turmas.index')->with('success', 'Fábrica e Perfil Corporativo criados com sucesso!');
    }

    public function editarTurma(Turma $turma)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);
        return view('professor.turmas_edit', ['turma' => $turma]);
    }

    public function atualizarTurma(Request $request, Turma $turma)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);

        $request->validate([
            'nome' => 'required|string|max:255',
            'nome_empresa' => 'required|string|max:255',
            'setor' => 'nullable|string|max:255',
            'segmento' => 'nullable|string|max:255',
            'resumo' => 'nullable|string',
            'cnpj' => 'nullable|string|max:20',
            'telefone' => 'nullable|string|max:20',
            'rua' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
        ]);

        // Atualiza todos os campos de uma vez
        $turma->update($request->all());

        return redirect()->route('professor.turmas.index')->with('success', 'Perfil da empresa atualizado com sucesso!');
    }

    

    // IMPORTANTE: Adicione esta linha no topo do arquivo se não tiver:
    // use Illuminate\Support\Facades\Schema;
    
    public function excluirTurma(Turma $turma)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);

        // 1. DESATIVA AS REGRAS DO BANCO (MODO DEUS)
        // Isso impede o erro "Integrity constraint violation"
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

        try {
            // === LIMPEZA GERAL ===

            // A. Desvincular Alunos
            $turma->alunos()->delete();

            // B. Coletar IDs de Pedidos (Incluindo os que estão na lixeira - withTrashed)
            // Isso garante que pegamos ATÉ O QUE ESTÁ ESCONDIDO
            $pedidosIds = \App\Models\PedidoVenda::withTrashed()
                ->where('turma_id', $turma->id)
                ->pluck('id');

            if ($pedidosIds->count() > 0) {
                // Limpa filhos dos pedidos
                \App\Models\OrdemCompra::whereIn('pedido_venda_id', $pedidosIds)->delete();
                \App\Models\NotaFiscal::whereIn('pedido_venda_id', $pedidosIds)->delete();
                \App\Models\PedidoVendaItem::whereIn('pedido_venda_id', $pedidosIds)->forceDelete();
                
                // Limpa OPs e dependências
                $opsIds = \App\Models\OrdemProducao::whereIn('pedido_venda_id', $pedidosIds)->pluck('id');
                if ($opsIds->count() > 0) {
                    \App\Models\SolicitacaoSeparacao::whereIn('ordem_producao_id', $opsIds)->delete();
                    \App\Models\ApontamentoProducao::whereIn('ordem_producao_id', $opsIds)->delete();
                    \App\Models\OrdemProducao::whereIn('id', $opsIds)->delete();
                }

                // Apaga os Pedidos (Force Delete para sumir da lixeira também)
                \App\Models\PedidoVenda::whereIn('id', $pedidosIds)->forceDelete();
            }

            // C. Apagar Cadastros Base (Agora vai funcionar pois desligamos as regras)
            \App\Models\Cliente::where('turma_id', $turma->id)->forceDelete();
            \App\Models\Fornecedor::where('turma_id', $turma->id)->forceDelete();
            \App\Models\MateriaPrima::where('turma_id', $turma->id)->forceDelete();
            \App\Models\ProdutoAcabado::where('turma_id', $turma->id)->forceDelete();
            \App\Models\LocalEstoque::where('turma_id', $turma->id)->delete();

            // D. TCHAU TURMA
            $turma->delete();

        } catch (\Exception $e) {
            // Se der erro, reativa as regras antes de mostrar o erro
            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
            return back()->with('error', 'Erro crítico ao excluir: ' . $e->getMessage());
        }

        // 2. REATIVA AS REGRAS DO BANCO
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        return redirect()->route('professor.turmas.index')
            ->with('success', 'Turma excluída com sucesso! (Regras de FK ignoradas para limpeza total).');
    }

    

    public function salvarMateriaPrima(Request $request)
    {
        $request->validate([
            'sku' => 'required|string|max:255', // Unique removido
            'nome' => 'required|string|max:255',
            'unidade_medida' => 'required|string|max:255',
        ]);
        
        MateriaPrima::create([
            'sku' => $request->sku,
            'nome' => $request->nome,
            'unidade_medida' => $request->unidade_medida,
            'turma_id' => $request->turma_id ?: null, // Salva o ID ou Nulo (Global)
        ]);
        
        return redirect()->route('professor.materias-primas.index');
    }

    public function editarMateriaPrima(\App\Models\MateriaPrima $materiaPrima)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);
        return view('professor.materias-primas_edit', ['materiaPrima' => $materiaPrima]);
    }

    public function atualizarMateriaPrima(Request $request, \App\Models\MateriaPrima $materiaPrima)
    {
        // Adicionei esta linha para manter a segurança igual às outras funções
        if (Auth::user()->tipo == 'aluno') abort(403);

        $request->validate([
            'sku' => 'required|string|max:255',
            'nome' => 'required|string|max:255',
            'unidade_medida' => 'required|string|max:255',
        ]);
        
        $materiaPrima->update($request->all());
        return redirect()->route('professor.materias-primas.index');
    }

     public function excluirMateriaPrima(MateriaPrima $materiaPrima)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);

        // Move o material para a lixeira (Soft Delete)
        $materiaPrima->delete();

        return redirect()->route('professor.materias-primas.index')
            ->with('success', 'Matéria-prima movida para a lixeira com sucesso.');
    }

    // --- CADASTRO DE PRODUTOS ACABADOS ---
    public function gerenciarProdutosAcabados(Request $request)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);
        
        $user = Auth::user();
        $query = ProdutoAcabado::with('turma');

        // --- NOVO: LÓGICA DE ISOLAMENTO ---
        if ($user->id !== 1) {
            // Pega apenas as turmas deste professor
            $minhasTurmasIds = Turma::where('professor_id', $user->id)->pluck('id');

            // Filtra produtos: Globais OU das minhas turmas
            $query->where(function($q) use ($minhasTurmasIds) {
                $q->whereNull('turma_id')
                  ->orWhereIn('turma_id', $minhasTurmasIds);
            });
        }
        // ----------------------------------

        if ($request->filled('filtro_turma')) {
            if ($request->filtro_turma == 'global') {
                $query->whereNull('turma_id');
            } else {
                $query->where('turma_id', $request->filtro_turma);
            }
        }
        
        $produtosAcabados = $query->get();

        // CORREÇÃO: Carrega apenas as turmas dele para filtros e cadastros
        $turmas = Turma::doProfessor()->get();

        return view('professor.produtos-acabados_index', compact('produtosAcabados', 'turmas'));
    }

    public function salvarProdutoAcabado(Request $request)
    {
        $request->validate([
            'sku' => 'required|string|max:255',
            'nome' => 'required|string|max:255',
            'preco_venda' => 'required|numeric|min:0',
            'tempo_producao_dias' => 'required|integer|min:1',
        ]);
        
        ProdutoAcabado::create([
            'sku' => $request->sku,
            'nome' => $request->nome,
            'preco_venda' => $request->preco_venda,
            'tempo_producao_dias' => $request->tempo_producao_dias,
            'turma_id' => $request->turma_id ?: null,
        ]);
        
        return redirect()->route('professor.produtos-acabados.index');
    }

    public function editarProdutoAcabado(ProdutoAcabado $produto)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);
        $materiasPrimasDisponiveis = MateriaPrima::all();
        return view('professor.produtos-acabados_edit', [
            'produto' => $produto,
            'materiasPrimasDisponiveis' => $materiasPrimasDisponiveis,
        ]);
    }

    public function atualizarProdutoAcabado(Request $request, ProdutoAcabado $produto)
    {
        $request->validate([
            'sku' => 'required|string|max:255',
            'nome' => 'required|string|max:255',
            'preco_venda' => 'required|numeric|min:0',
            'tempo_producao_dias' => 'required|integer|min:1',
        ]);
        $produto->update($request->all());
        return redirect()->route('professor.produtos-acabados.index');
    }

    public function excluirProdutoAcabado(ProdutoAcabado $produto)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);

        // ATENÇÃO: Removi o 'detach()' para preservar a receita do produto no banco.
        // Como o Model agora usa SoftDeletes, o comando abaixo apenas move para a lixeira.
        $produto->delete();

        return redirect()->route('professor.produtos-acabados.index')
            ->with('success', 'Produto movido para a lixeira. O histórico de composição foi preservado.');
    }

    public function adicionarComponente(Request $request, ProdutoAcabado $produto)
    {
        $request->validate([
            'materia_prima_id' => 'required|exists:materias_primas,id',
            'quantidade' => 'required|numeric|min:0.01',
        ]);
        $produto->materiasPrimas()->attach($request->materia_prima_id, ['quantidade' => $request->quantidade]);
        return redirect()->back();
    }

    public function removerComponente(ProdutoAcabado $produto, MateriaPrima $materiaPrima)
    {
        $produto->materiasPrimas()->detach($materiaPrima->id);
        return redirect()->back();
    }

    // --- CADASTRO DE CLIENTES ---
    public function salvarCliente(Request $request)
    {
        $request->validate([
            'nome_razao_social' => 'required|string|max:255',
            'cnpj_cpf' => 'nullable|string|max:18',
        ]);
        
        // Injeta a turma_id manualmente antes de criar
        $dados = $request->all();
        if(!$request->turma_id) $dados['turma_id'] = null;
        
        Cliente::create($dados);
        return redirect()->route('professor.clientes.index');
    }

    public function gerenciarClientes(Request $request)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);

        $user = Auth::user();
        $query = Cliente::with('turma');

        // --- NOVO: LÓGICA DE ISOLAMENTO ---
        // Se não for o Admin (ID 1), filtra para ver apenas o que lhe pertence
        if ($user->id !== 1) {
            $minhasTurmasIds = Turma::where('professor_id', $user->id)->pluck('id');

            $query->where(function($q) use ($minhasTurmasIds) {
                $q->whereNull('turma_id') // Vê clientes globais
                  ->orWhereIn('turma_id', $minhasTurmasIds); // Vê clientes das próprias turmas
            });
        }
        // ----------------------------------

        if ($request->filled('filtro_turma')) {
            if ($request->filtro_turma == 'global') {
                $query->whereNull('turma_id');
            } else {
                $query->where('turma_id', $request->filtro_turma);
            }
        }

        $clientes = $query->get();

        // CORREÇÃO: Dropdown mostra apenas as turmas deste professor
        $turmas = Turma::doProfessor()->get();

        return view('professor.clientes_index', compact('clientes', 'turmas'));
    }

    public function editarCliente(Cliente $cliente)
    {
        return view('professor.clientes_edit', ['cliente' => $cliente]);
    }

    public function atualizarCliente(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nome_razao_social' => 'required|string|max:255',
            'cnpj_cpf' => 'nullable|string|max:18',
        ]);
        $cliente->update($request->all());
        return redirect()->route('professor.clientes.index');
    }

    public function excluirCliente(Cliente $cliente)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);

        // Como o Model Cliente usa SoftDeletes, isso não apaga do banco.
        $cliente->delete();

        return redirect()->route('professor.clientes.index')
            ->with('success', 'Cliente removido da lista ativa (enviado para Lixeira).');
    }
    
    // --- CADASTRO DE FORNECEDORES ---
    public function gerenciarFornecedores(Request $request)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);

        $user = Auth::user();
        $query = Fornecedor::with(['materiaPrima', 'turma']);

        // --- NOVO: LÓGICA DE ISOLAMENTO (RESULTADOS) ---
        if ($user->id !== 1) {
            $minhasTurmasIds = Turma::where('professor_id', $user->id)->pluck('id');

            $query->where(function($q) use ($minhasTurmasIds) {
                $q->whereNull('turma_id')
                  ->orWhereIn('turma_id', $minhasTurmasIds);
            });
        }
        // -----------------------------------------------

        if ($request->filled('filtro_turma')) {
            if ($request->filtro_turma == 'global') {
                $query->whereNull('turma_id');
            } else {
                $query->where('turma_id', $request->filtro_turma);
            }
        }
        
        $fornecedores = $query->get();

        // --- NOVO: FILTRO DAS MATÉRIAS-PRIMAS DO DROPDOWN ---
        // Garante que o professor só selecione materiais que ele pode ver
        $mpsQuery = MateriaPrima::query();
        if ($user->id !== 1) {
            $minhasTurmasIds = Turma::where('professor_id', $user->id)->pluck('id');
            $mpsQuery->where(function($q) use ($minhasTurmasIds) {
                $q->whereNull('turma_id')
                  ->orWhereIn('turma_id', $minhasTurmasIds);
            });
        }
        $materiasPrimas = $mpsQuery->get();

        // CORREÇÃO: Dropdown de turmas seguro
        $turmas = Turma::doProfessor()->get();

        return view('professor.fornecedores_index', compact('fornecedores', 'materiasPrimas', 'turmas'));
    }

    public function salvarFornecedor(Request $request)
    {
        $request->validate([
            'nome_razao_social' => 'required|string|max:255',
            'preco_unitario' => 'required|numeric|min:0',
            'tempo_entrega_dias' => 'required|integer|min:0',
        ]);
        
        $dados = $request->all();
        if(!$request->turma_id) $dados['turma_id'] = null;

        Fornecedor::create($dados);
        return redirect()->route('professor.fornecedores.index')->with('success', 'Fornecedor cadastrado!');
    }

    public function excluirFornecedor(Fornecedor $fornecedor)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);

        // O comando delete() agora apenas marca como excluído (Soft Delete)
        $fornecedor->delete();

        return redirect()->route('professor.fornecedores.index')
            ->with('success', 'Fornecedor removido da lista ativa. O histórico de compras foi preservado.');
    }

    // --- PEDIDOS DE VENDA ---
    public function gerenciarPedidosVenda(Request $request)
    {
        // Carrega listas para os filtros
        $clientes = Cliente::all();
        $produtos = ProdutoAcabado::all();
        
        // CORREÇÃO: Carrega APENAS as turmas do professor para o dropdown
        // Isso evita que você veja turmas de outros professores no filtro
        $turmas = Turma::doProfessor()->get(); 

        // Inicia a query de pedidos
        // Trazendo relacionamentos necessários
        $query = PedidoVenda::with(['cliente', 'itens', 'turma']);

        // --- FILTROS ---

        // 1. Filtro por ID da Turma (Vem do Dashboard ou do Dropdown)
        if ($request->filled('filtro_turma')) {
            $query->where('turma_id', $request->filtro_turma);
        }

        // 2. NOVO: Busca por Nome da Empresa (Texto)
        if ($request->filled('busca_empresa')) {
            $termo = $request->busca_empresa;
            $query->whereHas('turma', function($q) use ($termo) {
                $q->where('nome_empresa', 'like', '%' . $termo . '%');
            });
        }

        if ($request->filled('filtro_cliente')) $query->where('cliente_id', $request->filtro_cliente);
        if ($request->filled('filtro_status')) $query->where('status', $request->filtro_status);
        
        // Filtros de Cidade/Estado (se existirem na view)
        if ($request->filled('filtro_cidade')) {
            $cidade = $request->filtro_cidade;
            $query->whereHas('cliente', fn($q) => $q->where('cidade', 'like', "%$cidade%"));
        }
        if ($request->filled('filtro_estado')) {
            $uf = $request->filtro_estado;
            $query->whereHas('cliente', fn($q) => $q->where('estado', $uf));
        }
        
        // Garante que o professor só veja pedidos das SUAS turmas (Segurança)
        if (Auth::user()->id !== 1) {
            $minhasTurmas = Turma::where('professor_id', Auth::user()->id)->pluck('id');
            $query->whereIn('turma_id', $minhasTurmas);
        }

        $pedidos = $query->orderBy('id', 'desc')->get();
        
        // Proteção para caso a tabela de config não tenha dados ainda
        $dataSimulacao = now();
        $configData = \App\Models\ConfiguracaoSimulacao::where('chave', 'data_simulacao')->first();
        if($configData) $dataSimulacao = $configData->valor;

        return view('professor.pedidos-venda_index', compact('pedidos', 'clientes', 'produtos', 'turmas', 'dataSimulacao'));
    }

    public function salvarPedidoVenda(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'turma_id' => 'required|exists:turmas,id',
            'data_entrega_solicitada' => 'required|date',
            'itens' => 'required|array|min:1',
        ]);

        $valorTotalPedido = 0;
        $itensParaSalvar = [];

        foreach ($request->itens as $itemData) {
            $produto = ProdutoAcabado::find($itemData['produto_id']);
            $totalItem = $produto->preco_venda * $itemData['quantidade'];
            $valorTotalPedido += $totalItem;
            
            $itensParaSalvar[] = [
                'produto_acabado_id' => $itemData['produto_id'],
                'quantidade' => $itemData['quantidade'],
                'preco_unitario' => $produto->preco_venda,
                'preco_total_item' => $totalItem,
            ];
        }

        $pedido = PedidoVenda::create([
            'cliente_id' => $request->cliente_id,
            'turma_id' => $request->turma_id,
            'data_pedido' => \App\Models\ConfiguracaoSimulacao::where('chave', 'data_simulacao')->first()->valor,
            'data_entrega_solicitada' => $request->data_entrega_solicitada,
            'valor_total' => $valorTotalPedido,
            'status' => 'Novo',
        ]);

        $pedido->itens()->createMany($itensParaSalvar);
        return redirect()->route('professor.pedidos-venda.index');
    }

    public function excluirPedidoVenda(PedidoVenda $pedidoVenda)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);

        // Como ativamos o SoftDeletes no Model, esse comando agora
        // NÃO apaga o registro. Ele apenas marca a data de exclusão (Lixeira).
        $pedidoVenda->delete();

        return redirect()->route('professor.pedidos-venda.index')
            ->with('success', 'Pedido removido da lista ativa (enviado para Lixeira). Histórico mantido.');
    }

    public function verDetalhesPedidoVenda(PedidoVenda $pedidoVenda)
    {
        return view('professor.pedidos-venda_show', ['pedido' => $pedidoVenda]);
    }

    public function editarPedidoVenda(PedidoVenda $pedidoVenda)
    {
        $clientes = Cliente::all();
        $produtos = ProdutoAcabado::all();
        $turmas = Turma::all();

        return view('professor.pedidos-venda_edit', [
            'pedido' => $pedidoVenda,
            'clientes' => $clientes,
            'produtos' => $produtos,
            'turmas' => $turmas,
        ]);
    }

    public function atualizarPedidoVenda(Request $request, PedidoVenda $pedidoVenda)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'turma_id' => 'required|exists:turmas,id',
            'data_entrega_solicitada' => 'required|date',
        ]);

        $pedidoVenda->update([
            'cliente_id' => $request->cliente_id,
            'turma_id' => $request->turma_id,
            'data_entrega_solicitada' => $request->data_entrega_solicitada,
        ]);
        return redirect()->route('professor.pedidos-venda.index');
    }

    public function adicionarItemPedido(Request $request, PedidoVenda $pedidoVenda)
    {
        $request->validate([
            'produto_id' => 'required|exists:produtos_acabados,id',
            'quantidade' => 'required|integer|min:1',
        ]);

        $produto = ProdutoAcabado::find($request->produto_id);
        $total = $produto->preco_venda * $request->quantidade;

        $pedidoVenda->itens()->create([
            'produto_acabado_id' => $request->produto_id,
            'quantidade' => $request->quantidade,
            'preco_unitario' => $produto->preco_venda,
            'preco_total_item' => $total,
        ]);

        $pedidoVenda->valor_total = $pedidoVenda->itens()->sum('preco_total_item');
        $pedidoVenda->save();
        return redirect()->back();
    }

    public function removerItemPedido(PedidoVendaItem $pedidoVendaItem)
    {
        $pedido = $pedidoVendaItem->pedido;
        $pedidoVendaItem->delete();
        $pedido->valor_total = $pedido->itens()->sum('preco_total_item');
        $pedido->save();
        return redirect()->back();
    }

    public function avancarTempo(Request $request)
    {
        $request->validate(['turma_id' => 'required|exists:turmas,id']);
        $turma = Turma::findOrFail($request->turma_id);

        if ($request->has('minutos')) {
            $turma->data_jogo = $turma->data_jogo->addMinutes((int)$request->minutos);
        } elseif ($request->has('horas')) {
            $turma->data_jogo = $turma->data_jogo->addHours((int)$request->horas);
        } else {
            $dias = (int) $request->input('dias', 1);
            $turma->data_jogo = $turma->data_jogo->addDays($dias);
        }
        $turma->save();
        return redirect()->back()->with('success', "Tempo avançado!");
    }

    public function resetarTempo()
    {
        ConfiguracaoSimulacao::updateOrCreate(
            ['chave' => 'data_simulacao'],
            ['valor' => now()->toDateTimeString()]
        );
        return redirect()->back()->with('success', 'Simulação resetada.');
    }

    public function alterarStatusJogo(Request $request)
    {
        $request->validate([
            'turma_id' => 'required|exists:turmas,id',
            'acao' => 'required|in:iniciar,pausar,resetar'
        ]);

        $turma = Turma::findOrFail($request->turma_id);

        if ($request->acao == 'iniciar') {
            $turma->jogo_ativo = true;
            $msg = "Simulação INICIADA para {$turma->nome}.";
        } 
        elseif ($request->acao == 'pausar') {
            $turma->jogo_ativo = false;
            $msg = "Simulação PAUSADA para {$turma->nome}.";
        }
        elseif ($request->acao == 'resetar') {
            $turma->jogo_ativo = false;
            $turma->data_jogo = now(); 
            $msg = "Simulação RESETADA para {$turma->nome}.";
        }

        $turma->save();
        return redirect()->back()->with('success', $msg);
    }

    public function gerarMapaEstoque(Request $request)
    {
        $request->validate(['turma_id' => 'required|exists:turmas,id']);
        $turmaId = $request->turma_id;
        
        LocalEstoque::where('turma_id', $turmaId)->delete(); 

        $ruas = 10; $colunas = 10; $vaos = 2; $niveis = 5;
        $totalCriado = 0;

        for ($r = 1; $r <= $ruas; $r++) {
            for ($c = 1; $c <= $colunas; $c++) {
                for ($v = 1; $v <= $vaos; $v++) {
                    for ($n = 1; $n <= $niveis; $n++) {
                        $codigo = sprintf("R%02d-C%02d-V%d-N%d", $r, $c, $v, $n);
                        LocalEstoque::create([
                            'turma_id' => $turmaId,
                            'codigo_visual' => $codigo,
                            'rua' => $r, 'coluna' => $c, 'vao' => $v, 'nivel' => $n,
                            'ocupado' => false
                        ]);
                        $totalCriado++;
                    }
                }
            }
        }
        return redirect()->back()->with('success', "WMS criado com {$totalCriado} endereços.");
    }
    public function atualizarCapacidadeProducao(Request $request, $id)
    {
        $request->validate(['capacidade' => 'required|integer|min:1']);
        $turma = Turma::findOrFail($id);
        $turma->capacidade_producao = $request->capacidade;
        $turma->save();
        
        return back()->with('success', "Capacidade da fábrica alterada para {$request->capacidade} ordens simultâneas.");
    }
    // =========================================================================
    //  GESTÃO DE PROFESSORES (SALA DOS PROFESSORES) - APENAS ADMIN (ID 1)
    // =========================================================================

    public function gerenciarProfessores()
    {
        // SEGURANÇA MÁXIMA: Só o usuário ID 1 (Você) pode acessar
        if (Auth::id() !== 1) {
            return redirect()->route('professor.dashboard')->with('error', 'Acesso restrito ao Administrador.');
        }

        // Lista todos os usuários do tipo 'professor', exceto você mesmo
        $professores = User::where('tipo', 'professor')
                           ->where('id', '!=', 1) 
                           ->orderBy('name')
                           ->get();

        return view('professor.global.professores_index', compact('professores'));
    }

    public function salvarProfessor(Request $request)
    {
        if (Auth::id() !== 1) abort(403); 

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'cod_acesso' => 'required|string|size:3', // Valida os 3 números
        ]);

        // Cria a senha combinando o prefixo Senai + os números escolhidos
        $senhaGerada = 'Senai' . $request->cod_acesso;

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($senhaGerada), 
            'tipo' => 'professor',
            // O campo rg_finais pode ser usado para guardar esses 3 digitos se quiser referencia futura, 
            // ou deixamos null pois é professor. Vamos deixar null para diferenciar.
        ]);

        return redirect()->back()->with('success', "Professor cadastrado! A senha inicial é: $senhaGerada");
    }

    public function excluirProfessor(User $user)
    {
        if (Auth::id() !== 1) abort(403); // Segurança

        // Impede de excluir a si mesmo (redundância de segurança)
        if ($user->id === 1) return back()->with('error', 'Você não pode se excluir.');

        // Ao deletar o professor, o CASCADE do banco deletará as turmas dele automaticamente
        $user->delete();

        return redirect()->back()->with('success', 'Professor e suas turmas foram removidos.');
    }

    // =========================================================================
    //  EDIÇÃO DE FORNECEDORES
    // =========================================================================

    public function editarFornecedor(Fornecedor $fornecedor)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);
        
        $user = Auth::user();

        // 1. Carrega Matérias-Primas (Mesma lógica de segurança da listagem)
        $mpsQuery = MateriaPrima::query();
        if ($user->id !== 1) {
            $minhasTurmasIds = Turma::where('professor_id', $user->id)->pluck('id');
            $mpsQuery->where(function($q) use ($minhasTurmasIds) {
                $q->whereNull('turma_id')->orWhereIn('turma_id', $minhasTurmasIds);
            });
        }
        $materiasPrimas = $mpsQuery->get();

        // 2. Carrega Turmas Disponíveis
        $turmas = Turma::doProfessor()->get();

        return view('professor.fornecedores_edit', compact('fornecedor', 'materiasPrimas', 'turmas'));
    }

    public function atualizarFornecedor(Request $request, Fornecedor $fornecedor)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);

        $request->validate([
            'nome_razao_social' => 'required|string|max:255',
            'preco_unitario' => 'required|numeric|min:0',
            'tempo_entrega_dias' => 'required|integer|min:0',
        ]);

        $dados = $request->all();
        // Garante que se vier vazio, salva como null (Global)
        if(!$request->turma_id) $dados['turma_id'] = null;
        if(!$request->materia_prima_id) $dados['materia_prima_id'] = null;

        $fornecedor->update($dados);

        return redirect()->route('professor.fornecedores.index')->with('success', 'Fornecedor atualizado com sucesso!');
    }

    // Alternar status (Bloquear/Desbloquear)
    public function toggleStatusUsuario(User $user)
    {
        // Segurança: Só Admin (ID 1) pode bloquear Professores
        if ($user->tipo == 'professor' && Auth::id() !== 1) {
            abort(403, 'Apenas o Admin pode bloquear professores.');
        }

        // Não pode bloquear a si mesmo
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Você não pode se bloquear.');
        }

        // Inverte o status (Se tá true vira false, se tá false vira true)
        $user->ativo = !$user->ativo;
        $user->save();

        $status = $user->ativo ? 'ATIVADO' : 'BLOQUEADO';
        $cor = $user->ativo ? 'success' : 'warning'; // Warning para amarelo/atenção

        return back()->with($cor, "O acesso do usuário {$user->name} foi {$status}.");
    }

    // Atualizar quantidade de um item específico
    public function atualizarItemPedido(Request $request, \App\Models\PedidoVendaItem $item)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);

        $request->validate([
            'quantidade' => 'required|integer|min:1'
        ]);

        // 1. Atualiza o Item
        $item->quantidade = $request->quantidade;
        $item->preco_total_item = $item->quantidade * $item->preco_unitario;
        $item->save();

        // 2. Atualiza o Total do Pedido Pai
        $pedido = $item->pedido;
        $pedido->valor_total = $pedido->itens()->sum('preco_total_item');
        $pedido->save();

        return redirect()->back()->with('success', 'Quantidade atualizada e total recalculado!');
    }
    public function verPerfilTurma(\App\Models\Turma $turma)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);
        // Retorna a view de visualização
        return view('perfil_empresa', compact('turma'));
    }

    // =========================================================================
    //  GESTÃO DE MATÉRIAS-PRIMAS
    // =========================================================================
    public function gerenciarMateriasPrimas(Request $request)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);
        
        $user = Auth::user();
        $query = \App\Models\MateriaPrima::with('turma');

        if ($user->id !== 1) {
            $minhasTurmasIds = \App\Models\Turma::where('professor_id', $user->id)->pluck('id');
            $query->where(function($q) use ($minhasTurmasIds) {
                $q->whereNull('turma_id')
                  ->orWhereIn('turma_id', $minhasTurmasIds);
            });
        }

        if ($request->filled('filtro_turma')) {
            if ($request->filtro_turma == 'global') {
                $query->whereNull('turma_id');
            } else {
                $query->where('turma_id', $request->filtro_turma);
            }
        }
        
        $materiasPrimas = $query->get();
        $turmas = \App\Models\Turma::doProfessor()->get(); 

        $temMapaWMS = false;
        // Verifica se existe filtro e se não é global
        if ($request->filled('filtro_turma') && $request->filtro_turma != 'global') {
            $temMapaWMS = \App\Models\LocalEstoque::where('turma_id', $request->filtro_turma)->exists();
        } // <--- FECHAMENTO DO IF DO MAPA
        
        return view('professor.materias-primas_index', compact('materiasPrimas', 'turmas', 'temMapaWMS'));
    }

    public function atualizarLimiteVendas(Request $request, $id)
    {
        $request->validate([
            'limite_vendas_por_aluno' => 'required|integer|min:1|max:1000'
        ]);

        $turma = \App\Models\Turma::findOrFail($id);
        $turma->limite_vendas_por_aluno = $request->limite_vendas_por_aluno;
        $turma->save();

        return redirect()->back()->with('success', "Regra de Mercado atualizada: Limite de {$request->limite_vendas_por_aluno} pedidos/dia por aluno.");
    }

    

} // <--- FIM DA CLASSE    