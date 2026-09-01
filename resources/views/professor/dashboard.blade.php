<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
            <span>🕹️</span> {{ __('Painel de Controle (Game Master)') }}
        </h2>
    </x-slot>

    <div class="py-8 bg-yellow-50 min-h-screen">
        <div class="max-w-[98%] mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- BOTÃO SALA DOS PROFESSORES (ADMIN) --}}
            @if(Auth::id() === 1)
                <a href="{{ route('professor.global.professores') }}" 
                   class="group relative inline-flex items-center gap-2 px-5 py-2 bg-purple-600 text-white font-black text-sm rounded-lg border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all duration-200 hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none hover:bg-purple-700 mb-2">
                    <span class="text-xl group-hover:rotate-12 transition-transform duration-300">👑</span>
                    <span>Sala dos Professores - Cadastro</span>
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded border border-black shadow-sm rotate-12">
                        ADMIN
                    </span>
                </a>
            @endif
            
            {{-- 1. ESTATÍSTICAS (MANTENDO A LÓGICA ORIGINAL, VISUAL NOVO) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Banco de Talentos -->
                <div class="bg-green-200 border-2 border-black rounded-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] p-4 flex justify-between items-center hover:-translate-y-1 transition">
                    <div>
                        <p class="text-[10px] font-bold text-black-500 uppercase tracking-widest">Banco de Talentos</p>
                        <p class="text-3xl font-black text-gray-800 mt-1">{{ $stats['total_alunos'] ?? 0 }}</p>
                    </div>
                    <div class="bg-indigo-100 p-2 rounded border-2 border-black text-indigo-600 text-2xl">
                        👥
                    </div>
                </div>

                <!-- Turmas -->
                <div class="bg-green-200 border-2 border-black rounded-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] p-4 flex justify-between items-center hover:-translate-y-1 transition">
                    <div>
                        <p class="text-[10px] font-bold text-black-500 uppercase tracking-widest">Turmas Criadas</p>
                        <p class="text-3xl font-black text-gray-800 mt-1">{{ $stats['total_turmas'] ?? 0 }}</p>
                    </div>
                    <div class="bg-blue-100 p-2 rounded border-2 border-black text-blue-600 text-2xl">
                        🏭
                    </div>
                </div>

                <!-- Simulações -->
                <div class="bg-green-200 border-2 border-black rounded-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] p-4 flex justify-between items-center hover:-translate-y-1 transition">
                    <div>
                        <p class="text-[10px] font-bold text-black-500 uppercase tracking-widest">Simulações Rodando</p>
                        <p class="text-3xl font-black text-gray-800 mt-1">{{ $stats['turmas_ativas'] ?? 0 }}</p>
                    </div>
                    <div class="bg-green-100 p-2 rounded border-2 border-black text-green-600 text-2xl animate-pulse">
                        🟢
                    </div>
                </div>
            </div>
            
            {{-- 2. CENTRO DE COMANDO --}}
            <div>
                <div class="bg-gray-100 flex flex-col md:flex-row justify-between items-center mb-4 gap-4 border-b-4 border-black pb-2">
                    <h3 class="font-black text-xl text-indigo-900 flex items-center">
                        <span class="text-2xl mr-2">🕹️</span> Centro de Comando
                    </h3>

                    {{-- BUSCADOR (LÓGICA MANTIDA) --}}
                    <form method="GET" action="{{ route('professor.dashboard') }}" class="flex gap-2 w-full md:w-auto">
                        <input type="text" name="busca_turma" value="{{ request('busca_turma') }}" placeholder="Buscar Turma ou Empresa..." 
                               class="text-xs font-bold rounded border-2 border-black shadow-[2px_2px_0px_0px_black] focus:ring-0 focus:border-indigo-500 w-full md:w-64">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-1.5 rounded border-2 border-black shadow-[2px_2px_0px_0px_black] font-bold text-xs hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none hover:bg-indigo-700 transition">BUSCAR</button>
                        @if(request('busca_turma'))
                            <a href="{{ route('professor.dashboard') }}" class="bg-gray-200 text-gray-700 px-3 py-1.5 rounded border-2 border-black font-bold text-xs flex items-center hover:bg-red-200 transition">X</a>
                        @endif
                    </form>
                </div>

                @if($turmas->isEmpty())
                    <div class="bg-white border-2 border-dashed border-gray-400 p-10 rounded-lg text-center text-gray-500 italic">
                        Nenhuma turma encontrada. <a href="{{ route('professor.turmas.index') }}" class="text-indigo-600 font-bold underline">Criar Turma</a>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($turmas as $turma)
                            <!-- CARD DA TURMA -->
                            <div class="bg-green-100 rounded-lg border-2 border-black shadow-[4px_4px_0px_0px_black] p-3 transition hover:-translate-y-1 relative overflow-hidden">
                                
                                {{-- Indicador Lateral --}}
                                <div class="absolute left-0 top-0 bottom-0 w-2 {{ $turma->jogo_ativo ? 'bg-green-500' : 'bg-red-400' }} border-r border-black"></div>

                                <div class="flex flex-col xl:flex-row items-stretch gap-4 pl-3 w-full">
                                    
                                    <!-- 1. IDENTIFICAÇÃO (Esquerda) -->
                                    <div class="flex-none xl:w-1/4 w-full min-w-[200px] flex flex-col justify-center">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4 class="font-black text-lg text-gray-800 truncate" title="{{ $turma->nome_empresa }}">{{ $turma->nome_empresa }}</h4>
                                            @if($turma->jogo_ativo)
                                                <span class="bg-green-100 text-green-800 px-1.5 py-0.5 rounded text-[9px] font-black border border-black animate-pulse">RODANDO</span>
                                            @else
                                                <span class="bg-red-100 text-red-800 px-1.5 py-0.5 rounded text-[9px] font-black border border-black">PAUSADO</span>
                                            @endif
                                        </div>
                                        <div class="text-xs font-bold text-gray-500 mb-2">{{ $turma->nome }}</div>
                                        
                                        {{-- Data do Jogo --}}
                                        <div class="bg-green-100 inline-flex items-center gap-1 bg-gray-50 border border-gray-300 rounded px-2 py-1 self-start">
                                            <span class="text-sm">🕰️</span>
                                            <p class="text-indigo-700 font-black text-xs leading-none">
                                                {{ $turma->data_jogo->format('d/m/Y H:i') }}
                                                <span class="text-black-400 font-normal uppercase text-[9px] ml-1">({{ $turma->data_jogo->locale('pt_BR')->dayName }})</span>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- 2. NÚCLEO DE OPERAÇÕES (Centro - 2 Linhas Organizdas) -->
                                    <div class="flex-1 w-full flex flex-col justify-center gap-3 bg-gray-200 p-3 rounded border border-gray-400 shadow-inner">
                                        
                                        <!-- LINHA 1: GESTÃO E IA (VISUAL ALINHADO + CONTEÚDO ORIGINAL RESTAURADO) -->
<div class="grid grid-cols-2 xl:grid-cols-4 gap-2 w-full">
    
    {{-- 1. Botão Gênesis --}}
    <form action="{{ route('professor.genesis.gerar') }}" method="POST" class="w-full h-full m-0">
        @csrf
        <input type="hidden" name="turma_id" value="{{ $turma->id }}">
        <button type="submit" title="Gênesis: Gerar Estrutura de Matérias-Primas, Fornecedores e Clientes via IA" 
                class="w-full h-full bg-white border-2 border-black p-1 rounded shadow-[2px_2px_0px_0px_black] hover:bg-purple-50 hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition-all flex flex-col items-center justify-center gap-1">
            <span class="text-sm">✨</span>
            <span class="text-[9px] font-black uppercase whitespace-nowrap">Gênesis IA</span>
        </button>
    </form>

    {{-- 2. Gerador de Demanda --}}
    <div class="bg-pink-50 border-2 border-black p-1.5 rounded shadow-[2px_2px_0px_0px_black] flex flex-col justify-center w-full h-full">
        <label class="block text-[8px] font-black uppercase text-pink-700 italic text-center mb-1 whitespace-nowrap">🚀 Demanda IA:</label>
        <form action="{{ route('professor.turmas.gerar_demanda', $turma->id) }}" method="POST" class="flex items-center justify-center gap-1 m-0 w-full">
            @csrf
            <input type="number" name="quantidade_pedidos" value="5" min="1" max="20" class="w-8 h-6 p-0 border border-black text-[10px] font-black text-center focus:ring-0">
            <button type="submit" title="Demanda: Gerar pedidos de vendas via IA" class="bg-pink-500 text-white border border-black px-2 h-6 text-[9px] font-black uppercase hover:bg-pink-600 transition flex-1">
                Gerar
            </button>
        </form>
    </div>

    {{-- 3. Gestão Financeira --}}
    <a href="{{ route('professor.turmas.financeiro', $turma->id) }}" title="Gestão Financeira da Empresa" 
       class="w-full h-full bg-yellow-400 border-2 border-black p-1 rounded shadow-[2px_2px_0px_0px_black] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition-all flex flex-col items-center justify-center gap-1">
        <span class="text-sm">💰</span>
        <span class="text-[9px] font-black uppercase whitespace-nowrap">Financeiro</span>
    </a>

    {{-- 4. Relógio Auto --}}
    <div class="bg-blue-50 border-2 border-black p-1.5 rounded shadow-[2px_2px_0px_0px_black] flex flex-col justify-center w-full h-full">
        <label class="block text-[8px] font-black uppercase text-blue-800 text-center mb-1 whitespace-nowrap">🕒 DATA AUTOMÁTICA MIN.:</label>
        <form action="{{ route('professor.turmas.relogio', $turma->id) }}" method="POST" class="flex items-center justify-center gap-1 m-0 w-full">
            @csrf @method('PUT')
            <input type="number" name="intervalo" value="{{ $turma->relogio_intervalo }}" class="w-8 h-6 p-0 border border-black text-[10px] font-black text-center focus:ring-0">
            <button type="submit" name="status" value="{{ $turma->relogio_ativo ? '0' : '1' }}" title="Automatizador do Data-Game" class="{{ $turma->relogio_ativo ? 'bg-green-500' : 'bg-red-500' }} text-white border border-black px-2 h-6 text-[9px] font-black uppercase transition flex-1">
                {{ $turma->relogio_ativo ? 'LIGADO' : 'DESLIGADO' }}
            </button>
        </form>
    </div>

</div>

                                        {{-- RÉGUA DE CALIBRAGEM (NOVOS CONTROLES DE REGRAS) --}}
<div class="grid grid-cols-2 gap-2 mt-3">
    
    {{-- 1. COTA DE VENDAS (Limite Diário) --}}
    <div class="bg-green-50 border-2 border-black p-1.5 shadow-[2px_2px_0px_0px_black] flex flex-col justify-center">
        <label class="text-[8px] font-black uppercase text-green-900 mb-1 text-center">Cota Vendas/Aluno</label>
        {{-- Rota confirmada no seu web.php --}}
        <form action="{{ route('professor.turmas.limite_vendas', $turma->id) }}" method="POST" class="flex gap-1 m-0">
            @csrf @method('PUT')
            <input type="number" name="limite_vendas_por_aluno" value="{{ $turma->limite_vendas_por_aluno ?? 10 }}" min="1" max="999" 
                   class="w-full h-6 p-0 border border-black text-[10px] font-black text-center focus:ring-0" title="Pedidos por dia">
            <button type="submit" class="bg-green-600 text-white border border-black px-1.5 h-6 text-[8px] font-black uppercase hover:bg-green-700 transition" title="COTA DE VENDAS (Limite Diário)">
                OK
            </button>
        </form>
    </div>

    {{-- 2. CAPACIDADE PRODUTIVA (Limite de OPs) --}}
    <div class="bg-yellow-50 border-2 border-black p-1.5 shadow-[2px_2px_0px_0px_black] flex flex-col justify-center">
        <label class="text-[8px] font-black uppercase text-yellow-900 mb-1 text-center">Capacidade Produção/Aluno</label>
        {{-- Rota confirmada no seu web.php --}}
        <form action="{{ route('professor.turmas.capacidade', $turma->id) }}" method="POST" class="flex gap-1 m-0">
            @csrf @method('PUT')
            <input type="number" name="capacidade" value="{{ $turma->capacidade_producao ?? 10 }}" min="1" max="999" 
                   class="w-full h-6 p-0 border border-black text-[10px] font-black text-center focus:ring-0" title="Ordens simultâneas">
            <button type="submit" class="bg-yellow-500 text-black border border-black px-1.5 h-6 text-[8px] font-black uppercase hover:bg-yellow-600" title="CAPACIDADE PRODUTIVA (Limite de OPs)">
                OK
            </button>
        </form>
    </div>

</div>

                                        {{-- DIVISOR DAS LINHAS --}}
                                        <div class="w-full border-t border-dashed border-gray-400"></div>

                                        {{-- LINHA 2: SETORES OPERACIONAIS --}}
                                        <div class="flex gap-2 flex-wrap justify-center">
                                            @php $btnIcon = "w-9 h-9 rounded bg-white flex items-center justify-center border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none transition cursor-pointer"; @endphp

                                            <a href="{{ route('professor.turmas.perfil', $turma->id) }}" class="flex flex-col items-center group" title="Ver Perfil da Empresa">
                                                <div class="w-9 h-9 rounded bg-indigo-100 flex items-center justify-center border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)] hover:bg-indigo-600 hover:text-white transition">🏢</div>
                                                <span class="text-[9px] text-gray-600 mt-0.5 font-black uppercase">Perfil</span>
                                            </a>
                                            <a href="{{ route('professor.monitoramento.vendas', $turma->id) }}" class="flex flex-col items-center group" title="Vendas">
                                                <div class="{{ $btnIcon }} text-green-600 group-hover:bg-green-600 group-hover:text-white text-sm">💰</div>
                                                <span class="text-[9px] text-gray-600 mt-0.5 font-black uppercase">Vendas</span>
                                            </a>
                                            <a href="{{ route('professor.monitoramento.pcp', $turma->id) }}" class="flex flex-col items-center group" title="PCP- Planejamento e Controle da Produção">
                                                <div class="{{ $btnIcon }} text-blue-600 group-hover:bg-blue-600 group-hover:text-white text-sm">📊</div>
                                                <span class="text-[9px] text-gray-600 mt-0.5 font-black uppercase">PCP</span>
                                            </a>
                                            <a href="{{ route('professor.monitoramento.compras', $turma->id) }}" class="flex flex-col items-center group" title="Compras">
                                                <div class="{{ $btnIcon }} text-purple-600 group-hover:bg-purple-600 group-hover:text-white text-sm">🛒</div>
                                                <span class="text-[9px] text-gray-600 mt-0.5 font-black uppercase">Compras</span>
                                            </a>
                                            <a href="{{ route('professor.monitoramento.almoxarifado', $turma->id) }}" class="flex flex-col items-center group" title="WMS-Armazenagem">
                                                <div class="{{ $btnIcon }} text-orange-600 group-hover:bg-orange-600 group-hover:text-white text-sm">📦</div>
                                                <span class="text-[9px] text-gray-600 mt-0.5 font-black uppercase">WMS</span>
                                            </a>
                                            <a href="{{ route('professor.monitoramento.producao', $turma->id) }}" class="flex flex-col items-center group" title="Produção">
                                                <div class="{{ $btnIcon }} text-yellow-600 group-hover:bg-yellow-500 group-hover:text-white text-sm">🏭</div>
                                                <span class="text-[9px] text-gray-600 mt-0.5 font-black uppercase">Produção</span>
                                            </a>
                                            <a href="{{ route('professor.monitoramento.embalagem', $turma->id) }}" class="flex flex-col items-center group" title="Embalagem">
                                                <div class="{{ $btnIcon }} text-pink-600 group-hover:bg-pink-600 group-hover:text-white text-sm">🎁</div>
                                                <span class="text-[9px] text-gray-600 mt-0.5 font-black uppercase">Pack</span>
                                            </a>
                                            <a href="{{ route('professor.monitoramento.expedicao', $turma->id) }}" class="flex flex-col items-center group" title="Expedição">
                                                <div class="{{ $btnIcon }} text-teal-600 group-hover:bg-teal-600 group-hover:text-white text-sm">🚚</div>
                                                <span class="text-[9px] text-gray-600 mt-0.5 font-black uppercase">Expedição</span>
                                            </a>
                                            <a href="{{ route('professor.monitoramento.bi', $turma->id) }}" class="flex flex-col items-center group" title="BI- Business Intelligence">
                                                <div class="{{ $btnIcon }} bg-gray-800 text-white border-gray-600 group-hover:bg-black text-sm">📈</div>
                                                <span class="text-[9px] text-gray-600 mt-0.5 font-black uppercase">BI</span>
                                            </a>
                                            <a href="{{ route('aluno.gestao_vista', $turma->id) }}" class="flex flex-col items-center group" title="Painel de Gestão à Vista">
                                                <div class="{{ $btnIcon }} text-black hover:bg-yellow-400 text-sm">📺</div>
                                                <span class="text-[9px] text-gray-600 mt-0.5 font-black uppercase">TV</span>
                                            </a>
                                        </div>

                                    </div>

                                    <!-- 3. CONTROLE DE TEMPO (Direita - Compacto) -->
                                    <div class="flex-none flex flex-col items-center justify-center gap-1 border-l-2 border-dashed border-gray-400 pl-3">
                                        <p class="text-[10px] font-black text-black-800 uppercase tracking-widest text-center">Avançar<br>Manualmente<br>Data Game</p>
                                        <div class="grid grid-cols-2 gap-1 w-[80px]">
                                            <form action="{{ route('professor.simulacao.avancar') }}" method="POST">
                                                @csrf <input type="hidden" name="turma_id" value="{{ $turma->id }}"> <input type="hidden" name="minutos" value="1">
                                                <button class="w-full bg-white hover:bg-yellow-100 text-gray-800 border border-black rounded shadow-[1px_1px_0px_0px_black] active:shadow-none active:translate-y-[1px] text-[10px] font-bold py-0.5 transition">+1m</button>
                                            </form>
                                            <form action="{{ route('professor.simulacao.avancar') }}" method="POST">
                                                @csrf <input type="hidden" name="turma_id" value="{{ $turma->id }}"> <input type="hidden" name="minutos" value="30">
                                                <button class="w-full bg-white hover:bg-yellow-100 text-gray-800 border border-black rounded shadow-[1px_1px_0px_0px_black] active:shadow-none active:translate-y-[1px] text-[10px] font-bold py-0.5 transition">+30m</button>
                                            </form>
                                            <form action="{{ route('professor.simulacao.avancar') }}" method="POST">
                                                @csrf <input type="hidden" name="turma_id" value="{{ $turma->id }}"> <input type="hidden" name="horas" value="1">
                                                <button class="w-full bg-yellow-50 hover:bg-yellow-100 text-yellow-900 border border-black rounded shadow-[1px_1px_0px_0px_black] active:shadow-none active:translate-y-[1px] text-[10px] font-bold py-0.5 transition">+1h</button>
                                            </form>
                                            <form action="{{ route('professor.simulacao.avancar') }}" method="POST">
                                                @csrf <input type="hidden" name="turma_id" value="{{ $turma->id }}"> <input type="hidden" name="dias" value="1">
                                                <button class="w-full bg-green-50 hover:bg-green-100 text-green-900 border border-black rounded shadow-[1px_1px_0px_0px_black] active:shadow-none active:translate-y-[1px] text-[10px] font-bold py-0.5 transition">+1D</button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- 4. ADMINISTRAÇÃO (Direita Extrema) -->
                                    <div class="flex-none flex flex-col justify-center gap-1 w-[90px]">
                                        @if(!$turma->jogo_ativo)
                                            <form action="{{ route('professor.simulacao.status') }}" method="POST">
                                                @csrf <input type="hidden" name="turma_id" value="{{ $turma->id }}"> <input type="hidden" name="acao" value="iniciar">
                                                <button class="w-full bg-green-600 hover:bg-green-700 text-white border-2 border-black shadow-[2px_2px_0px_0px_black] active:shadow-none active:translate-y-[1px] text-[10px] font-black py-1.5 rounded transition uppercase">
                                                    ▶ Iniciar
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('professor.simulacao.status') }}" method="POST">
                                                @csrf <input type="hidden" name="turma_id" value="{{ $turma->id }}"> <input type="hidden" name="acao" value="pausar">
                                                <button class="w-full bg-yellow-400 hover:bg-yellow-500 text-black border-2 border-black shadow-[2px_2px_0px_0px_black] active:shadow-none active:translate-y-[1px] text-[10px] font-black py-1.5 rounded transition uppercase" title="Iniciar/ Pausar Game da Empresa">
                                                    ⏸ Pausar
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <a href="{{ route('professor.caos.index', $turma->id) }}" class="w-full bg-purple-600 text-white border-2 border-black shadow-[2px_2px_0px_0px_black] hover:bg-purple-700 active:shadow-none active:translate-y-[1px] text-[10px] font-black py-1 rounded transition text-center block uppercase" title="Painel de Inserção de Caos na Empresa">
                                            ⚡ Caos
                                        </a>

                                        {{-- BOTÃO GESTÃO DE PEDIDOS --}}
                                        <a href="{{ route('professor.pedidos-venda.index', ['filtro_turma' => $turma->id]) }}" class="w-full bg-blue-500 hover:bg-blue-600 text-white border-2 border-black shadow-[2px_2px_0px_0px_black] active:shadow-none active:translate-y-[1px] text-[9px] font-black py-1.5 rounded transition text-center block uppercase mt-1" title="Editar/Excluir Pedidos">
                                            📝 Pedidos
                                        </a>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- OUTROS CARDS (REGRAS E ACESSO RÁPIDO) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Regras -->
                <div class="bg-amber-200 border-2 border-black rounded-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] p-6">
                    <h3 class="font-black text-lg mb-4 text-gray-800 flex items-center gap-2"><span>📜</span> Regras Globais</h3>
                    <form method="POST" action="{{ route('professor.regras.salvar') }}">
                        @csrf @method('PUT')
                        <div class="space-y-3">
                            <label class="flex items-center cursor-pointer hover:bg-gray-50 p-1 rounded">
                                <input type="checkbox" name="aluno_cadastra_cliente" value="1" class="rounded text-indigo-600 border-2 border-gray-400 focus:ring-0 w-5 h-5" @if($configuracoes['aluno_cadastra_cliente']) checked @endif> 
                                <span class="ml-2 text-sm font-bold text-gray-700">Alunos cadastram Clientes</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-50 p-1 rounded">
                                <input type="checkbox" name="aluno_cadastra_fornecedor" value="1" class="rounded text-indigo-600 border-2 border-gray-400 focus:ring-0 w-5 h-5" @if($configuracoes['aluno_cadastra_fornecedor']) checked @endif> 
                                <span class="ml-2 text-sm font-bold text-gray-700">Alunos cadastram Fornecedores</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-50 p-1 rounded">
                                <input type="checkbox" name="aluno_cadastra_materia_prima" value="1" class="rounded text-indigo-600 border-2 border-gray-400 focus:ring-0 w-5 h-5" @if($configuracoes['aluno_cadastra_materia_prima']) checked @endif> 
                                <span class="ml-2 text-sm font-bold text-gray-700">Alunos cadastram Matérias</span>
                            </label>
                            
                            {{-- NOVO CAMPO: EXIGIR INSPEÇÃO MANUAL DE QUALIDADE --}}
                            <label class="flex items-center cursor-pointer hover:bg-gray-50 p-1 rounded">
                                <input type="checkbox" name="exigir_inspecao_qualidade" value="1" class="rounded text-indigo-600 border-2 border-gray-400 focus:ring-0 w-5 h-5" @if($configuracoes['exigir_inspecao_qualidade'] ?? false) checked @endif> 
                                <span class="ml-2 text-sm font-bold text-gray-700">Exigir Inspeção Manual de Qualidade</span>
                            </label>
                        </div>
                        <div class="mt-4 text-right">
                            <button class="bg-gray-800 text-white px-6 py-2 rounded font-bold text-xs border-2 border-black shadow-[2px_2px_0px_0px_gray] active:shadow-none active:translate-y-[1px] transition">SALVAR</button>
                        </div>
                    </form>
                </div>
                <!-- Atalhos -->
                <div class="bg-amber-200 border-2 border-black rounded-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] p-6">
                    <h3 class="font-black text-lg mb-4 text-gray-800 flex items-center gap-2"><span>🚀</span> Acesso Rápido</h3>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <a href="{{ route('professor.turmas.index') }}" class="p-2 bg-indigo-50 text-indigo-700 border border-indigo-200 hover:border-black rounded font-bold block text-center transition">📂 Turmas</a>
                        <a href="{{ route('professor.global.alunos') }}" class="p-2 bg-indigo-50 text-indigo-700 border border-indigo-200 hover:border-black rounded font-bold block text-center transition">🎓 Banco de Talentos</a>
                        <a href="{{ route('professor.materias-primas.index') }}" class="p-2 bg-gray-50 text-gray-600 border border-gray-200 hover:border-black rounded font-bold block text-center transition">📦 Matérias-Primas</a>
                        <a href="{{ route('professor.produtos-acabados.index') }}" class="p-2 bg-gray-50 text-gray-600 border border-gray-200 hover:border-black rounded font-bold block text-center transition">🏭 Produtos Acabados</a>
                        <a href="{{ route('professor.clientes.index') }}" class="p-2 bg-gray-50 text-gray-700 border border-gray-200 hover:border-black rounded font-bold block text-center transition">👥 Clientes</a>
                        <a href="{{ route('professor.fornecedores.index') }}" class="p-2 bg-gray-50 text-gray-700 border border-gray-200 hover:border-black rounded font-bold block text-center transition">🚚 Fornecedores</a>
                        
                        <a href="{{ route('professor.pedidos-venda.index') }}" class="col-span-2 flex justify-center items-center p-3 bg-green-50 text-green-700 border border-green-200 hover:border-green-600 rounded font-black mt-2 transition text-sm">💰 GESTÃO DE PEDIDOS DE VENDA</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>