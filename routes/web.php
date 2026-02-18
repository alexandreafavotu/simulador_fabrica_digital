<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\AlunoController;
use App\Http\Controllers\MonitoramentoController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
*/
Route::get('/', function () { return view('welcome'); });

require __DIR__.'/auth.php';


// Rotas do Sistema de Ajuda Dinâmico
    Route::post('/ajuda/get', [App\Http\Controllers\AjudaController::class, 'getConteudo'])->name('ajuda.get');
    Route::post('/ajuda/salvar', [App\Http\Controllers\AjudaController::class, 'salvarConteudo'])->name('ajuda.salvar');

/*
|--------------------------------------------------------------------------
| ROTA INTELIGENTE (O GPS)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $user = Auth::user();

    // 1. Professor vai para o painel dele (SEM EXCEÇÕES)
    if ($user->tipo == 'professor') {
        return redirect()->route('professor.dashboard');
    }

    // 2. Aluno é redirecionado conforme o setor
    $aluno = \App\Models\Aluno::where('user_id', $user->id)->first();

    // Se o aluno não tem turma, mostra tela padrão (vazia)
    if (!$aluno) return view('dashboard'); 

    // GPS: Redireciona para o setor correto
    switch ($aluno->setor) {
        case 'pcp':          return redirect()->route('aluno.pcp.dashboard');
        case 'compras':      return redirect()->route('aluno.compras.dashboard');
        case 'almoxarifado': return redirect()->route('aluno.almoxarifado.dashboard');
        case 'producao':     return redirect()->route('aluno.producao.dashboard');
        case 'expedicao':    return redirect()->route('aluno.expedicao.dashboard');
        case 'vendas':       return redirect()->route('aluno.vendas.index');
        case 'embalagem':    return redirect()->route('aluno.embalagem.dashboard');
        default:             return view('dashboard');
    }
})->middleware(['auth', 'verified'])->name('dashboard');



/*
|--------------------------------------------------------------------------
| ROTAS PROTEGIDAS (AUTENTICADAS)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // --- PERFIL ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



    // Rotas para painel gestao a vista
    // Adicione no grupo de middleware auth
// Adicione o {turma_id?} com a interrogação para ser opcional
Route::get('/gestao-a-vista/{turma_id?}', [App\Http\Controllers\AlunoController::class, 'painelGestaoVista'])->name('aluno.gestao_vista');

    // =========================================================================
    //  ÁREA DO PROFESSOR (Game Master) - CORRIGIDO: USANDO O NOME 'professor'
    // =========================================================================
    Route::middleware('professor')->group(function () {
        
        Route::get('/professor', [ProfessorController::class, 'index'])->name('professor.dashboard');
        Route::put('/professor/regras', [ProfessorController::class, 'salvarRegras'])->name('professor.regras.salvar');

        // --- GESTÃO DE ALUNOS (NOVO MODELO: GLOBAL & ESCALAÇÃO) ---
        Route::get('/professor/global/alunos', [ProfessorController::class, 'gerenciarCadastroGlobal'])->name('professor.global.alunos');
        Route::post('/professor/global/alunos', [ProfessorController::class, 'salvarAlunoGlobal'])->name('professor.global.store');
        Route::delete('/professor/global/alunos/{user}', [ProfessorController::class, 'excluirAlunoGlobal'])->name('professor.global.destroy');
        Route::post('/professor/global/alunos/{user}/resetar', [ProfessorController::class, 'resetarSenhaAluno'])->name('professor.global.resetar');

        Route::get('/professor/turmas', [ProfessorController::class, 'gerenciarTurmas'])->name('professor.turmas.index');
        Route::post('/professor/turmas', [ProfessorController::class, 'salvarTurma'])->name('professor.turmas.store');
        Route::get('/professor/turmas/{turma}/editar', [ProfessorController::class, 'editarTurma'])->name('professor.turmas.edit');
        Route::put('/professor/turmas/{turma}', [ProfessorController::class, 'atualizarTurma'])->name('professor.turmas.update');
Route::delete('/professor/turmas/{turma}', [ProfessorController::class, 'excluirTurma'])
    ->middleware(\App\Http\Middleware\ProtecaoSenhaMestra::class) // <--- ADICIONAMOS ISSO
    ->name('professor.turmas.destroy');

    // Rota para Bloquear/Desbloquear Usuários (Alunos e Professores)
        Route::patch('/professor/usuario/{user}/status', [ProfessorController::class, 'toggleStatusUsuario'])->name('professor.usuario.toggle');
        
        Route::get('/professor/turmas/{id}/equipe', [ProfessorController::class, 'gerenciarEquipeTurma'])->name('professor.turmas.equipe');
        Route::post('/professor/turmas/{id}/vincular', [ProfessorController::class, 'vincularAluno'])->name('professor.turmas.vincular');
        Route::delete('/professor/turmas/aluno/{id}', [ProfessorController::class, 'desvincularAluno'])->name('professor.turmas.desvincular');
        Route::put('/professor/turmas/aluno/{id}/setor', [ProfessorController::class, 'definirSetor'])->name('professor.turmas.setor');

        Route::put('/professor/turmas/{id}/capacidade', [ProfessorController::class, 'atualizarCapacidadeProducao'])->name('professor.turmas.capacidade');
        // Rota para Adormecer/Acordar Turma
        Route::patch('/professor/turmas/{turma}/arquivar', [ProfessorController::class, 'arquivarTurma'])->name('professor.turmas.arquivar');

        // Rota legada
        Route::get('/professor/alunos', [ProfessorController::class, 'gerenciarAlunos'])->name('professor.alunos.index');

        // Cadastros Base
        Route::get('/professor/materias-primas', [ProfessorController::class, 'gerenciarMateriasPrimas'])->name('professor.materias-primas.index');
        Route::post('/professor/materias-primas', [ProfessorController::class, 'salvarMateriaPrima'])->name('professor.materias-primas.store');
        Route::get('/professor/materias-primas/{materiaPrima}/editar', [ProfessorController::class, 'editarMateriaPrima'])->name('professor.materias-primas.edit');
        Route::put('/professor/materias-primas/{materiaPrima}', [ProfessorController::class, 'atualizarMateriaPrima'])->name('professor.materias-primas.update');
        

        Route::get('/professor/produtos-acabados', [ProfessorController::class, 'gerenciarProdutosAcabados'])->name('professor.produtos-acabados.index');
        Route::post('/professor/produtos-acabados', [ProfessorController::class, 'salvarProdutoAcabado'])->name('professor.produtos-acabados.store');
        Route::get('/professor/produtos-acabados/{produto}/editar', [ProfessorController::class, 'editarProdutoAcabado'])->name('professor.produtos-acabados.edit');
        Route::put('/professor/produtos-acabados/{produto}', [ProfessorController::class, 'atualizarProdutoAcabado'])->name('professor.produtos-acabados.update');
        // Excluir Matéria-Prima (Protegido)
        Route::delete('/professor/materias-primas/{materiaPrima}', [ProfessorController::class, 'excluirMateriaPrima'])
            ->middleware(\App\Http\Middleware\ProtecaoSenhaMestra::class)
            ->name('professor.materias-primas.destroy');

        // Excluir Produto Acabado (Protegido)
        Route::delete('/professor/produtos-acabados/{produto}', [ProfessorController::class, 'excluirProdutoAcabado'])
            ->middleware(\App\Http\Middleware\ProtecaoSenhaMestra::class)
            ->name('professor.produtos-acabados.destroy');
        Route::post('/professor/produtos-acabados/{produto}/adicionar-componente', [ProfessorController::class, 'adicionarComponente'])->name('professor.produtos-acabados.adicionarComponente');
        Route::delete('/professor/produtos-acabados/{produto}/remover-componente/{materiaPrima}', [ProfessorController::class, 'removerComponente'])->name('professor.produtos-acabados.removerComponente');

        Route::get('/professor/clientes', [ProfessorController::class, 'gerenciarClientes'])->name('professor.clientes.index');
        Route::post('/professor/clientes', [ProfessorController::class, 'salvarCliente'])->name('professor.clientes.store');
        Route::get('/professor/clientes/{cliente}/editar', [ProfessorController::class, 'editarCliente'])->name('professor.clientes.edit');
        Route::put('/professor/clientes/{cliente}', [ProfessorController::class, 'atualizarCliente'])->name('professor.clientes.update');
        // Rota de Exclusão de Clientes Protegida por Senha Mestra
        Route::delete('/professor/clientes/{cliente}', [ProfessorController::class, 'excluirCliente'])
            ->middleware(\App\Http\Middleware\ProtecaoSenhaMestra::class)
            ->name('professor.clientes.destroy');

        Route::get('/professor/fornecedores', [ProfessorController::class, 'gerenciarFornecedores'])->name('professor.fornecedores.index');
        Route::post('/professor/fornecedores', [ProfessorController::class, 'salvarFornecedor'])->name('professor.fornecedores.store');
        // Rota de Exclusão de Fornecedores Protegida por Senha Mestra
        Route::delete('/professor/fornecedores/{fornecedor}', [ProfessorController::class, 'excluirFornecedor'])
            ->middleware(\App\Http\Middleware\ProtecaoSenhaMestra::class)
            ->name('professor.fornecedores.destroy');


            // Rotas para Gerenciar Acessibilidade do Aluno
Route::get('/professor/alunos/{user}/editar', [App\Http\Controllers\ProfessorController::class, 'editarAluno'])->name('professor.alunos.editar');
Route::put('/professor/alunos/{user}/atualizar', [App\Http\Controllers\ProfessorController::class, 'atualizarAluno'])->name('professor.alunos.atualizar');

            // Rota para salvar o Limite de Vendas Diárias
Route::put('/professor/turmas/{id}/limite-vendas', [ProfessorController::class, 'atualizarLimiteVendas'])->name('professor.turmas.limite_vendas');



        // --- GESTÃO DE PEDIDOS (PROFESSOR) ---
        
        // Listagem e Cadastro
        Route::get('/professor/pedidos-venda', [ProfessorController::class, 'gerenciarPedidosVenda'])->name('professor.pedidos-venda.index');
        Route::post('/professor/pedidos-venda', [ProfessorController::class, 'salvarPedidoVenda'])->name('professor.pedidos-venda.store');
        
        // Visualizar Detalhes
        Route::get('/professor/pedidos-venda/{pedidoVenda}', [ProfessorController::class, 'verDetalhesPedidoVenda'])->name('professor.pedidos-venda.show');
        
        // Edição
        Route::get('/professor/pedidos-venda/{pedidoVenda}/editar', [ProfessorController::class, 'editarPedidoVenda'])->name('professor.pedidos-venda.edit');
        Route::put('/professor/pedidos-venda/{pedidoVenda}', [ProfessorController::class, 'atualizarPedidoVenda'])->name('professor.pedidos-venda.update');
        
        // Itens do Pedido
        Route::post('/professor/pedidos-venda/{pedidoVenda}/adicionar-item', [ProfessorController::class, 'adicionarItemPedido'])->name('professor.pedidos-venda.addItem');
        Route::delete('/professor/pedidos-venda/remover-item/{pedidoVendaItem}', [ProfessorController::class, 'removerItemPedido'])->name('professor.pedidos-venda.removeItem');
        // Rota para Atualizar a Quantidade do Item
        Route::put('/professor/pedidos-venda/item/{item}', [ProfessorController::class, 'atualizarItemPedido'])->name('professor.pedidos-venda.updateItem');

        // --- A MUDANÇA ESTÁ AQUI EMBAIXO ---
        // Excluir/Cancelar (Adicionamos o Middleware de Senha Mestra)
        Route::delete('/professor/pedidos-venda/{pedidoVenda}', [ProfessorController::class, 'excluirPedidoVenda'])
            ->middleware(\App\Http\Middleware\ProtecaoSenhaMestra::class)
            ->name('professor.pedidos-venda.destroy');
            Route::get('/professor/turmas/{turma}/perfil', [ProfessorController::class, 'verPerfilTurma'])->name('professor.turmas.perfil');

        Route::post('/professor/simulacao/avancar', [ProfessorController::class, 'avancarTempo'])->name('professor.simulacao.avancar');
        Route::post('/professor/simulacao/resetar', [ProfessorController::class, 'resetarTempo'])->name('professor.simulacao.resetar');
        Route::post('/professor/simulacao/status', [ProfessorController::class, 'alterarStatusJogo'])->name('professor.simulacao.status');
        // Rota protegida por Senha Mestra (Geração de WMS é destrutiva)
        Route::post('/professor/estoque/gerar-mapa', [ProfessorController::class, 'gerarMapaEstoque'])
            ->middleware(\App\Http\Middleware\ProtecaoSenhaMestra::class)
            ->name('professor.estoque.gerar_mapa');

        if (class_exists(MonitoramentoController::class)) {
            Route::get('/professor/monitorar/{turma_id}', [MonitoramentoController::class, 'index'])->name('professor.monitoramento.index');
            Route::get('/professor/monitorar/{turma_id}/pcp', [MonitoramentoController::class, 'verPcp'])->name('professor.monitoramento.pcp');
            Route::get('/professor/monitorar/{turma_id}/compras', [MonitoramentoController::class, 'verCompras'])->name('professor.monitoramento.compras');
            Route::get('/professor/monitorar/{turma_id}/almoxarifado', [MonitoramentoController::class, 'verAlmoxarifado'])->name('professor.monitoramento.almoxarifado');
            Route::get('/professor/monitorar/{turma_id}/mapa', [MonitoramentoController::class, 'verMapa'])->name('professor.monitoramento.mapa');
            Route::get('/professor/monitorar/{turma_id}/estoque', [MonitoramentoController::class, 'verEstoque'])->name('professor.monitoramento.estoque');
            Route::get('/professor/monitorar/{turma_id}/embalagem', [MonitoramentoController::class, 'verEmbalagem'])->name('professor.monitoramento.embalagem');
        }
        // --- GESTÃO DE PROFESSORES (ADMIN) ---
        Route::get('/professor/global/docentes', [ProfessorController::class, 'gerenciarProfessores'])->name('professor.global.professores');
        Route::post('/professor/global/docentes', [ProfessorController::class, 'salvarProfessor'])->name('professor.global.professores.store');
        Route::delete('/professor/global/docentes/{user}', [ProfessorController::class, 'excluirProfessor'])->name('professor.global.professores.destroy');
    });

    

    // --- PAINEL DO CAOS ---
        Route::get('/professor/caos/{turma_id}', [App\Http\Controllers\CaosController::class, 'index'])->name('professor.caos.index');
        Route::post('/professor/caos/{turma_id}/bloqueio', [App\Http\Controllers\CaosController::class, 'aplicarBloqueio'])->name('professor.caos.bloqueio');
        Route::post('/professor/caos/{turma_id}/maquina', [App\Http\Controllers\CaosController::class, 'quebrarMaquina'])->name('professor.caos.maquina');
        Route::post('/professor/caos/{turma_id}/atraso', [App\Http\Controllers\CaosController::class, 'atrasarFornecedor'])->name('professor.caos.atraso');
        Route::post('/professor/caos/{turma_id}/mensagem', [App\Http\Controllers\CaosController::class, 'salvarMensagem'])->name('professor.caos.mensagem');
        Route::post('/professor/caos/{turma_id}/limpar', [App\Http\Controllers\CaosController::class, 'limparCaos'])->name('professor.caos.limpar');

        Route::post('/professor/caos/{turma_id}/sabotar', [App\Http\Controllers\CaosController::class, 'sabotarCarga'])->name('professor.caos.sabotar');
        
        Route::post('/professor/caos/{turma_id}/sabotar-carga', [App\Http\Controllers\CaosController::class, 'sabotarCarga'])->name('professor.caos.sabotar_carga');
        Route::post('/professor/caos/{turma_id}/sabotar-producao', [App\Http\Controllers\CaosController::class, 'sabotarProducao'])->name('professor.caos.sabotar_producao');

    Route::get('/professor/monitorar/{turma_id}/vendas', [MonitoramentoController::class, 'verVendas'])->name('professor.monitoramento.vendas');
Route::get('/professor/monitorar/{turma_id}/producao', [MonitoramentoController::class, 'verProducao'])->name('professor.monitoramento.producao');
Route::get('/professor/monitorar/{turma_id}/expedicao', [MonitoramentoController::class, 'verExpedicao'])->name('professor.monitoramento.expedicao');
Route::get('/professor/monitorar/{turma_id}/embalagem', [MonitoramentoController::class, 'verEmbalagem'])->name('professor.monitoramento.embalagem');


// Rota de Bloqueio (Pausa)
    Route::get('/aluno/pausado', function () {
        return view('aluno.pausado');
    })->name('aluno.jogo.pausado');


    ///

    // Rota de Business Intelligence (Gráficos)
Route::get('/professor/monitorar/{turma_id}/bi', [App\Http\Controllers\RelatoriosController::class, 'index'])->name('professor.monitoramento.bi');

    // =========================================================================
    //  ÁREA DO ALUNO (OPERACIONAL)
    // =========================================================================

    Route::get('/aluno/empresa/perfil', [AlunoController::class, 'verPerfilEmpresa'])->name('aluno.empresa.perfil');

    // --- Módulo VENDAS ---
    Route::middleware('setor:vendas')->group(function () {
        Route::get('/aluno/vendas', [AlunoController::class, 'gerenciarVendas'])->name('aluno.vendas.index');
        Route::post('/aluno/vendas', [AlunoController::class, 'salvarVenda'])->name('aluno.vendas.store');
    });

    // --- Módulo PCP ---
    Route::middleware('setor:pcp')->group(function () {
        Route::get('/aluno/pcp', [AlunoController::class, 'dashboardPcp'])->name('aluno.pcp.dashboard');
        Route::get('/aluno/pcp/analise/{id}', [AlunoController::class, 'analiseMRP'])->name('aluno.pcp.analise');
        Route::post('/aluno/pcp/gerar-ordens/{id}', [AlunoController::class, 'gerarOrdens'])->name('aluno.pcp.gerar_ordens');
    });

    // --- Módulo COMPRAS ---
    Route::middleware('setor:compras')->group(function () {
        Route::get('/aluno/compras', [AlunoController::class, 'dashboardCompras'])->name('aluno.compras.dashboard');
        Route::get('/aluno/compras/confirmar/{id}', [AlunoController::class, 'confirmarCompra'])->name('aluno.compras.confirmar');
        Route::post('/aluno/compras/efetivar/{id}', [AlunoController::class, 'efetivarCompra'])->name('aluno.compras.efetivar');
        Route::post('/aluno/compras/recomprar/{id}', [AlunoController::class, 'recomprarMaterial'])->name('aluno.compras.recomprar');
    });

    // --- Módulo ALMOXARIFADO (WMS) ---
    Route::middleware('setor:almoxarifado')->group(function () {
        Route::get('/aluno/almoxarifado', [AlunoController::class, 'dashboardAlmoxarifado'])->name('aluno.almoxarifado.dashboard');
        Route::post('/aluno/almoxarifado/receber/{id}', [AlunoController::class, 'receberMaterial'])->name('aluno.almoxarifado.receber');
        Route::get('/aluno/almoxarifado/recusar/{id}', [AlunoController::class, 'formRecusa'])->name('aluno.almoxarifado.recusar');
        Route::post('/aluno/almoxarifado/recusar/{id}', [AlunoController::class, 'salvarRecusa'])->name('aluno.almoxarifado.salvar-recusa');
        
        Route::get('/aluno/almoxarifado/mapa', [AlunoController::class, 'mapaEstoque'])->name('aluno.almoxarifado.mapa');
        Route::post('/aluno/almoxarifado/armazenar/{id}', [AlunoController::class, 'armazenarMaterial'])->name('aluno.almoxarifado.armazenar');
        Route::get('/aluno/almoxarifado/inventario', [AlunoController::class, 'estoqueAlmoxarifado'])->name('aluno.almoxarifado.estoque');
        
        Route::get('/aluno/almoxarifado/separar/{id}', [AlunoController::class, 'iniciarSeparacao'])->name('aluno.almoxarifado.separar');
        Route::post('/aluno/almoxarifado/confirmar-separacao/{id}', [AlunoController::class, 'confirmarSeparacao'])->name('aluno.almoxarifado.confirmar_separacao');
    });

    // --- Módulo PRODUÇÃO ---
    Route::middleware('setor:producao')->group(function () {
        Route::get('/aluno/producao', [AlunoController::class, 'dashboardProducao'])->name('aluno.producao.dashboard');
        Route::post('/aluno/producao/assumir/{id}', [AlunoController::class, 'assumirOrdem'])->name('aluno.producao.assumir');
        Route::post('/aluno/producao/solicitar/{id}', [AlunoController::class, 'solicitarMaterial'])->name('aluno.producao.solicitar');
        Route::post('/aluno/producao/iniciar/{id}', [AlunoController::class, 'iniciarProducao'])->name('aluno.producao.iniciar');
        Route::get('/aluno/producao/apontar/{id}', [AlunoController::class, 'formApontamento'])->name('aluno.producao.apontar');
        Route::post('/aluno/producao/apontar/{id}', [AlunoController::class, 'salvarApontamento'])->name('aluno.producao.salvar_apontamento');
    });

    // --- Módulo EMBALAGEM ---
    Route::middleware('setor:embalagem')->group(function () {
        Route::get('/aluno/embalagem', [AlunoController::class, 'dashboardEmbalagem'])->name('aluno.embalagem.dashboard');
        Route::post('/aluno/embalagem/confirmar/{id}', [AlunoController::class, 'confirmarEmbalagem'])->name('aluno.embalagem.confirmar');
    });

    // --- Módulo EXPEDIÇÃO ---
    Route::middleware('setor:expedicao')->group(function () {
        Route::get('/aluno/expedicao', [AlunoController::class, 'dashboardExpedicao'])->name('aluno.expedicao.dashboard');
        Route::get('/aluno/expedicao/conferir/{id}', [AlunoController::class, 'conferirExpedicao'])->name('aluno.expedicao.conferir');
        Route::post('/aluno/expedicao/faturar/{id}', [AlunoController::class, 'faturarPedido'])->name('aluno.expedicao.faturar');
        Route::get('/aluno/expedicao/nota/{id}', [AlunoController::class, 'visualizarNota'])->name('aluno.expedicao.nota');
    });

    // --- CADASTROS GERAIS (ALUNOS) ---
    // (Estas rotas não têm restrição de setor, mas podem ter se você quiser)
    Route::get('/aluno/materias-primas', [AlunoController::class, 'gerenciarMateriasPrimas'])->name('aluno.materias-primas.index');
    Route::post('/aluno/materias-primas', [AlunoController::class, 'salvarMateriaPrima'])->name('aluno.materias-primas.store');
    
    
    Route::get('/aluno/clientes', [AlunoController::class, 'gerenciarClientes'])->name('aluno.clientes.index');
    Route::post('/aluno/clientes', [AlunoController::class, 'salvarCliente'])->name('aluno.clientes.store');
    
    Route::get('/aluno/fornecedores', [AlunoController::class, 'gerenciarFornecedores'])->name('aluno.fornecedores.index');
    Route::post('/aluno/fornecedores', [AlunoController::class, 'salvarFornecedor'])->name('aluno.fornecedores.store');

    // Rotas de Edição de Fornecedores
        Route::get('/professor/fornecedores/{fornecedor}/editar', [ProfessorController::class, 'editarFornecedor'])->name('professor.fornecedores.edit');
        Route::put('/professor/fornecedores/{fornecedor}', [ProfessorController::class, 'atualizarFornecedor'])->name('professor.fornecedores.update');





});