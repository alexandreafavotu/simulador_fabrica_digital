<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                @if(Auth::user()->acessibilidade_visual)
    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="2"
         style="stroke:#FFFF00!important; filter:none!important;">
        <rect x="3" y="10" width="6" height="10"></rect>
        <rect x="10" y="6" width="6" height="14"></rect>
        <rect x="17" y="12" width="4" height="8"></rect>
    </svg>
@else
    <span class="text-3xl">🏭</span>
@endif
 {{ __('Chão de Fábrica - Produção') }}
            </h2>
            
            {{-- LÓGICA DE NAVEGAÇÃO (MANTIDA) --}}
            @if(Auth::user()->tipo == 'professor')
                @if($modo == 'historico')
                    <a href="{{ route('professor.monitoramento.producao', $aluno->turma_id) }}" 
                       class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                        @if(Auth::user()->acessibilidade_visual)
    <svg class="w-4 h-4 inline" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="4"
         style="filter:none!important; stroke:#FFFF00!important;">
        <path d="M19 12H5M12 19l-7-7 7-7"></path>
    </svg>
@else
    ⬅
@endif
Voltar para a Linha

                    </a>
                @else
                    <a href="{{ route('professor.monitoramento.index', $aluno->turma_id) }}" 
                       class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                        @if(Auth::user()->acessibilidade_visual)
    <!-- Seta Amarela Travada para PCD -->
    <svg class="w-4 h-4 inline" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="4" 
         style="filter: none !important; stroke: #ffff00 !important; background-color: transparent !important;">
        <path d="M19 12H5M12 19l-7-7 7-7"></path>
    </svg>
@else
    ⬅
@endif Sair do Setor
                    </a>
                @endif
            @else
                @php
                    if($modo == 'historico') {
                        $rotaVoltar = route('aluno.producao.dashboard');
                        $textoVoltar = 'Voltar para a Linha';
                    } else {
                        $rotaVoltar = route('dashboard'); 
                        $textoVoltar = 'Voltar ao Menu';
                    }
                @endphp
                <a href="{{ $rotaVoltar }}" class="bg-white text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                    @if(Auth::user()->acessibilidade_visual)
    <svg class="w-4 h-4 inline" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="4"
         style="filter:none!important; stroke:#FFFF00!important;">
        <path d="M19 12H5M12 19l-7-7 7-7"></path>
    </svg>
@else
    ⬅
@endif
{{ $textoVoltar }}

                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12 bg-yellow-50 min-h-screen">
        <div class="max-w-[98%] mx-auto px-4 space-y-8">
            
            @if($modo == 'producao')
                
                {{-- ALERTA DE CAOS (ESTILO INDUSTRIAL) --}}
                @if($aluno->turma->mensagem_plantao_caos)
                    <div class="bg-red-500 text-white p-5 border-4 border-black shadow-[8px_8px_0px_0px_black] flex items-start gap-4 animate-pulse rounded-xl">
                        <span class="text-3xl">📢</span>
                        <div>
                            <h3 class="font-black text-xl uppercase tracking-tighter">Plantão da Fábrica:</h3>
                            <p class="font-bold text-lg leading-tight">{{ $aluno->turma->mensagem_plantao_caos }}</p>
                        </div>
                    </div>
                @endif

                {{-- CONTROLE DE CAPACIDADE (PROFESSOR) --}}
                @if(Auth::user()->tipo == 'professor')
                    <div class="bg-yellow-100 border-4 border-black p-4 rounded-xl shadow-[6px_6px_0px_0px_rgba(0,0,0,0.2)] flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="bg-yellow-400 p-3 rounded border-2 border-black text-black">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
                            </div>
                            <div>
                                <h3 class="font-black text-yellow-900 uppercase">Capacidade Produtiva</h3>
                                <p class="text-xs font-bold text-yellow-800">Limite de ordens simultâneas por aluno.</p>
                            </div>
                        </div>
                        <form action="{{ route('professor.turmas.capacidade', $aluno->turma_id) }}" method="POST" class="flex items-center gap-2">
                            @csrf @method('PUT')
                            <input type="number" name="capacidade" value="{{ $aluno->turma->capacidade_producao ?? 3 }}" min="1" max="20" class="w-20 text-center font-black border-2 border-black rounded shadow-[2px_2px_0px_0px_black] focus:ring-0">
                            <button type="submit" class="bg-black hover:bg-gray-800 text-white text-xs font-black py-2 px-4 rounded border-2 border-transparent shadow-lg uppercase transition transform active:scale-95">SALVAR</button>
                        </form>
                    </div>
                @endif

                {{-- 1. ORDENS DISPONÍVEIS (POOL) --}}
                <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,0.1)] overflow-hidden">
                    <div class="bg-gray-800 text-white p-4 border-b-4 border-black flex items-center justify-between">
                        <h3 class="text-xl font-black uppercase tracking-tighter flex items-center gap-2">
                            @if(Auth::user()->acessibilidade_visual)
    <svg class="w-5 h-5 inline" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="2"
         style="stroke:#FFFF00!important; fill:none!important; filter:none!important;">
        <rect x="6" y="4" width="12" height="16" rx="2"></rect>
        <line x1="9" y1="8" x2="15" y2="8"></line>
        <line x1="9" y1="12" x2="15" y2="12"></line>
        <line x1="9" y1="16" x2="15" y2="16"></line>
    </svg>
@else
    <span>📋</span>
@endif

 1. Ordens Disponíveis (Backlog)
                        </h3>
                        <span class="bg-white text-black px-3 py-1 rounded border-2 border-black text-sm font-black shadow-[2px_2px_0px_0px_gray]">{{ $ordensDisponiveis->count() }}</span>
                    </div>

                    <div class="p-6 bg-gray-100">
                        @if($ordensDisponiveis->isEmpty())
                            <div class="p-8 text-center text-gray-400 border-4 border-dashed border-gray-300 rounded-xl">
                                <p class="font-black text-xl uppercase">Linha Limpa</p>
                                <p class="text-sm font-bold">Nenhuma ordem aguardando produção.</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                @foreach($ordensDisponiveis as $op)
                                    @php
                                        $temEstoque = true;
                                        foreach($op->produto->materiasPrimas as $mp) {
                                            $necessario = $op->quantidade * $mp->pivot->quantidade;
                                            if($mp->quantidade_estoque < $necessario) { $temEstoque = false; break; }
                                        }
                                    @endphp
                                    {{-- CARD OP --}}
                                    <div class="bg-white p-4 rounded-lg border-2 border-black shadow-[4px_4px_0px_0px_black] relative group transition-all hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_black] {{ $temEstoque ? '' : 'opacity-70 bg-gray-50' }}">
                                        
                                        <div class="flex justify-between items-start mb-2 border-b-2 border-gray-100 pb-2">
                                            <span class="text-[10px] font-black text-white bg-black px-2 py-0.5 rounded uppercase">OP #{{ $op->id }}</span>
                                            @if(!$temEstoque) 
                                                <span class="text-[9px] font-black text-white bg-red-600 px-2 py-0.5 rounded uppercase border border-black">Sem Material</span> 
                                            @else
                                                <span class="text-[9px] font-black text-green-800 bg-green-200 px-2 py-0.5 rounded uppercase border border-green-400">Pronto</span> 
                                            @endif
                                        </div>

                                        <h4 class="font-black text-gray-800 leading-tight mb-1 text-base uppercase">{{ $op->produto->nome }}</h4>
                                        
                                        <div class="bg-gray-50 p-2 rounded border border-gray-200 mb-3">
                                            <p class="text-xs text-gray-500 truncate"><strong>Cliente:</strong> {{ $op->pedido->cliente->nome_razao_social ?? 'N/A' }}</p>
                                            <p class="text-sm font-bold text-gray-800">Qtd: {{ $op->quantidade }} un</p>
                                        </div>
                                        
                                        @if(Auth::user()->tipo == 'aluno')
                                            @if($temEstoque)
                                                <form action="{{ route('aluno.producao.assumir', $op->id) }}" method="POST">
                                                    @csrf 
                                                    <button class="w-full bg-blue-600 text-white px-3 py-2 rounded border-2 border-black shadow-[2px_2px_0px_0px_black] active:shadow-none active:translate-y-[1px] text-xs font-black uppercase transition hover:bg-blue-700">
                                                        PEGAR ORDEM
                                                    </button>
                                                </form>
                                            @else
                                                <button disabled class="w-full bg-gray-200 text-gray-400 border-2 border-gray-300 px-3 py-2 rounded text-xs font-black uppercase cursor-not-allowed">
                                                    Bloqueado
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- 2. LINHAS ATIVAS --}}
                <div class="mt-8">
                    <div class="flex items-center justify-between mb-4 bg-indigo-600 text-white p-4 rounded-xl border-4 border-black shadow-[6px_6px_0px_0px_black]">
                        <h3 class="text-xl font-black flex items-center uppercase tracking-tighter gap-2">
                            @if(Auth::user()->acessibilidade_visual)
    <!-- Ícone fábrica amarelo -->
    <svg class="w-7 h-7 inline" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="2"
         style="stroke:#FFFF00!important; filter:none!important;">
        <rect x="3" y="10" width="6" height="10"></rect>
        <rect x="10" y="6" width="6" height="14"></rect>
        <rect x="17" y="12" width="4" height="8"></rect>
    </svg>
@else
    <span class="text-2xl">🏭</span>
@endif
 2. Linhas de Produção Ativas
                        </h3>
                        
                        @if(Auth::user()->tipo == 'aluno')
                            <div class="flex items-center gap-2 bg-indigo-800 px-3 py-1 rounded border border-indigo-400">
                                <span class="text-[10px] text-indigo-200 uppercase font-bold tracking-widest">Sua Carga:</span>
                                <span class="text-white text-sm font-black">
                                    @php $minhasAtivas = $minhasOrdens->where('aluno_id', $aluno->id ?? 0)->where('status', '!=', 'Concluída')->count(); @endphp
                                    {{ $minhasAtivas }} / {{ $aluno->turma->capacidade_producao ?? 3 }}
                                </span>
                            </div>
                        @endif
                    </div>

                    @if($minhasOrdens->isEmpty())
                        <div class="p-16 text-center text-indigo-300 border-4 border-dashed border-indigo-200 rounded-xl bg-indigo-50">
                            <p class="font-black text-2xl uppercase">Fábrica Parada</p>
                            <p class="font-bold text-sm">Nenhuma máquina operando no momento.</p>
                        </div>
                    @else
                        @php $grupos = $minhasOrdens->groupBy('aluno_id'); @endphp

                        <div class="space-y-8">
                            @foreach($grupos as $alunoId => $ordensDoAluno)
                                @php 
                                    $operador = $ordensDoAluno->first()->aluno;
                                    $eMeu = (Auth::user()->tipo == 'aluno' && $alunoId == $aluno->id);
                                @endphp

                                <div class="bg-white border-4 border-black rounded-xl overflow-hidden shadow-[8px_8px_0px_0px_rgba(0,0,0,0.1)]">
                                    {{-- Cabeçalho do Operador --}}
                                    <div class="bg-gray-100 p-3 border-b-4 border-black flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded bg-indigo-600 text-white flex items-center justify-center font-black border-2 border-black shadow-sm">
                                                {{ substr($operador->nome ?? '?', 0, 1) }}
                                            </div>
                                            <div>
                                                <h4 class="font-black text-gray-800 text-sm uppercase tracking-wide leading-none">
                                                    LINHA DE: {{ $operador->nome ?? 'Desconhecido' }}
                                                </h4>
                                                @if($eMeu) <span class="inline-block mt-1 bg-green-500 text-white text-[9px] font-black px-2 py-0.5 rounded border border-black uppercase">SUA LINHA</span> @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 bg-yellow-50">
                                        @foreach($ordensDoAluno as $op)
                                            
                                            {{-- LÓGICA DO CAOS: MÁQUINA QUEBRADA --}}
                                            @if($op->em_manutencao)
                                                <div class="bg-red-600 p-4 rounded-lg shadow-[6px_6px_0px_0px_black] border-4 border-black relative overflow-hidden flex flex-col justify-between animate-pulse text-white">
                                                    <div class="text-center z-10">
                                                        <span class="text-5xl">🛠️</span>
                                                        <h4 class="font-black text-xl uppercase leading-none mt-2">FALHA TÉCNICA</h4>
                                                        <div class="mt-2 bg-black/30 p-2 rounded border border-white/50">
                                                            <p class="text-xs font-bold uppercase">{{ $op->motivo_manutencao ?? 'Quebra Geral' }}</p>
                                                        </div>
                                                        @if($op->previsao_conserto_ate)
                                                            <div class="mt-2 bg-white text-red-600 px-2 py-1 rounded font-black text-[10px] uppercase">
                                                                Retorno: {{ \Carbon\Carbon::parse($op->previsao_conserto_ate)->format('d/m/Y') }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    
                                                    {{-- Faixas de Perigo (CSS Striped) --}}
                                                    <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/caution-stripe.png')]"></div>
                                                </div>
                                            @else
                                                
                                                {{-- CARD DE MÁQUINA (RODANDO) --}}
                                                <div class="bg-white p-4 rounded-lg border-4 border-black shadow-[6px_6px_0px_0px_black] relative flex flex-col justify-between 
                                                    {{ $op->status == 'Em Produção' ? 'ring-4 ring-green-400 ring-opacity-50' : '' }}">
                                                    
                                                    <div class="mb-3">
                                                        <div class="flex justify-between items-start mb-2">
                                                            <span class="text-[9px] font-black uppercase px-2 py-1 rounded border-2 border-black {{ $op->status == 'Em Produção' ? 'bg-green-400 text-black' : 'bg-yellow-300 text-black' }}">
                                                                {{ $op->status }}
                                                            </span>
                                                            <span class="text-xs font-mono font-bold text-gray-400">#{{ $op->id }}</span>
                                                        </div>
                                                        <h4 class="font-black text-gray-800 leading-tight uppercase text-sm mb-1">{{ $op->produto->nome }}</h4>
                                                        <p class="text-xs font-bold text-gray-500">Qtd: {{ $op->quantidade }} un</p>
                                                    </div>

                                                    <div class="pt-3 border-t-2 border-dashed border-gray-300">
                                                        @if($eMeu)
                                                            @if($op->status == 'Aberta')
                                                                @if($op->status_material == 'Pendente')
                                                                    <form action="{{ route('aluno.producao.solicitar', $op->id) }}" method="POST"> 
                                                                        @csrf 
                                                                        <button class="w-full bg-blue-600 text-white px-2 py-2 rounded border-2 border-black shadow-[2px_2px_0px_0px_black] active:shadow-none active:translate-y-[1px] text-[10px] font-black uppercase hover:bg-blue-700 transition">
                                                                            Solicitar Material
                                                                        </button> 
                                                                    </form>
                                                                @elseif($op->status_material == 'Solicitado')
                                                                    <div class="text-center bg-orange-100 border-2 border-orange-300 p-1 rounded">
                                                                        <p class="text-[9px] font-black text-orange-700 uppercase animate-pulse"><p class="text-[9px] font-black text-orange-700 uppercase animate-pulse">
    @if(Auth::user()->acessibilidade_visual)
        <svg class="w-3 h-3 inline" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="2"
             style="stroke:#FFFF00!important; filter:none!important;">
            <path d="M6 2h12v4l-4 4 4 4v4H6v-4l4-4-4-4V2z"></path>
        </svg>
    @else
        ⏳
    @endif
    Aguardando WMS
</p>
                                                                    </div>
                                                                @elseif($op->status_material == 'Entregue')
                                                                    <form action="{{ route('aluno.producao.iniciar', $op->id) }}" method="POST"> 
                                                                        @csrf 
                                                                        <button class="w-full bg-green-500 text-white px-2 py-2 rounded border-2 border-black shadow-[2px_2px_0px_0px_black] active:shadow-none active:translate-y-[1px] text-[10px] font-black uppercase hover:bg-green-600 transition animate-bounce">
                                                                            ⚡ Ligar Máquina
                                                                        </button> 
                                                                    </form>
                                                                @endif
                                                            @elseif($op->status == 'Em Produção')
                                                                @php
                                                                    $inicio = \Carbon\Carbon::parse($op->data_inicio_real); $agora = \Carbon\Carbon::parse($dataJogo);
                                                                    $dias = max(0, $inicio->diffInDays($agora)); $total = $op->produto->tempo_producao_dias;
                                                                    $porc = min(100, ($total > 0) ? ($dias / $total) * 100 : 100);
                                                                    $podeFinalizar = $porc >= 100;
                                                                @endphp
                                                                
                                                                {{-- Barra de Progresso Industrial --}}
                                                                <div class="w-full bg-gray-300 rounded-full h-4 mb-2 overflow-hidden border-2 border-black relative">
                                                                    <div class="h-full {{ $podeFinalizar ? 'bg-green-500' : 'bg-yellow-400 stripe-animation' }}" style="width: {{ $porc }}%"></div>
                                                                    <span class="absolute inset-0 flex items-center justify-center text-[8px] font-black text-black">{{ round($porc) }}%</span>
                                                                </div>

                                                                @if($podeFinalizar) 
                                                                    <a href="{{ route('aluno.producao.apontar', $op->id) }}" class="block w-full bg-indigo-600 text-white text-center px-2 py-2 rounded border-2 border-black shadow-[2px_2px_0px_0px_black] active:shadow-none active:translate-y-[1px] font-black uppercase text-[10px] hover:bg-indigo-700 transition">
                                                                        ⏹ FINALIZAR
                                                                    </a>
                                                                @else 
                                                                    <div class="text-center text-[9px] text-green-700 font-black uppercase bg-green-100 border border-green-300 rounded p-1">⚙️ Trabalhando...</div> 
                                                                @endif
                                                            @endif
                                                        @else
                                                            {{-- VISÃO PROFESSOR OU OUTROS --}}
                                                            @if($op->status == 'Em Produção')
                                                                @php $porc = rand(10, 90); // Simulação visual se não for meu @endphp
                                                                <div class="w-full bg-gray-200 rounded-full h-3 border border-black overflow-hidden"><div class="h-full bg-green-500" style="width: 50%"></div></div>
                                                                <div class="text-center text-[9px] font-bold text-gray-500 mt-1">EM OPERAÇÃO</div>
                                                            @else
                                                                <div class="text-center text-[9px] font-bold text-gray-400 bg-gray-100 border border-gray-200 rounded py-1">AGUARDANDO</div>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- CARD DO HISTÓRICO (LINK GRANDE) --}}
                @php $urlHistorico = Auth::user()->tipo == 'professor' ? route('professor.monitoramento.producao', $aluno->turma_id) . '?tela=historico' : route('aluno.producao.dashboard', ['tela' => 'historico']); @endphp
                <a href="{{ $urlHistorico }}" class="group block bg-white border-4 border-black shadow-[8px_8px_0px_0px_rgba(239,68,68,1)] rounded-xl hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition duration-200 mt-12 overflow-hidden">
                    <div class="p-6 flex items-center justify-between bg-white group-hover:bg-red-50 transition">
                        <div class="flex items-center gap-6">
                            <div class="bg-red-100 p-4 rounded-full border-2 border-black text-red-600 text-3xl flex items-center justify-center">
    @if(Auth::user()->acessibilidade_visual)
        <!-- Ícone amarelo de gráfico para PCD -->
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="3"
             style="stroke:#FFFF00!important; filter:none!important;">
            <polyline points="3 17 9 11 13 15 21 7"></polyline>
            <circle cx="21" cy="7" r="1.5"></circle>
        </svg>
    @else
        📉
    @endif
</div>

                            <div>
                                <h3 class="text-2xl font-black text-gray-800 uppercase tracking-tight">Qualidade & Refugo</h3>
                                <p class="text-gray-500 text-sm font-bold uppercase">Acessar histórico de apontamentos e perdas.</p>
                            </div>
                        </div>
                        <span class="bg-red-600 text-white font-black text-xs uppercase px-6 py-3 rounded border-2 border-black shadow-[3px_3px_0px_0px_black] group-hover:shadow-none group-hover:translate-x-1 group-hover:translate-y-1 transition">
                            Acessar Relatório
                        </span>
                    </div>
                </a>
            @endif

            {{-- ======================================================= --}}
            {{-- MODO 3: HISTÓRICO (TABELA)                              --}}
            {{-- ======================================================= --}}
            @if($modo == 'historico')
                <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_black] overflow-hidden">
                    <div class="bg-red-600 text-white p-4 border-b-4 border-black flex flex-col md:flex-row justify-between items-center gap-4">
                        <h3 class="font-black text-xl uppercase tracking-tighter">Histórico de Produção</h3>
                        
                        {{-- FILTRO --}}
                        <form method="GET" action="{{ request()->url() }}" class="flex flex-wrap gap-2 items-center justify-end">
                            <input type="hidden" name="tela" value="historico">
                            <input type="date" name="filtro_data" value="{{ request('filtro_data') }}" class="text-[10px] font-black border-2 border-black rounded p-1 text-black focus:ring-0">
                            <button class="bg-black text-white text-[10px] px-3 py-1.5 rounded font-black uppercase hover:bg-gray-800 border-2 border-white">Filtrar</button>
                            
                            @if(request()->anyFilled(['filtro_data', 'filtro_produto']))
                                @php $urlLimpar = Auth::user()->tipo == 'professor' ? route('professor.monitoramento.producao', $aluno->turma_id) . '?tela=historico' : route('aluno.producao.dashboard', ['tela' => 'historico']); @endphp
                                <a href="{{ $urlLimpar }}" class="bg-white text-red-600 border-2 border-black px-2 py-0.5 rounded text-[10px] font-black uppercase">X</a>
                            @endif
                        </form>
                    </div>

                    <div class="p-0 overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-100 text-gray-700 uppercase text-[10px] font-black border-b-2 border-black">
                                <tr>
                                    <th class="px-6 py-4">Data/Hora</th>
                                    <th class="px-6 py-4">OP</th>
                                    <th class="px-6 py-4">Produto</th>
                                    <th class="px-6 py-4 text-center">Operador</th>
                                    <th class="px-6 py-4 text-center bg-green-100 text-green-900 border-x-2 border-black">Produzido</th>
                                    <th class="px-6 py-4 text-center bg-red-100 text-red-900">Refugo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y-2 divide-gray-100">
                                @forelse($historico as $reg)
                                    <tr class="hover:bg-yellow-50 transition">
                                        <td class="px-6 py-4 font-mono text-xs font-bold text-gray-500">
                                            {{ \Carbon\Carbon::parse($reg->data_apontamento)->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 font-black text-gray-400">#{{ $reg->ordem_producao_id }}</td>
                                        <td class="px-6 py-4 font-black text-gray-800 uppercase">{{ $reg->ordem->produto->nome }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-[10px] font-bold border border-gray-300 uppercase">
                                                {{ explode(' ', $reg->aluno->nome)[0] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center font-black text-green-600 bg-green-50/50 text-lg border-x border-gray-200">
                                            {{ $reg->quantidade_produzida }}
                                        </td>
                                        <td class="px-6 py-4 text-center font-black text-red-600 bg-red-50/50 text-lg">
                                            @if($reg->quantidade_refugo > 0) 
                                                {{ $reg->quantidade_refugo }} <span class="text-xs">⚠️</span>
                                            @else 
                                                <span class="text-gray-300 opacity-50">-</span> 
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="p-12 text-center text-gray-400 font-bold italic uppercase">Nenhum registro de produção.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- Animação da Barra de Progresso --}}
    <style>
        .stripe-animation {
            background-image: linear-gradient(45deg, rgba(255,255,255,.15) 25%, transparent 25%, transparent 50%, rgba(255,255,255,.15) 50%, rgba(255,255,255,.15) 75%, transparent 75%, transparent);
            background-size: 1rem 1rem;
            animation: progress-bar-stripes 1s linear infinite;
        }
        @keyframes progress-bar-stripes {
            from { background-position: 1rem 0; }
            to { background-position: 0 0; }
        }
    </style>
</x-app-layout>