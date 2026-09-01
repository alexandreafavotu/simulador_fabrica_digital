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
use App\Models\DemandaMercado;
use App\Models\DemandaMercadoItem;
use Illuminate\Support\Facades\Http;

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

    /**
     * Gera o modelo CSV para download pelo professor
     */
    public function downloadModeloAlunosCsv()
    {
        if (Auth::user()->tipo == 'aluno') abort(403);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="modelo_importacao_alunos.csv"',
        ];

        $content = "\xEF\xBB\xBF"; // UTF-8 BOM para abrir perfeitamente no Excel
        $content .= "Nome;Email;Curso;Ano/Turma;Senha 3 Digitos\n";
        $content .= "Arthur Lemes Alves;arthur@edu.senai.br;Almoxarife;2026 - Manhã;123\n";
        $content .= "Maria Silva;maria@edu.senai.br;Logística;2026 - Tarde;456\n";

        return response($content, 200, $headers);
    }

    /**
     * Importação em massa de alunos via arquivo CSV / Excel
     */
    public function importarAlunosEmMassa(Request $request)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);

        $request->validate([
            'arquivo' => 'required|file|max:10240',
        ]);

        $file = $request->file('arquivo');
        $path = $file->getRealPath();

        $handle = @fopen($path, 'r');
        if (!$handle) {
            return back()->with('error', 'Não foi possível abrir o arquivo enviado. Verifique a extensão e tente novamente.');
        }

        // Tenta identificar o delimitador
        $primeiraLinha = fgets($handle);
        rewind($handle);

        $delimiter = ';';
        if (substr_count($primeiraLinha, ',') > substr_count($primeiraLinha, ';')) {
            $delimiter = ',';
        } elseif (substr_count($primeiraLinha, "\t") > substr_count($primeiraLinha, ';')) {
            $delimiter = "\t";
        }

        $importados = 0;
        $ignorados = 0;
        $linha = 0;

        while (($data = fgetcsv($handle, 2000, $delimiter)) !== false) {
            $linha++;

            // Pula a primeira linha se for o cabeçalho
            if ($linha === 1) {
                $cabecalhoStr = strtolower(implode(' ', $data));
                if (str_contains($cabecalhoStr, 'nome') || str_contains($cabecalhoStr, 'email') || str_contains($cabecalhoStr, 'curso')) {
                    continue;
                }
            }

            // Pula linhas vazias
            if (empty(array_filter($data))) continue;

            // Remove o BOM da primeira célula se existir
            if (isset($data[0])) {
                $data[0] = preg_replace('/^[\x{FEFF}\x{FFFE}]/u', '', $data[0]);
            }

            // Colunas: 0: Nome, 1: Email, 2: Curso, 3: Ano/Turma, 4: Senha (3 dígitos)
            $nome = isset($data[0]) ? trim($data[0]) : '';
            $email = isset($data[1]) ? trim(strtolower($data[1])) : '';
            $curso = isset($data[2]) ? trim($data[2]) : 'Geral';
            $anoTurma = isset($data[3]) ? trim($data[3]) : '2026';
            $rgFinais = isset($data[4]) ? preg_replace('/[^0-9]/', '', trim($data[4])) : '';
            if (strlen($rgFinais) !== 3) {
                $rgFinais = '123';
            }

            if (empty($nome) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $ignorados++;
                continue;
            }

            // Se o e-mail já existir no sistema, ignora
            if (User::where('email', $email)->exists()) {
                $ignorados++;
                continue;
            }

            User::create([
                'name' => $nome,
                'email' => $email,
                'tipo' => 'aluno',
                'curso' => $curso,
                'ano_letivo' => $anoTurma,
                'rg_finais' => $rgFinais,
                'password' => Hash::make('Senai' . $rgFinais),
                'ativo' => true,
            ]);

            $importados++;
        }

        fclose($handle);

        $mensagem = "📊 Importação em massa realizada! {$importados} aluno(s) matriculado(s) com sucesso.";
        if ($ignorados > 0) {
            $mensagem .= " ({$ignorados} linha(s) ignorada(s) por já existirem no sistema ou estarem incorretas).";
        }

        return redirect()->route('professor.global.alunos')->with('success', $mensagem);
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
        $regras = [
            'aluno_cadastra_cliente', 
            'aluno_cadastra_fornecedor', 
            'aluno_cadastra_materia_prima',
            'exigir_inspecao_qualidade' // <--- Inclusão da nova regra global
        ];

        foreach ($regras as $regra) {
            // updateOrCreate garante a criação automática se a chave for nova
            ConfiguracaoSimulacao::updateOrCreate(
                ['chave' => $regra],
                ['valor' => $request->has($regra) ? 1 : 0]
            );
        }

        return redirect()->route('professor.dashboard')->with('success', 'Regras globais atualizadas com sucesso!');
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

    // =========================================================================
    //  EDIÇÃO DE ALUNO E ACESSIBILIDADE
    // =========================================================================

    public function editarAluno(User $user)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);
        
        // BUSCAMOS AS TURMAS: Isso corrige o erro "Undefined variable $turmas"
        $turmas = Turma::doProfessor()->get();
        
        return view('professor.alunos_edit', [
            'aluno' => $user,
            'turmas' => $turmas
        ]);
    }

    public function atualizarAluno(Request $request, User $user)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);

        $request->validate([
            'nome' => 'required|string|max:255',
            'curso' => 'required|string|max:255',
            'ano_letivo' => 'required|string|max:255',
        ]);

        // ATRIBUIÇÃO DIRETA: Ignora a trava do $fillable e garante a gravação no banco
        $user->name = $request->nome;
        $user->curso = $request->curso;
        $user->ano_letivo = $request->ano_letivo;
        
        $user->acessibilidade_visual = $request->acessibilidade_visual;
        $user->acessibilidade_motora = $request->acessibilidade_motora;
        $user->acessibilidade_audio = $request->acessibilidade_audio;
        $user->acessibilidade_pictogramas = $request->acessibilidade_pictogramas;
        $user->acessibilidade_libras = $request->acessibilidade_libras;
        $user->acessibilidade_cognitiva = 0;

        $user->save(); // <--- Força a gravação real no banco de dados

        return redirect()->route('professor.global.alunos')
            ->with('success', 'Cadastro, curso e turma atualizados com sucesso!');
    }

public function gerarDemandaMercado(Request $request, $turmaId)
{
    set_time_limit(180);
    $turma = Turma::findOrFail($turmaId);
    $quantidadeSolicitada = $request->input('quantidade_pedidos', 5);
    
    $clientes = Cliente::where('turma_id', $turmaId)->get(['id', 'nome_razao_social'])->toArray();
    $produtos = ProdutoAcabado::where('turma_id', $turmaId)->get(['id', 'nome'])->toArray();
    $dataJogo = \Carbon\Carbon::parse($turma->data_jogo)->format('Y-m-d');

    if (empty($clientes) || empty($produtos)) {
        return back()->with('error', 'Cadastre pelo menos um Cliente e um Produto.');
    }

    $prompt = "Você é o Mercado Consumidor Brasileiro. Gere uma lista de INTENÇÕES DE COMPRA para a empresa '{$turma->nome_empresa}'.
    CONTEXTO:
    - Data Atual do Jogo: {$dataJogo}
    - Clientes Disponíveis (IDs): " . json_encode($clientes) . "
    - Produtos Disponíveis (IDs): " . json_encode($produtos) . "
    - Quantidade de Pedidos a Gerar: {$quantidadeSolicitada}

    REGRAS:
    1. PRAZO: data_entrega entre 10 e 25 dias após {$dataJogo}.
    2. ITENS: Cada pedido pode ter de 1 a 3 produtos diferentes da lista.
    3. QUANTIDADE: Inteiros entre 5 e 100.
    4. FORMATO: Responda APENAS com JSON puro.";

    try {
        $apiKey = env('GEMINI_API_KEY');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent?key=" . $apiKey;

        $response = Http::timeout(120)->retry(3, 2000)->post($url, [
            'contents' => [['parts' => [['text' => $prompt]]]],
            'generationConfig' => ['response_mime_type' => 'application/json']
        ]);

        $res = $response->json();
        $textoJson = $res['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
        $demandas = json_decode($textoJson, true);

        if (!is_array($demandas)) {
            return back()->with('error', 'Erro ao processar dados do mercado.');
        }

        // SALVAMENTO NO BANCO
        // SALVAMENTO NO BANCO
        foreach ($demandas as $d) {
            $novaDemanda = DemandaMercado::create([
                'turma_id' => $turmaId,
                'cliente_id' => $d['cliente_id'],
                'data_entrega_solicitada' => $d['data_entrega'],
                'data_jogo_emissao' => $turma->data_jogo, // <-- ADICIONE APENAS ESTA LINHA
                'status' => 'Pendente'
            ]);

            foreach ($d['itens'] as $item) {
                DemandaMercadoItem::create([
                    'demanda_mercado_id' => $novaDemanda->id, // Nome exato da migration
                    'produto_acabado_id' => $item['produto_id'],
                    'quantidade' => $item['quantidade']
                ]);
            }
        
        }

        return back()->with('success', "Mercado Aquecido! {$quantidadeSolicitada} novas solicitações foram enviadas para o setor de Vendas.");

    } catch (\Throwable $e) {
        // Se der erro no banco (coluna errada, etc), ele vai parar aqui e te dizer o nome da coluna.
        dd("Erro ao salvar no banco: " . $e->getMessage(), "Linha: " . $e->getLine());
    }
}
  public function verFinanceiro($id)
{
    $turma = Turma::findOrFail($id);
    return view('professor.turmas_financeiro', compact('turma'));
}

public function injetarCapital(Request $request, $id)
{
    $turma = Turma::findOrFail($id);
    $valor = (float) $request->valor_injeção;

    // Forçamos a verificação do valor que vem do botão "acao"
    if ($request->input('acao') == 'retirar') {
        $turma->capital_atual = $turma->capital_atual - $valor;
        $msg = "R$ " . number_format($valor, 2, ',', '.') . " retirados do caixa!";
    } else {
        $turma->capital_atual = $turma->capital_atual + $valor;
        $msg = "R$ " . number_format($valor, 2, ',', '.') . " injetados com sucesso!";
    }

    $turma->save();
    return back()->with('success', $msg);
}
public function configurarRelogio(Request $request, $id)
{
    $turma = Turma::findOrFail($id);
    
    // Convertemos os valores para números inteiros para evitar o erro de type
    $status = (int) $request->status;
    $intervalo = (int) $request->intervalo;

    $turma->relogio_ativo = $status;
    $turma->relogio_intervalo = $intervalo;
    
    // Se ligou o relógio, calcula a próxima virada
    if($status === 1) {
        // Agora usamos a variável já convertida para número
        $turma->relogio_proximo_avanco = now()->addMinutes($intervalo);
    } else {
        // Se desligou, limpamos a próxima virada
        $turma->relogio_proximo_avanco = null;
    }
    
    $turma->save();
    return back()->with('success', 'Configuração do relógio atualizada!');
}

    // =========================================================================
    //  SISTEMA DE AVALIAÇÃO PEDAGÓGICA (GATILHO DE COMPETÊNCIAS)
    // =========================================================================

    /**
     * Garante que as tabelas de competências existam no banco do servidor online
     */
    private function garantirTabelasCompetenciasExistentes()
    {
        if (!Schema::hasTable('competencias_cursos')) {
            Schema::create('competencias_cursos', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->id();
                $table->string('curso');
                $table->string('unidade_curricular')->nullable();
                $table->enum('categoria', ['tecnica', 'organizacional', 'socioemocional', 'conhecimento']);
                $table->string('nome');
                $table->text('descricao');
                $table->enum('tipo_avaliacao', ['automatica', 'manual'])->default('manual');
                $table->string('metrica_chave')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('aluno_avaliacoes')) {
            Schema::create('aluno_avaliacoes', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('competencia_id')->constrained('competencias_cursos')->onDelete('cascade');
                $table->enum('status', ['pendente', 'conforme', 'nao_conforme'])->default('pendente');
                $table->text('evidencia_automatica')->nullable();
                $table->text('observacoes_professor')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * 1. Exibe a tela de busca de alunos filtrada estritamente por Curso e Ano Letivo
     */
    public function indexAvaliacoes(Request $request)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);

        // Garante a existência das tabelas no banco de dados para prevenir Erro 500
        $this->garantirTabelasCompetenciasExistentes();

        // Busca opções únicas e reais para alimentar os dropdowns (evitando misturar turmas)
        $cursosDisponiveis = User::where('tipo', 'aluno')->whereNotNull('curso')->distinct()->pluck('curso');
        $anosDisponiveis = User::where('tipo', 'aluno')->whereNotNull('ano_letivo')->distinct()->pluck('ano_letivo');

        $alunos = collect();

        // Só executa a busca de alunos se ambos os filtros estiverem preenchidos na tela
        if ($request->filled('curso') && $request->filled('ano_letivo')) {
            $alunos = User::where('tipo', 'aluno')
                ->where('curso', $request->curso)
                ->where('ano_letivo', $request->ano_letivo)
                ->orderBy('name')
                ->get();

            // CÁLCULO DINÂMICO DE RENDIMENTO DE CADA ALUNO DA LISTA
            foreach ($alunos as $al) {
                $cursoLimpo = trim($al->curso);

                // 1. Conta o total de capacidades vinculadas ao plano de curso dele
                $al->total_competencias = \Illuminate\Support\Facades\DB::table('competencias_cursos')
                    ->whereRaw('LOWER(curso) = ?', [strtolower($cursoLimpo)])
                    ->count();

                // 2. Conta quantas capacidades o professor ou o sistema já avaliaram como Conforme (OK)
                $al->competencias_atingidas = \Illuminate\Support\Facades\DB::table('aluno_avaliacoes')
                    ->where('user_id', $al->id)
                    ->where('status', 'conforme')
                    ->count();
            }
        }

        return view('professor.avaliacoes_index', compact('cursosDisponiveis', 'anosDisponiveis', 'alunos'));
    }

    /**
     * 2. Carrega o Dashboard de Competências detalhado de um Aluno com KPIs Reais do BI (Corrigido)
     */
    public function avaliarAluno($id)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);

        $this->garantirTabelasCompetenciasExistentes();

        $aluno = User::where('tipo', 'aluno')->findOrFail($id);
        $cursoExato = trim($aluno->curso);

        // Busca as competências específicas usando o termo case-insensitive
        $competencias = \Illuminate\Support\Facades\DB::table('competencias_cursos')
            ->whereRaw('LOWER(curso) = ?', [strtolower($cursoExato)])
            ->get();

        // Carrega o histórico de avaliações já salvas para este aluno
        $avaliacoesSalvas = \Illuminate\Support\Facades\DB::table('aluno_avaliacoes')
            ->where('user_id', $id)
            ->get()
            ->keyBy('competencia_id');

        // --- INTEGRAÇÃO DOS DADOS DO BI (CORREÇÃO DE MULTI-VÍNCULOS) ---
        // 1. Prioriza o vínculo da turma que está com o JOGO ATIVO (Rodando)
        $vincAlun = \App\Models\Aluno::where('user_id', $id)
            ->whereHas('turma', function($q) {
                $q->where('jogo_ativo', true);
            })
            ->first();

        // 2. Fallback: Se nenhuma estiver rodando, pega a última vinculada
        if (!$vincAlun) {
            $vincAlun = \App\Models\Aluno::where('user_id', $id)->latest('id')->first();
        }

        $turmaId = $vincAlun ? $vincAlun->turma_id : null;
        $turma = $turmaId ? Turma::find($turmaId) : null;
        $dataJogoStr = $turma ? $turma->data_jogo->toDateTimeString() : now()->toDateTimeString();
        $alunoTableId = $vincAlun ? $vincAlun->id : null; 

        $bi = [
            'lead_time_vendas' => 0,
            'lead_time_compras' => 0,
            'lead_time_producao' => 0,
            'dias_atraso_acumulado' => 0,
            'gargalos' => ['Vendas' => 0, 'Compras' => 0, 'WMS' => 0, 'Produção' => 0, 'Expedição' => 0]
        ];

        if ($turmaId && $turma && $alunoTableId) {
            // 1. Lead Times por Etapa
            $bi['lead_time_vendas'] = \Illuminate\Support\Facades\DB::table('demandas_mercado')
                ->join('pedidos_venda', 'demandas_mercado.cliente_id', '=', 'pedidos_venda.cliente_id')
                ->where('demandas_mercado.turma_id', $turmaId)
                ->where('demandas_mercado.status', 'Atendido')
                ->where('pedidos_venda.aluno_id', $alunoTableId)
                ->select(\Illuminate\Support\Facades\DB::raw('AVG(DATEDIFF(pedidos_venda.data_pedido, demandas_mercado.data_jogo_emissao)) as media'))
                ->first()->media ?? 0;

            $bi['lead_time_compras'] = \App\Models\OrdemCompra::whereHas('pedido', fn($q) => $q->where('turma_id', $turmaId)->where('aluno_id', $alunoTableId))
                ->where('status', 'Concluído')
                ->select(\Illuminate\Support\Facades\DB::raw('AVG(DATEDIFF(updated_at, created_at)) as media'))
                ->first()->media ?? 0;

            $bi['lead_time_producao'] = \App\Models\OrdemProducao::whereHas('pedido', fn($q) => $q->where('turma_id', $turmaId)->where('aluno_id', $alunoTableId))
                ->where('status', 'Concluída')
                ->select(\Illuminate\Support\Facades\DB::raw('AVG(DATEDIFF(data_fim, data_inicio_real)) as media'))
                ->first()->media ?? 0;

            // 2. Dias de Atraso Acumulados
            $bi['dias_atraso_acumulado'] = \Illuminate\Support\Facades\DB::table('pedidos_venda')
                ->join('pedido_venda_itens', 'pedidos_venda.id', '=', 'pedido_venda_itens.pedido_venda_id')
                ->where('pedidos_venda.turma_id', $turmaId)
                ->where('pedidos_venda.status', '!=', 'Faturado')
                ->where('pedidos_venda.aluno_id', $alunoTableId)
                ->whereDate('pedidos_venda.data_entrega_solicitada', '<', $dataJogoStr)
                ->select(\Illuminate\Support\Facades\DB::raw("SUM(DATEDIFF('{$dataJogoStr}', pedidos_venda.data_entrega_solicitada)) as total_dias"))
                ->first()->total_dias ?? 0;

            // 3. Gargalos Ativos
            $bi['gargalos'] = [
                'Vendas' => \App\Models\DemandaMercado::where('turma_id', $turmaId)->where('status', 'Pendente')->where('aluno_id', $alunoTableId)->count(),
                'Compras' => \App\Models\OrdemCompra::whereHas('pedido', fn($q) => $q->where('turma_id', $turmaId)->where('aluno_id', $alunoTableId))->where('status', 'Pendente')->count(),
                'WMS' => \App\Models\MateriaPrima::where('turma_id', $turmaId)->where('quantidade_estoque', '>', 0)->whereNull('local_estoque_id')->count(), 
                'Produção' => \App\Models\OrdemProducao::whereHas('pedido', fn($q) => $q->where('turma_id', $turmaId)->where('aluno_id', $alunoTableId))->where('status', 'Aberta')->count(),
                'Expedição' => \App\Models\PedidoVenda::where('turma_id', $turmaId)->where('status', 'Em Produção')->where('aluno_id', $alunoTableId)->whereDoesntHave('ordensProducao', fn($q)=>$q->where('embalado', false))->count(),
            ];
        }

        // CALCULO DE MÉTRICAS PEDAGÓGICAS REAIS (Baseadas nas turmas ativas do aluno)
        $minhasTurmasIds = \App\Models\Aluno::where('user_id', $id)->pluck('turma_id');
        $metricas = [
            'wms_picking_concluido' => \App\Models\SolicitacaoSeparacao::where('status', 'Entregue')->whereHas('ordemProducao.pedido', fn($q) => $q->whereIn('turma_id', $minhasTurmasIds))->count(),
            'producao_material_solicitado' => \App\Models\SolicitacaoSeparacao::whereHas('ordemProducao.pedido', fn($q) => $q->whereIn('turma_id', $minhasTurmasIds))->count(),
            'compras_conferencia_concluida' => \App\Models\OrdemCompra::where('status', 'Concluído')->whereHas('pedido', fn($q) => $q->whereIn('turma_id', $minhasTurmasIds))->count(),
            'compras_recusa_justificada' => \App\Models\OrdemCompra::where('status', 'Recusado')->whereNotNull('motivo_recusa')->whereHas('pedido', fn($q) => $q->whereIn('turma_id', $minhasTurmasIds))->count(),
            'producao_refugo_apontado' => \App\Models\ApontamentoProducao::where('aluno_id', $id)->sum('quantidade_refugo') ?? 0,
            'producao_lote_concluido' => \App\Models\ApontamentoProducao::where('aluno_id', $id)->count(),
            'expedicao_faturamento_concluido' => \App\Models\NotaFiscal::whereHas('pedido', fn($q) => $q->whereIn('turma_id', $minhasTurmasIds))->count(),
            'wms_enderecamento_concluido' => \App\Models\MateriaPrima::whereNotNull('local_estoque_id')->where(fn($q) => $q->whereIn('turma_id', $minhasTurmasIds))->count(), 
            'wms_movimentacao_total' => \App\Models\SolicitacaoSeparacao::where('status', 'Entregue')->whereHas('ordemProducao.pedido', fn($q) => $q->whereIn('turma_id', $minhasTurmasIds))->count() + \App\Models\MateriaPrima::whereNotNull('local_estoque_id')->where(fn($q) => $q->whereIn('turma_id', $minhasTurmasIds))->count(),
            'pcp_prioridade_mde' => \App\Models\OrdemProducao::whereHas('pedido', fn($q) => $q->whereIn('turma_id', $minhasTurmasIds))->count(), 
            'financeiro_caixa_positivo' => 1, 
            'bi_acesso_indicadores' => 1, 
        ];

        // --- MOTOR DE SUGESTÕES PEDAGÓGICAS ---
        foreach ($competencias as $comp) {
            if (isset($avaliacoesSalvas[$comp->id])) {
                $comp->status_atual = $avaliacoesSalvas[$comp->id]->status;
                $comp->observacao_atual = $avaliacoesSalvas[$comp->id]->observacoes_professor;
                $comp->sugerido = false;
            } else {
                $comp->observacao_atual = '';
                $comp->sugerido = true;
                $statusSugerido = 'pendente';

                if ($comp->tipo_avaliacao == 'automatica' && isset($comp->metrica_chave)) {
                    $valor = $metricas[$comp->metrica_chave] ?? 0;
                    $statusSugerido = ($valor > 0) ? 'conforme' : 'nao_conforme';
                } 
                else {
                    if ($comp->nome == 'Demonstrar Visão Sistêmica') {
                        $statusSugerido = ($metricas['bi_acesso_indicadores'] > 0) ? 'conforme' : 'nao_conforme';
                    }
                    elseif ($comp->nome == 'Trabalhar em Equipe') {
                        $statusSugerido = ($metricas['expedicao_faturamento_concluido'] > 0) ? 'conforme' : 'nao_conforme';
                    }
                    elseif ($comp->nome == 'Demonstrar Resiliência Emocional e Autogestão') {
                        $statusSugerido = ($metricas['wms_movimentacao_total'] > 0) ? 'conforme' : 'nao_conforme';
                    }
                    elseif ($comp->nome == 'Demonstrar Pensamento Analítico e Tomada de Decisão') {
                        $statusSugerido = ($metricas['financeiro_caixa_positivo'] == 1) ? 'conforme' : 'nao_conforme';
                    }
                    elseif ($comp->nome == 'Demonstrar Atenção a Detalhes') {
                        $statusSugerido = ($metricas['producao_refugo_apontado'] < 50) ? 'conforme' : 'nao_conforme';
                    }
                    elseif ($comp->nome == 'Demonstrar Capacidade de Planejamento e Organização') {
                        $statusSugerido = ($metricas['wms_enderecamento_concluido'] > 0) ? 'conforme' : 'nao_conforme';
                    }
                }

                $comp->status_atual = $statusSugerido;
            }
        }

        return view('professor.avaliar_aluno', compact('aluno', 'competencias', 'avaliacoesSalvas', 'metricas', 'bi'));
    }

    /**
     * 3. Processa e salva as avaliações e comentários atribuídos pelo professor
     */
    public function salvarAvaliacaoAluno(Request $request, $id)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);

        $request->validate([
            'avaliacoes' => 'required|array',
            'avaliacoes.*.status' => 'required|in:conforme,nao_conforme,pendente',
            'avaliacoes.*.observacoes' => 'nullable|string'
        ]);

        foreach ($request->avaliacoes as $competenciaId => $dados) {
            \Illuminate\Support\Facades\DB::table('aluno_avaliacoes')->updateOrInsert(
                [
                    'user_id' => $id,
                    'competencia_id' => $competenciaId
                ],
                [
                    'status' => $dados['status'],
                    'observacoes_professor' => $dados['observacoes'] ?? null,
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );
        }

        return redirect()->route('professor.avaliacoes.index', [
            'curso' => $request->input('curso'),
            'ano_letivo' => $request->input('ano_letivo')
        ])->with('success', 'Avaliação pedagógica salva com sucesso!');
    }
    // =========================================================================
    //  IMPORTADOR DE PLANOS DE CURSO (IA GEMINI MULTIMODAL)
    // =========================================================================

    /**
     * Exibe a tela de upload e digitação de ementa
     */
    public function formImportarCurso()
    {
        if (Auth::user()->tipo == 'aluno') abort(403);
        return view('professor.importar_curso');
    }

    /**
     * Processa a importação do PDF ou texto, envia ao Gemini e grava as competências
     */
    public function processarImportarCurso(Request $request)
    {
        // Define o limite de execução do PHP para 5 minutos para processar o PDF grande por IA
        set_time_limit(300); 
        if (Auth::user()->tipo == 'aluno') abort(403);

        $request->validate([
            'curso' => 'required|string|max:255',
            'texto_plano' => 'nullable|string',
            'arquivo_pdf' => 'nullable|file|mimes:pdf|max:30000',
        ]);

        $curso = trim($request->curso);
        $parts = [];

        if ($request->hasFile('arquivo_pdf')) {
            // Se for PDF: Converte em Base64 e envia inline na requisição
            $file = $request->file('arquivo_pdf');
            $pdfBase64 = base64_encode(file_get_contents($file->path()));
            
            $parts[] = [
                'inlineData' => [
                    'mimeType' => 'application/pdf',
                    'data' => $pdfBase64
                ]
            ];
        } elseif ($request->filled('texto_plano')) {
            // Se for texto digitado/colado
            $parts[] = [
                'text' => "Texto do plano de curso fornecido:\n" . $request->texto_plano
            ];
        } else {
            return back()->with('error', 'Por favor, insira o texto do plano de curso ou selecione um arquivo PDF.');
        }

        // PROMPT SISTÊMICO DE EXTRAÇÃO PEDAGÓGICA E CONFORMIDADE DE BANCO
        $prompt = "Você é um analista pedagógico especialista em educação profissional do SENAI-SP. 
Sua tarefa é analisar o plano de curso fornecido e extrair TODAS as unidades curriculares, capacidades técnicas, organizacionais e socioemocionais (extraia a matriz completa sem omitir nenhuma competência do plano).
Você deve alinhar cada capacidade extraída diretamente com as métricas internas de banco de dados do simulador de ERP Fábrica Digital v1.6.

REGRAS OBRIGATÓRIAS DE EXTRAÇÃO:
1. CURSO: Use exatamente o nome '{$curso}'.
2. CATEGORIA: Classifique cada registro estritamente em uma destas quatro opções: 'tecnica', 'organizacional', 'socioemocional' ou 'conhecimento'.
3. MÉTODOS DE AVALIAÇÃO E MÉTRIAS:
   - Se for uma capacidade que o software consegue medir de forma automatizada no ERP, defina 'tipo_avaliacao' como 'automatica' e associe a uma das seguintes chaves exatas no campo 'metrica_chave':
     * 'wms_picking_concluido' (separação e picking de materiais concluídos)
     * 'producao_material_solicitado' (abastecimento solicitado pelo operador de máquinas)
     * 'compras_conferencia_concluida' (checklist e conferência quantitativa de compras na doca)
     * 'compras_recusa_justificada' (devolução e recusa de cargas avariadas pelo Caos)
     * 'producao_refugo_apontado' (apontamento de perdas e refugos em máquina)
     * 'producao_lote_concluido' (conclusão física e embalagem de lotes fabricados)
     * 'expedicao_faturamento_concluido' (faturamento de ordens e emissão de notas fiscais)
     * 'wms_enderecamento_concluido' (endereçamento de matérias-primas no mapa de estoque)
     * 'wms_movimentacao_total' (movimentações gerais de armazém)
     * 'pcp_prioridade_mde' (aluno do PCP priorizando ordens e analisando MRP/BOM)
     * 'financeiro_caixa_positivo' (aluno gerenciando e mantendo caixa positivo)
     * 'bi_acesso_indicadores' (aluno acessando e analisando dados de BI)
   - Se for uma capacidade socioemocional ou organizativa que exige avaliação observacional do professor (ex: trabalho em equipe, autogestão, resiliência), defina 'tipo_avaliacao' as 'manual' e defina 'metrica_chave' como null.
4. DESCRICAO: Escreva uma descrição curta e objetiva de como essa capacidade se aplica faticamente nas telas do simulador.

RETORNE APENAS UM ARRAY JSON PURO contendo objetos com esta estrutura de colunas:
[
  {
    \"curso\": \"{$curso}\",
    \"unidade_curricular\": \"Nome da Unidade Curricular ou elemento\",
    \"categoria\": \"tecnica/socioemocional/organizacional/conhecimento\",
    \"nome\": \"Nome resumido do padrão de desempenho\",
    \"descricao\": \"Descrição detalhada de como o aluno executa no software\",
    \"tipo_avaliacao\": \"automatica/manual\",
    \"metrica_chave\": \"chave_da_metrica_ou_null\"
  }
]";

        // Adiciona o prompt textual ao array de partes
        $parts[] = ['text' => $prompt];

        try {
            $apiKey = env('GEMINI_API_KEY');
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent?key=" . $apiKey;

            // Ajuste do timeout para 45s (abaixo dos 60s do Nginx) sem retentativas longas para evitar 504
            $response = Http::timeout(45)->post($url, [
                'contents' => [
                    [
                        'parts' => $parts
                    ]
                ],
                'generationConfig' => [
                    'response_mime_type' => 'application/json',
                    'temperature' => 0.2
                ]
            ]);

            $res = $response->json();
            $textoJson = $res['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
            $competenciasExtraidas = json_decode($textoJson, true);

            if (!is_array($competenciasExtraidas) || empty($competenciasExtraidas)) {
                return back()->withInput()->with('error', 'A IA não conseguiu estruturar as competências deste arquivo. Dica: Tente utilizar a Opção A (Copiar e Colar o texto do plano de curso) para um resultado mais rápido.');
            }

            return view('professor.importar_curso_preview', compact('curso', 'competenciasExtraidas'));

        } catch (\Exception $e) {
            logger()->error('Erro na importação IA do curso: ' . $e->getMessage());
            return back()->withInput()->with('error', '⏱️ TEMPO LIMITE EXCEDIDO: O arquivo PDF enviado é muito extenso e a análise da IA ultrapassou o tempo do servidor (504 Timeout). DICA DE OURO: Utilize a Opção A (Copiar e Colar o texto das UCs/Capacidades) para a IA processar em poucos segundos!');
        }
    }

    /**
     * C. Grava definitivamente as competências revisadas e editadas pelo professor no banco
     */
    public function confirmarImportarCurso(Request $request)
    {
        if (Auth::user()->tipo == 'aluno') abort(403);

        $request->validate([
            'curso' => 'required|string|max:255',
            'competencias' => 'required|array|min:1',
            'competencias.*.unidade_curricular' => 'nullable|string|max:255',
            'competencias.*.categoria' => 'required|in:tecnica,organizacional,socioemocional,conhecimento',
            'competencias.*.nome' => 'required|string|max:255',
            'competencias.*.descricao' => 'required|string',
            'competencias.*.tipo_avaliacao' => 'required|in:automatica,manual',
            'competencias.*.metrica_chave' => 'nullable|string|max:255',
        ]);

        $curso = trim($request->curso);

        // 1. LIMPEZA SEGURA CONTRA DUPLICAÇÕES
        \Illuminate\Support\Facades\DB::table('competencias_cursos')
            ->whereRaw('LOWER(curso) = ?', [strtolower($curso)])
            ->delete();

        // 2. FILTRA E SALVA APENAS AS CAPACIDADES QUE O PROFESSOR AUTORIZOU (CHECKBOX 'importar')
        $itensParaSalvar = [];
        foreach ($request->competencias as $comp) {
            // Se o professor desmarcou o checkbox desta linha, ela é descartada e não entra no banco
            if (!isset($comp['importar']) || $comp['importar'] != '1') {
                continue;
            }

            $itensParaSalvar[] = [
                'curso' => $curso,
                'unidade_curricular' => $comp['unidade_curricular'] ?? null,
                'categoria' => $comp['categoria'],
                'nome' => $comp['nome'],
                'descricao' => $comp['descricao'],
                'tipo_avaliacao' => $comp['tipo_avaliacao'],
                'metrica_chave' => !empty($comp['metrica_chave']) && $comp['metrica_chave'] !== 'null' ? $comp['metrica_chave'] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (empty($itensParaSalvar)) {
            return redirect()->route('professor.avaliacoes.index')->with('error', 'Importação cancelada: Nenhuma capacidade foi selecionada para gravação.');
        }

        // Grava as capacidades autorizadas no banco de dados em lote
        \Illuminate\Support\Facades\DB::table('competencias_cursos')->insert($itensParaSalvar);

        $qty = count($itensParaSalvar);
        return redirect()->route('professor.avaliacoes.index')->with('success', "Plano de Curso '{$curso}' revisado e importado com sucesso! {$qty} capacidades pedagógicas foram gravadas.");
    }

    /**
     * D. Exclui a matriz de competências de um curso (Protegido por Senha Mestra)
     */
    public function excluirMatrizCurso(Request $request)
    {
        // Esta parte do middleware aqui dentro garante que o request venha com a senha
        // E o middleware da rota (web.php) faz a validação da senha_mestra antes de chegar aqui.
        
        $curso = trim($request->curso);

        $deletado = \Illuminate\Support\Facades\DB::table('competencias_cursos')
            ->where('curso', '=', $curso)
            ->delete();

        if ($deletado > 0) {
            return redirect()->route('professor.avaliacoes.index')->with('success', "Matriz do curso '{$curso}' excluída!");
        }

        return redirect()->route('professor.avaliacoes.index')->with('error', "Erro ao excluir curso.");
    }

} // <--- FIM DA CLASSE