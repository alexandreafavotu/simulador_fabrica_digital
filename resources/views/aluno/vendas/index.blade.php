<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                @if(Auth::user()->acessibilidade_visual)
    <svg class="w-8 h-8 inline" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="2" style="filter: none !important; stroke: #ffff00 !important;">
        <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.407 2.67 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.407-2.67-1M12 16v1m-6-5h12"></path>
    </svg>
@else
    <span class="text-3xl">💰</span>
@endif {{ __('Módulo Comercial / Vendas') }} 
            </h2>
            
            @if(Auth::user()->tipo == 'professor')
                {{-- Botão Voltar Industrial para Professor --}}
                <a href="{{ route('professor.monitoramento.index', $aluno->turma_id) }}" 
                   class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                    ⬅ Sair do Setor
                </a>
            @elseif(isset($modo) && $modo != 'menu')
                {{-- Botão Voltar Industrial para Aluno --}}
                <a href="{{ url()->current() }}" 
                   class="bg-white text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                    @if(Auth::user()->acessibilidade_visual)
    <svg class="w-4 h-4 inline" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="4" style="filter: none !important; stroke: #ffff00 !important; background-color: transparent !important;">
        <path d="M19 12H5M12 19l-7-7 7-7"></path>
    </svg>
@else
    ⬅
@endif Menu Vendas
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12 bg-yellow-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- 1. ALERTAS DE CAOS (ESTILO INDUSTRIAL) --}}
            @if($aluno->turma->mensagem_plantao_caos)
                <div class="bg-red-500 text-white p-5 border-4 border-black shadow-[8px_8px_0px_0px_black] flex items-start gap-4 animate-pulse">
                    <span class="text-3xl">📢</span>
                    <div>
                        <h3 class="font-black text-xl uppercase tracking-tighter">Plantão Urgente:</h3>
                        <p class="font-bold text-lg leading-tight">{{ $aluno->turma->mensagem_plantao_caos }}</p>
                    </div>
                </div>
            @endif

            {{-- Alertas de Sucesso/Erro --}}
@if(session('success'))
    <div class="bg-green-500 text-white p-4 border-4 border-black mb-4 font-black uppercase">
        ✅ {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-500 text-white p-4 border-4 border-black mb-4 font-black uppercase">
        ❌ {{ session('error') }}
    </div>
@endif



            @php
                $bloqueioSistema = false;
                if(isset($aluno->turma->bloqueio_faturamento_ate)) {
                    $dataJogo = \Carbon\Carbon::parse($aluno->turma->data_jogo);
                    $dataFimSistema = \Carbon\Carbon::parse($aluno->turma->bloqueio_faturamento_ate);
                    if($dataJogo->lt($dataFimSistema)) $bloqueioSistema = true;
                }
            @endphp

            {{-- 2. PAINEL DE CONTROLE DE LIMITE (EXCLUSIVO PROFESSOR) --}}
            @if(Auth::user()->tipo == 'professor')
                <div class="bg-white border-4 border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,0.2)] p-6 mb-8 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div>
                        <h3 class="font-black text-lg text-gray-800 uppercase flex items-center gap-2">
                            <span>🎛️</span> Controle de Mercado (Regra de Negócio)
                        </h3>
                        <p class="text-xs font-bold text-gray-500 max-w-md">
                            Defina o limite máximo de pedidos que cada aluno pode abrir por dia simulado.
                        </p>
                    </div>

                    <form action="{{ route('professor.turmas.limite_vendas', $aluno->turma_id) }}" method="POST" class="flex items-end gap-2 bg-gray-100 p-3 rounded border-2 border-black">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Limite Diário / Aluno</label>
                            <input type="number" name="limite_vendas_por_aluno" 
                                   value="{{ $aluno->turma->limite_vendas_por_aluno ?? 10 }}" 
                                   min="1" max="500" 
                                   class="w-32 border-2 border-black text-center font-black text-lg rounded focus:ring-0 focus:border-indigo-600">
                        </div>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2.5 rounded border-2 border-black shadow-[2px_2px_0px_0px_black] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none transition font-black uppercase text-xs">
                            Definir Regra
                        </button>
                    </form>
                </div>
            @endif

            @if($bloqueioSistema)
                <div class="bg-black text-white p-6 border-4 border-red-600 shadow-[8px_8px_0px_0px_rgba(220,38,38,0.5)] flex items-center gap-6">
                    <div class="bg-red-600 p-4 rounded border-2 border-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black uppercase tracking-widest text-red-500">Terminal de Faturamento Offline</h3>
                        <p class="font-bold text-gray-300">Servidores de TI em manutenção. Operações de saída suspensas.</p>
                        <p class="text-sm mt-1 text-yellow-400 font-mono">Previsão de Retorno: {{ $dataFimSistema->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            @endif
            

            {{-- ======================================================= --}}
            {{-- MODO 1: MENU INICIAL (LAUNCHER CARDS 3D)                --}}
            {{-- ======================================================= --}}
            @if($modo == 'menu')
                <div class="mb-8 text-center">
                    <h3 class="text-3xl font-black text-gray-800 uppercase tracking-tighter">Console de Operações Comerciais</h3>
                    <p class="font-bold text-indigo-600 uppercase text-xs tracking-widest">Olá, Comprador {{ explode(' ', $aluno->nome)[0] }}! Selecione o procedimento de trabalho</p>
                </div>

                @php
                    $rotaBase = Auth::user()->tipo == 'professor' ? 'professor.monitoramento.vendas' : 'aluno.vendas.index';
                    $params = Auth::user()->tipo == 'professor' ? ['turma_id' => $aluno->turma_id] : [];
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 max-w-5xl mx-auto">
                    {{-- CARD 1: NOVO PEDIDO --}}
                    <a href="{{ route($rotaBase, array_merge($params, ['tela' => 'novo'])) }}" 
                       class="group bg-white border-4 border-black p-8 shadow-[10px_10px_0px_0px_rgba(34,197,94,1)] hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all duration-200">
                        <div class="flex flex-col items-center text-center">
                            <div class="flex items-center gap-3 border-2 border-black p-5 rounded-full mb-4 transition {{ Auth::user()->acessibilidade_visual ? 'bg-black border-yellow-400' : 'bg-green-100 group-hover:bg-green-400' }}">
    @if(Auth::user()->acessibilidade_visual)
        <svg class="w-12 h-12" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="4" style="filter:none!important; stroke:#ffff00 !important;">
            <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"></path>
            <line x1="7" y1="7" x2="7.01" y2="7"></line>
    @else
        <span class="text-5xl">🏷️</span>
    @endif
    

    
</div>


                            <h4 class="text-2xl font-black text-gray-800 uppercase leading-none">Abrir Venda</h4>
                            <p class="text-sm font-bold text-gray-500 mt-2">Registrar entrada de novo pedido de cliente.</p>
                        </div>
                    </a>

                    {{-- CARD 2: HISTÓRICO --}}
                    <a href="{{ route($rotaBase, array_merge($params, ['tela' => 'historico'])) }}" 
                       class="group bg-white border-4 border-black p-8 shadow-[10px_10px_0px_0px_rgba(59,130,246,1)] hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all duration-200">
                        <div class="flex flex-col items-center text-center">
                            <div class="border-2 border-black p-5 rounded-full mb-4 transition {{ Auth::user()->acessibilidade_visual ? 'bg-black border-yellow-400' : 'bg-blue-100 group-hover:bg-blue-400' }}">
    @if(Auth::user()->acessibilidade_visual)
        <!-- Prancheta Amarela para PCD -->
        <svg class="w-12 h-12" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="2" style="filter:none!important; stroke:#ffff00 !important;">
            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
    @else
        <span class="text-5xl">📋</span>
    @endif
</div>
                            <h4 class="text-2xl font-black text-gray-800 uppercase leading-none">Gerenciar Carteira</h4>
                            <p class="text-sm font-bold text-gray-500 mt-2">Consultar status e histórico de pedidos ativos.</p>
                            <div class="flex gap-2 mt-4">
                                <span class="bg-black text-white text-[10px] font-black px-2 py-1 rounded uppercase">{{ $dadosStats['total_pedidos'] }} Pedidos</span>
                                <span class="bg-yellow-400 text-black border border-black text-[10px] font-black px-2 py-1 rounded uppercase">{{ $dadosStats['pedidos_abertos'] }} Pendentes</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

           {{-- ======================================================= --}}
            {{-- MODO 2: FORMULÁRIO DE NOVO PEDIDO (COM MODAL DE CONFIRMAÇÃO) --}}
            {{-- ======================================================= --}}
            @if($modo == 'novo')
<div class="mb-8">
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            @if(Auth::user()->acessibilidade_visual)
    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="2" style="filter: none !important; stroke: #ffff00 !important;">
        <path d="M22 6s-4.47 0-7 2c-2.53 2-4 3-4 3l-2 2H3c-1.1 0-2 .9-2 2v5c0 1.1.9 2 2 2h15c1.1 0 2-.9 2-2V9s1.35-1.35 3-3V2h-2v4z"></path>
    </svg>
@else
    <span class="text-2xl">📬</span>
@endif
            <h3 class="font-black text-xl uppercase tracking-tighter text-gray-800">Mural de Solicitações do Mercado</h3>
            

           {{-- 2. O PICTOGRAMA (Só aparece se o botão de Pictogramas estiver ON) --}}
    @if(Auth::user()?->acessibilidade_pictogramas)
        <img src="{{ asset('img/vendas/analise_de_demanda.png') }}" 
             class="w-20 h-20 object-contain {{ Auth::user()->acessibilidade_visual ? 'img-pcd-yellow' : '' }}">
    @endif 
        </div>
        @if(isset($dadosStats['limite_info']))
    <div class="text-[10px] font-black bg-white border-2 border-black px-2 py-1 uppercase">
        Cota Disponível: {{ ($dadosStats['limite_info']['total'] - $dadosStats['limite_info']['usado']) }} pedidos
    </div>
@else
    <div class="text-[10px] font-black bg-yellow-300 border-2 border-black px-2 py-1 uppercase">
        👁️ Visão do Professor
    </div>
@endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($demandasMercado as $demanda)
            @php
                $meu = $demanda->aluno_id == $aluno->id;
                $deOutro = $demanda->aluno_id && $demanda->aluno_id != $aluno->id;
                $disponivel = !$demanda->aluno_id;
                $cotaCheia = $dadosStats['limite_info']['bloqueado'] ?? false;
            @endphp

            {{-- Só mostramos os que estão disponíveis OU os que são DESTE aluno --}}
            @if(!$deOutro)
                <div class="border-4 border-black p-4 relative {{ $meu ? 'bg-green-50 shadow-[6px_6px_0px_0px_rgba(34,197,94,1)]' : 'bg-white shadow-[6px_6px_0px_0px_black]' }}">
                    
                    {{-- Status visual --}}
                    <div class="absolute top-0 right-0 px-2 py-1 border-l-4 border-b-4 border-black font-black text-[8px] uppercase {{ $meu ? 'bg-green-500 text-white' : 'bg-yellow-400' }}">
                        @if(Auth::user()->acessibilidade_visual)
    @if($meu)
        <svg class="w-3 h-3 inline mr-1" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="3" style="filter: none !important; stroke: #ffff00 !important;"><path d="M20 6L9 17l-5-5"></path></svg> Seu Documento
    @else
        <svg class="w-3 h-3 inline mr-1" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="3" style="filter: none !important; stroke: #ffff00 !important;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg> Mercado
    @endif
@else
    {{ $meu ? '✍️ Seu Documento' : '🆕 Mercado' }}
@endif
                    </div>

                    <div class="mb-2">
                        <label class="block text-[8px] font-black text-gray-400 uppercase">Cliente:</label>
                        <p class="font-black text-xs uppercase">{{ $demanda->cliente->nome_razao_social }}</p>
                    </div>

                    {{-- Se for meu, eu vejo os detalhes para digitar --}}
                    @if($meu)
                        <div class="bg-white border-2 border-dashed border-black p-2 mt-2">
                            <label class="block text-xs font-black text-red-500 mb-1 italic">DADOS PARA DIGITAÇÃO:</label>
                            <p class="text-sm font-bold">
    @if(Auth::user()->acessibilidade_visual)
        <svg class="w-4 h-4 inline mr-1" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="2" style="filter: none !important; stroke: #ffff00 !important;">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
        </svg>
    @else
        📅 
    @endif
    Entrega: {{ \Carbon\Carbon::parse($demanda->data_entrega_solicitada)->format('d/m/Y') }}
</p>
<ul class="mt-1">
                                @foreach($demanda->itens as $it)
                                    <li class="text-xs font-black uppercase text-indigo-700">• {{ $it->produto->nome }} ({{ $it->quantidade }} un)</li>
                                @endforeach
                            </ul>
                        </div>
                    @else


                   {{-- Bloco de datas com letras maiores e data do simulador --}}
<div class="mt-3 mb-4 p-2 border-2 border-dashed border-gray-300 bg-gray-50 rounded">
    <div class="flex justify-between items-center mb-1">
        <span class="text-[10px] font-black uppercase text-gray-500">
    @if(Auth::user()->acessibilidade_visual)
        <svg class="w-3.5 h-3.5 inline mr-1" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="2.5" style="filter: none !important; stroke: #ffff00 !important;">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
        </svg>
    @else
        📅 
    @endif
    Recebido em:
</span>
        <span class="text-xs font-black text-black">
            {{-- Aqui usamos a nova data do jogo --}}
            {{ \Carbon\Carbon::parse($demanda->data_jogo_emissao)->format('d/m/Y') }}
        </span>
    </div>
    <div class="flex justify-between items-center">
        <span class="text-[10px] font-black uppercase text-gray-500">
    @if(Auth::user()->acessibilidade_visual)
        <svg class="w-3.5 h-3.5 inline mr-1" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="3" style="filter: none !important; stroke: #ffff00 !important;">
            <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path>
            <line x1="12" y1="9" x2="12" y2="13"></line>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
        </svg>
    @else
        🚨 
    @endif
    Entrega para:
</span>
        <span class="text-sm font-black text-red-600 underline">
            {{ \Carbon\Carbon::parse($demanda->data_entrega_solicitada)->format('d/m/Y') }}
        </span>
    </div>
</div>




                        {{-- Se não for meu, eu preciso "Assumir" para ver os detalhes --}}
                        <div class="mt-4">
                            @if($cotaCheia)
                                <div class="bg-gray-100 p-2 text-center border-2 border-gray-300 text-[9px] font-black text-gray-400 uppercase">
                                    🔒 Pedido em Análise (Cota de Vendas Atingida)
                                </div>
                            @else

                                <form action="{{ route('aluno.vendas.assumir', $demanda->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-black text-white text-[10px] font-black py-2 uppercase hover:bg-indigo-600 transition">
                                        Assumir este Pedido
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        @endforeach
    </div>
</div>

            
                    <div class="bg-white border-4 border-black shadow-[8px_8px_0px_0px_black] rounded-xl overflow-hidden" 
     x-data="{ 
        showConfirm: false, 
        loading: false,
        clienteNome: '',
        dataEntrega: '',
        itens: [{ produto_id: '', quantidade: 1 }],
        
        // FUNÇÃO PARA SOMAR TODAS AS QUANTIDADES
        totalPecas() {
            return this.itens.reduce((sum, item) => sum + parseInt(item.quantidade || 0), 0);
        },

        abrirConfirmacao() {
            const select = document.getElementById('cliente_id');
            this.clienteNome = select.options[select.selectedIndex].text;
            
            // Formata a data para ficar bonita no modal (Brasil)
            const dataInput = document.getElementById('data_entrega').value;
            if(dataInput) {
                const [ano, mes, dia] = dataInput.split('-');
                this.dataEntrega = `${dia}/${mes}/${ano}`;
            }

            if(!this.clienteNome || this.clienteNome.includes('--') || !dataInput) {
                alert('⚠️ Preencha o Cliente e a Data primeiro!');
                return;
            }
            this.showConfirm = true;
        }
     }">
                    
                    <div class="bg-green-500 text-white p-4 border-b-4 border-black flex items-center gap-2">
    @if(Auth::user()->acessibilidade_visual)
        <!-- Ícone em código Amarelo para PCD -->
        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="2" style="filter:none!important; stroke:#ffff00 !important;">
            <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
        </svg>
    @else
        <!-- Seu emoji original -->
        <span class="text-2xl">📝</span>
    @endif
    <h3 class="font-black text-lg uppercase">Registro de Ordem de Venda</h3>
</div>

                    <div class="p-8">
                        @if(Auth::user()->tipo == 'professor')
                            <div class="mb-6 bg-yellow-100 border-4 border-yellow-400 p-4 font-bold text-yellow-800 flex items-center gap-3">
                                <span>👁️</span> MODO DE APENAS LEITURA (VISÃO MESTRE)
                            </div>
                        @endif

                        <form id="formVenda" action="{{ route('aluno.vendas.store') }}" method="POST">
                            @csrf
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                                <div>
                                    <label class="block text-xs font-black text-gray-700 uppercase mb-1">Selecionar Cliente</label>
                                    <select name="cliente_id" id="cliente_id" class="w-full border-2 border-black rounded p-2 font-bold focus:ring-0 focus:border-green-600" required>
                                        <option value="">-- Buscar na Carteira --</option>
                                        @foreach($clientes as $cliente)
                                            <option value="{{ $cliente->id }}">{{ $cliente->nome_razao_social }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-black text-gray-700 uppercase mb-1">Data de Entrega Prometida</label>
                                    <input type="date" name="data_entrega_solicitada" id="data_entrega" class="w-full border-2 border-black rounded p-2 font-bold focus:ring-0 focus:border-green-600" required>
                                </div>

                                <div class="bg-gray-800 text-white p-3 rounded border-2 border-black text-center shadow-[4px_4px_0px_0px_rgba(0,0,0,0.3)]">
                                    <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Data do Sistema</span>
                                    <span class="block text-xl font-black text-green-400 font-mono">{{ \Carbon\Carbon::parse($dataSimulacao)->format('d/m/Y') }}</span>
                                </div>
                            </div>

                            {{-- Itens do Pedido --}}
                            <div class="mb-8">
                                <h4 class="font-black text-sm text-gray-600 uppercase mb-4 border-b-2 border-gray-100 pb-1">Composição do Pedido</h4>
                                <div class="space-y-4">
                                    <template x-for="(item, index) in itens" :key="index">
                                        <div class="flex gap-4 items-end bg-gray-50 p-4 border-2 border-black rounded shadow-[4px_4px_0px_0px_rgba(0,0,0,0.05)]">
                                            <div class="flex-1">
                                                <label class="text-[10px] font-black text-gray-500 uppercase">Produto / SKU</label>
                                                <select :name="'itens['+index+'][produto_id]'" x-model="item.produto_id" class="w-full border-2 border-gray-300 rounded font-bold text-sm" required>
                                                    <option value="">Selecione o item...</option>
                                                    @foreach($produtos as $prod)
                                                        <option value="{{ $prod->id }}">{{ $prod->nome }} (R$ {{ number_format($prod->preco_venda, 2, ',', '.') }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="w-32">
                                                <label class="text-[10px] font-black text-gray-500 uppercase">Qtd</label>
                                                <input type="number" :name="'itens['+index+'][quantidade]'" x-model="item.quantidade" class="w-full border-2 border-gray-300 rounded font-black text-center" min="1" required>
                                            </div>
                                            
                                            @if(Auth::user()->tipo == 'aluno')
                                                <button type="button" @click="itens = itens.filter((_, i) => i !== index)" class="bg-white border-2 border-black p-2 rounded text-red-600 hover:bg-red-50" x-show="itens.length > 1">
                                                   @if(Auth::user()->acessibilidade_visual)
    <svg class="w-5 h-5 mx-auto" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="2" style="filter:none!important; stroke:#ffff00 !important;">
        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
    </svg>
@else
    🗑️
@endif 
                                                </button>
                                            @endif
                                        </div>
                                    </template>
                                </div>
                                
                                @if(Auth::user()->tipo == 'aluno')
                                    <button type="button" @click="itens.push({ produto_id: '', quantidade: 1 })" class="mt-4 bg-white border-2 border-black px-4 py-1 text-xs font-black uppercase hover:bg-green-50 transition shadow-[2px_2px_0px_0px_black] active:shadow-none">
                                        + Adicionar Item
                                    </button>
                                @endif
                            </div>

                        <div class="text-right border-t-2 border-dashed border-gray-200 pt-6 flex justify-end items-center gap-4">
                                @if(Auth::user()->tipo == 'aluno')
                                    
                                    {{-- CASO 1: BLOQUEADO PELO LIMITE --}}
                                    @if(isset($dadosStats['limite_info']) && $dadosStats['limite_info']['bloqueado'])
                                        <div class="text-right">
                                            <p class="text-[10px] font-black text-red-600 uppercase animate-pulse">
                                                ⛔ Cota Diária Atingida ({{ $dadosStats['limite_info']['usado'] }}/{{ $dadosStats['limite_info']['total'] }})
                                            </p>
                                            <button type="button" disabled class="bg-gray-300 text-gray-500 font-black py-4 px-12 rounded border-2 border-gray-400 cursor-not-allowed uppercase tracking-widest shadow-none">
                                                Mercado Fechado
                                            </button>
                                        </div>
                                    
                                    {{-- CASO 2: LIBERADO --}}
                                    @else
                                        <div class="text-right">
                                            @if(isset($dadosStats['limite_info']))
                                                <p class="text-[9px] font-bold text-green-600 uppercase mb-1">
                                                    Cota de Vendas: {{ $dadosStats['limite_info']['usado'] }} de {{ $dadosStats['limite_info']['total'] }}
                                                </p>
                                            @endif
                                            {{-- Botão que dispara o Modal --}}
                                            <button type="button" @click="abrirConfirmacao()" class="bg-green-600 text-white font-black py-4 px-12 rounded border-2 border-black shadow-[6px_6px_0px_0px_black] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-none transition uppercase tracking-widest">
                                            @if(Auth::user()->acessibilidade_visual)
    <svg class="w-5 h-5 inline mr-2" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="3" style="filter:none!important; stroke:#ffff00 !important;">
        <path d="M13 10V3L4 14h7v7l9-11h-7z"></path>
    </svg>
@else
    🚀
@endif 
Finalizar Pedido    
                                            </button>
                                        </div>
                                    @endif

                                @endif
                            </div>    
                        </form>
                    </div>

                    {{-- ======================================================= --}}
                    {{-- MODAL DE CONFIRMAÇÃO (NEO-BRUTALISTA)                   --}}
                    {{-- ======================================================= --}}
                    <div x-show="showConfirm" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-90"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                         style="display: none;">
                        
                        <div class="bg-white border-4 border-black p-8 max-w-md w-full shadow-[15px_15px_0px_0px_rgba(0,0,0,1)] relative">
                            <div class="text-center mb-6">
                                @if(Auth::user()->acessibilidade_visual)
    <!-- Ícone de Antena Amarelo para o Modal -->
    <svg class="w-16 h-16 mx-auto" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="2" style="filter: none !important; stroke: #ffff00 !important;">
        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
        <path d="M12 12m-5 0a5 5 0 1 0 10 0a5 5 0 1 0 -10 0"></path>
        <path d="M12 12l0 .01"></path>
        <path d="M7 12a5 5 0 0 1 5 -5"></path>
        <path d="M12 17a5 5 0 0 1 -5 -5"></path>
    </svg>
@else
    <span class="text-5xl">📡</span>
@endif
                                <h4 class="text-2xl font-black uppercase mt-4 tracking-tighter">Transmitir Ordem?</h4>
                                <p class="text-xs font-bold text-gray-500 uppercase italic">Revise os dados antes do envio</p>
                            </div>

                            <div class="bg-yellow-50 border-2 border-black p-4 mb-6 space-y-2">
                                <p class="text-xs font-bold uppercase"><span class="text-gray-500">Cliente:</span> <span x-text="clienteNome" class="text-black"></span></p>
                                <p class="text-xs font-bold uppercase"><span class="text-gray-500">Entrega:</span> <span x-text="dataEntrega" class="text-black"></span></p>
                                <p class="text-xs font-bold uppercase">
    <span class="text-gray-500">Volume Total:</span> 
    <span x-text="totalPecas()" class="text-black text-lg"></span> <span class="text-black">peças</span>
</p>
                                <p class="text-[10px] font-black text-indigo-600 uppercase pt-2 border-t border-black/10">Vínculo: {{ $aluno->turma->nome_empresa }}</p>
                            </div>

                            <div class="flex gap-4">
                                <button @click="showConfirm = false" class="flex-1 bg-white border-2 border-black py-3 font-black uppercase hover:bg-gray-100 transition">
                                    Cancelar
                                </button>
                                <button @click="loading = true; document.getElementById('formVenda').submit();" 
                                        :disabled="loading"
                                        class="flex-1 bg-green-500 text-white border-2 border-black py-3 font-black uppercase shadow-[4px_4px_0px_0px_black] hover:shadow-none transition disabled:bg-gray-400 disabled:cursor-not-allowed">
                                    <span x-show="!loading">Confirmar</span>
                                    <span x-show="loading" class="flex items-center justify-center gap-2">
                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Enviando...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ======================================================= --}}
            {{-- MODO 3: HISTÓRICO COM VISUAL INDUSTRIAL                 --}}
            {{-- ======================================================= --}}
            @if($modo == 'historico')
                <div class="bg-white border-4 border-black shadow-[8px_8px_0px_0px_black] rounded-xl overflow-hidden">
                    <div class="bg-blue-600 text-white p-4 border-b-4 border-black flex flex-col md:flex-row justify-between items-center gap-4">
                        <h3 class="font-black text-xl uppercase tracking-tighter">Carteira de Pedidos Realizados</h3>
                        
                        {{-- Filtros Estilizados --}}
                        <form method="GET" action="{{ request()->url() }}" class="flex flex-wrap gap-2 items-center justify-end">
                            <input type="hidden" name="tela" value="historico">
                            
                            <select name="filtro_cliente" class="text-[10px] font-black uppercase border-2 border-black rounded p-1 text-black focus:ring-0">
                                <option value="">Todos Clientes</option>
                                @foreach($clientes as $c)
                                    <option value="{{ $c->id }}" {{ request('filtro_cliente') == $c->id ? 'selected' : '' }}>{{ $c->nome_razao_social }}</option>
                                @endforeach
                            </select>

                            <select name="filtro_status" class="text-[10px] font-black uppercase border-2 border-black rounded p-1 text-black focus:ring-0">
                                <option value="">Todos Status</option>
                                @foreach(['Novo', 'Em Produção', 'Concluído', 'Faturado'] as $st)
                                    <option value="{{ $st }}" {{ request('filtro_status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                                @endforeach
                            </select>

                            <button class="bg-black text-white text-[10px] px-4 py-1.5 rounded font-black uppercase hover:bg-gray-800 transition">
                                Aplicar
                            </button>
                            
                            @if(request()->filled('filtro_cliente') || request()->filled('filtro_status'))
                                <a href="{{ Auth::user()->tipo == 'professor' ? route('professor.monitoramento.vendas', $aluno->turma_id).'?tela=historico' : route('aluno.vendas.index', ['tela' => 'historico']) }}" 
                                   class="bg-white text-red-600 border-2 border-black px-2 py-1 rounded text-[10px] font-black uppercase">X</a>
                            @endif
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-100 text-gray-700 uppercase text-[10px] font-black border-b-2 border-black">
                                <tr>
                                    <th class="px-6 py-4">ID</th>
                                    <th class="px-6 py-4">Parceiro Comercial</th>
                                    <th class="px-6 py-4">Data Emissão</th>
                                    <th class="px-6 py-4">Data Entrega</th>
                                    <th class="px-6 py-4 text-right">Valor Total</th>
                                    <th class="px-6 py-4 text-center">Status Operacional</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y-2 divide-gray-100">
                                @forelse($pedidos as $p)
                                    <tr class="hover:bg-yellow-50 transition">
                                        <td class="px-6 py-4 font-black text-gray-400">#{{ $p->id }}</td>
                                        <td class="px-6 py-4">
                                            <div class="font-black text-gray-900 uppercase">{{ $p->cliente->nome_razao_social ?? 'Ex-Cliente' }}</div>
                                            <div class="text-[10px] font-bold text-indigo-500 uppercase">{{ $p->cliente->cidade ?? 'Global' }}</div>
                                        </td>
                                        <td class="px-6 py-4 font-bold text-gray-600">{{ \Carbon\Carbon::parse($p->data_pedido)->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 font-black {{ \Carbon\Carbon::parse($p->data_entrega_solicitada) < \Carbon\Carbon::parse($dataSimulacao) ? 'text-red-600 animate-pulse' : 'text-gray-800' }}">
                                            {{ \Carbon\Carbon::parse($p->data_entrega_solicitada)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-right font-black text-gray-900">
                                            R$ {{ number_format($p->valor_total, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $badgeColor = match($p->status) {
                                                    'Novo' => 'bg-blue-100 text-blue-800 border-blue-300',
                                                    'Em Produção' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                                    'Concluído' => 'bg-green-100 text-green-800 border-green-300',
                                                    'Faturado' => 'bg-gray-800 text-white border-black',
                                                    default => 'bg-gray-100 text-gray-500 border-gray-300'
                                                };
                                            @endphp
                                            <span class="px-3 py-1 border-2 {{ $badgeColor }} rounded text-[9px] font-black uppercase tracking-widest">
                                                {{ $p->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 font-bold uppercase tracking-widest bg-gray-50 italic">
                                            Nenhum registro de venda localizado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>