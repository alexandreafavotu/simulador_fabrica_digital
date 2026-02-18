<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                @if(Auth::user()->acessibilidade_visual)
    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="2"
         style="stroke:#FFFF00!important; fill:none!important;">
        <rect x="1" y="3" width="15" height="13"></rect>
        <polygon points="16 8 20 8 23 11 23 16 16 16"></polygon>
        <circle cx="5.5" cy="18.5" r="2.5"></circle>
        <circle cx="18.5" cy="18.5" r="2.5"></circle>
    </svg>
@else
    <span class="text-3xl">🚚</span>
@endif
 {{ __('Expedição e Faturamento') }} 
            </h2>
            
            @if(Auth::user()->tipo == 'professor')
                @if($modo != 'menu')
                    <a href="{{ route('professor.monitoramento.expedicao', $aluno->turma_id) }}" 
                       class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                        ⬅ Voltar à Expedição
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
            @elseif(isset($modo) && $modo != 'menu')
                <a href="{{ url()->current() }}" 
   class="bg-white text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">

    @if(Auth::user()->acessibilidade_visual)
        <svg class="w-4 h-4 inline" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="4"
            style="filter:none!important; stroke:#ffff00!important; background-color:transparent!important;">
            <path d="M19 12H5M12 19l-7-7 7-7"></path>
        </svg>
    @else
        ⬅
    @endif

    Menu Expedição
</a>

            @endif
        </div>
    </x-slot>
    

    <div class="py-12 bg-yellow-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- ALERTA DE CAOS (GREVE) --}}
            @php
                $bloqueioExpedicao = false;
                $dataFimExpedicao = null;
                if(isset($aluno->turma->bloqueio_expedicao_ate)) {
                    $dataJogo = \Carbon\Carbon::parse($aluno->turma->data_jogo);
                    $dataFimExpedicao = \Carbon\Carbon::parse($aluno->turma->bloqueio_expedicao_ate);
                    if($dataJogo->lt($dataFimExpedicao)) $bloqueioExpedicao = true;
                }
            @endphp
            @if($bloqueioExpedicao)
                <div class="bg-black text-white p-6 border-4 border-red-600 shadow-[8px_8px_0px_0px_rgba(220,38,38,0.5)] flex items-center gap-6 animate-pulse">
                    <div class="bg-red-600 p-4 rounded border-2 border-white">
                        @if(Auth::user()->acessibilidade_visual)
    <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="2"
         style="stroke:#FFFF00!important; fill:none!important;">
        <polygon points="7 2 17 2 22 7 22 17 17 22 7 22 2 17 2 7" />
        <line x1="8" y1="8" x2="16" y2="16" />
        <line x1="16" y1="8" x2="8" y2="16" />
    </svg>
@else
    <span class="text-4xl">🛑</span>
@endif

                    </div>
                    <div>
                        <h3 class="text-2xl font-black uppercase tracking-widest text-red-500">EXPEDIÇÃO SUSPENSA</h3>
                        <p class="font-bold text-gray-300">Motivo: Greve de Transportes / Bloqueio Logístico</p>
                        <p class="text-sm mt-1 text-yellow-400 font-mono">Previsão de Normalização: {{ $dataFimExpedicao->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            @endif

            {{-- ALERTA GERAL (NOTÍCIAS) --}}
            @if($aluno->turma->mensagem_plantao_caos)
                <div class="bg-red-500 text-white p-5 border-4 border-black shadow-[8px_8px_0px_0px_black] flex items-start gap-4">
                    @if(Auth::user()->acessibilidade_visual)
    <svg class="w-8 h-8"
         viewBox="0 0 24 24"
         fill="none"
         stroke="#FFFF00"
         stroke-width="2"
         style="stroke:#FFFF00!important; fill:none!important;">
        <path d="M11 5L6 9H2V15H6L11 19V5Z"></path>
        <path d="M19.07 4.93a10 10 0 0 1 0 14.14"></path>
        <path d="M15.54 8.46a5 5 0 0 1 0 7.07"></path>
    </svg>
@else
    <span class="text-3xl">📢</span>
@endif

                    <div>
                        <h3 class="font-black text-xl uppercase tracking-tighter">Plantão Logístico:</h3>
                        <p class="font-bold text-lg leading-tight">{{ $aluno->turma->mensagem_plantao_caos }}</p>
                    </div>
                </div>
            @endif

            {{-- ======================================================= --}}
            {{-- MODO 1: MENU INICIAL (LAUNCHER)                         --}}
            {{-- ======================================================= --}}
            @if($modo == 'menu')
                <div class="mb-8 text-center">
                    <h3 class="text-3xl font-black text-gray-800 uppercase tracking-tighter">Terminal de Saída (Outbound)</h3>
                    <p class="font-bold text-indigo-600 uppercase text-xs tracking-widest">Gerencie o embarque e faturamento:</p>
                </div>

                @php
                    $rotaBase = Auth::user()->tipo == 'professor' ? 'professor.monitoramento.expedicao' : 'aluno.expedicao.dashboard';
                    $params = Auth::user()->tipo == 'professor' ? ['turma_id' => $aluno->turma_id] : [];
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 max-w-5xl mx-auto">
                    
                    {{-- CARD 1: DOCK DE SAÍDA (VERDE) --}}
                    <a href="{{ route($rotaBase, array_merge($params, ['tela' => 'faturar'])) }}" 
                       class="group bg-white border-4 border-black p-8 shadow-[10px_10px_0px_0px_rgba(34,197,94,1)] hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all duration-200">
                        <div class="flex flex-col items-center text-center">
                            <div class="bg-green-100 border-2 border-black p-5 rounded-full mb-4 group-hover:bg-green-400 transition">
    @if(Auth::user()->acessibilidade_visual)
        <svg class="w-10 h-10"
             viewBox="0 0 24 24"
             fill="none"
             stroke="#FFFF00"
             stroke-width="2"
             style="stroke:#FFFF00!important; fill:none!important;">
            <path d="M3 7l9-4 9 4v10l-9 4-9-4V7z"/>
            <path d="M3 7l9 4 9-4"/>
            <path d="M12 11v10"/>
        </svg>
    @else
        <span class="text-5xl">📦</span>
    @endif
</div>

                            <h4 class="text-2xl font-black text-gray-800 uppercase leading-none">Dock de Saída</h4>
                            <p class="text-[10px] font-bold text-gray-500 mt-2 uppercase tracking-widest">Conferência e Emissão de NF</p>
                            
                            @if($stats['a_faturar'] > 0)
                                <span class="mt-4 bg-green-600 text-white text-[10px] font-black px-3 py-1 rounded-full animate-pulse border border-black shadow-[2px_2px_0px_0px_black]">
                                    {{ $stats['a_faturar'] }} PEDIDOS PRONTOS
                                </span>
                            @else
                                <span class="mt-4 bg-gray-100 text-gray-400 text-[10px] font-black px-3 py-1 rounded-full border border-gray-200 uppercase">Doca Vazia</span>
                            @endif
                        </div>
                    </a>

                    {{-- CARD 2: HISTÓRICO (AZUL) --}}
                    <a href="{{ route($rotaBase, array_merge($params, ['tela' => 'historico'])) }}" 
                       class="group bg-white border-4 border-black p-8 shadow-[10px_10px_0px_0px_rgba(59,130,246,1)] hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all duration-200">
                        <div class="flex flex-col items-center text-center">
                            <div class="bg-blue-100 border-2 border-black p-5 rounded-full mb-4 group-hover:bg-blue-400 transition">
    @if(Auth::user()->acessibilidade_visual)
        <svg class="w-10 h-10"
             viewBox="0 0 24 24"
             fill="none"
             stroke="#FFFF00"
             stroke-width="2"
             style="stroke:#FFFF00!important; fill:none!important;">
            <path d="M4 2h12l4 4v16H4z"/>
            <path d="M16 2v6h6"/>
            <line x1="8" y1="12" x2="16" y2="12"/>
            <line x1="8" y1="16" x2="16" y2="16"/>
        </svg>
    @else
        <span class="text-5xl">📄</span>
    @endif
</div>

                            <h4 class="text-2xl font-black text-gray-800 uppercase leading-none">Histórico de NFs</h4>
                            <p class="text-[10px] font-bold text-gray-500 mt-2 uppercase tracking-widest">Arquivo Morto Fiscal</p>
                            
                            <span class="mt-4 bg-blue-100 text-blue-800 border border-blue-400 text-[10px] font-black px-3 py-1 rounded-full uppercase">
                                {{ $stats['total_notas'] }} Notas Emitidas
                            </span>
                        </div>
                    </a>
                </div>
            @endif

            {{-- ======================================================= --}}
            {{-- MODO 2: TELA DE FATURAMENTO (DOCK DE SAÍDA)             --}}
            {{-- ======================================================= --}}
            @if($modo == 'faturar')
                <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(34,197,94,1)] overflow-hidden">
                    <div class="bg-green-500 text-white p-4 border-b-4 border-black flex flex-col md:flex-row justify-between items-center gap-4">
                        <h3 class="font-black text-xl uppercase tracking-tighter">Pedidos Aguardando Embarque</h3>

                        <form method="GET" action="{{ request()->url() }}" class="flex gap-2">
                            <input type="hidden" name="tela" value="faturar">
                            <select name="filtro_cliente" class="text-[10px] font-black uppercase border-2 border-black rounded p-1 text-black focus:ring-0">
                                <option value="">Todos Clientes</option>
                                @foreach($clientes as $c)
                                    <option value="{{ $c->id }}" {{ request('filtro_cliente') == $c->id ? 'selected' : '' }}>{{ Str::limit($c->nome_razao_social, 15) }}</option>
                                @endforeach
                            </select>
                            <button class="bg-black text-white text-[10px] px-3 rounded font-black uppercase hover:bg-gray-800 transition">Filtrar</button>
                        </form>
                    </div>

                    <div class="p-0 overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-100 text-gray-700 uppercase text-[10px] font-black border-b-2 border-black">
                                <tr>
                                    <th class="px-6 py-4">Pedido #</th>
                                    <th class="px-6 py-4">Cliente / Destino</th>
                                    <th class="px-6 py-4 text-center">Volumes</th>
                                    <th class="px-6 py-4 text-right">Valor Carga</th>
                                    <th class="px-6 py-4 text-center">Status Prazo</th>
                                    <th class="px-6 py-4 text-center">Ação</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y-2 divide-gray-100">
                                @forelse($pedidosParaFaturar as $pedido)
                                    <tr class="hover:bg-green-50 transition">
                                        <td class="px-6 py-4 font-black text-gray-400">#{{ $pedido->id }}</td>
                                        <td class="px-6 py-4 font-black text-gray-800 uppercase">{{ $pedido->cliente->nome_razao_social ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-[10px] font-bold border border-gray-300">
                                                {{ $pedido->itens->count() }} VOL
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right font-black text-green-700">
                                            R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $dataEntrega = \Carbon\Carbon::parse($pedido->data_entrega_solicitada);
                                                $hoje = \Carbon\Carbon::parse($aluno->turma->data_jogo);
                                                $dias = $hoje->diffInDays($dataEntrega, false);
                                                $corPrazo = $dias < 0 ? 'bg-red-100 text-red-700 border-red-300 animate-pulse' : 'bg-green-100 text-green-700 border-green-300';
                                            @endphp
                                            <div class="inline-block px-3 py-1 rounded text-[9px] font-black border {{ $corPrazo }} uppercase">
                                                {{ $dias < 0 ? 'ATRASADO' : 'NO PRAZO' }} ({{ abs(intval($dias)) }}d)
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if(Auth::user()->tipo == 'aluno')
                                                <a href="{{ route('aluno.expedicao.conferir', $pedido->id) }}" 
                                                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_black] active:shadow-none active:translate-y-[1px] text-[10px] font-black uppercase transition flex items-center justify-center gap-2">
                                                    @if(Auth::user()->acessibilidade_visual)
    <svg class="w-4 h-4 inline" viewBox="0 0 24 24" fill="none" 
         stroke="#FFFF00" stroke-width="2"
         style="stroke:#FFFF00!important; fill:none!important;">
        <path d="M3 7l9-4 9 4-9 4-9-4z"/>
        <path d="M3 7v10l9 4 9-4V7"/>
        <path d="M12 11v10"/>
    </svg>
@else
    <span>📦</span>
@endif
Conferir

                                                </a>
                                            @else
                                                <span class="text-[10px] font-bold text-gray-400 italic uppercase">Visão Mestre</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="p-12 text-center text-gray-400 font-bold italic uppercase">Doca vazia. Nenhum pedido liberado pela produção.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- ======================================================= --}}
            {{-- MODO 3: HISTÓRICO (NOTAS FISCAIS)                       --}}
            {{-- ======================================================= --}}
            @if($modo == 'historico')
                <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_black] overflow-hidden">
                    <div class="bg-blue-600 text-white p-4 border-b-4 border-black flex flex-col md:flex-row justify-between items-center gap-4">
                        <h3 class="font-black text-xl uppercase tracking-tighter">Arquivo Fiscal (NFs Emitidas)</h3>
                        
                        <form method="GET" action="{{ request()->url() }}" class="flex gap-2">
                            <input type="hidden" name="tela" value="historico">
                            <input type="text" name="busca" placeholder="NF ou Cliente..." value="{{ request('busca') }}"
class="text-[10px] font-black border-2 border-black rounded p-1 text-black focus:ring-0
@if(Auth::user()->acessibilidade_visual) placeholder-yellow-300 text-yellow-300 @endif">

                            <button class="bg-black text-white text-[10px] px-3 rounded font-black uppercase hover:bg-gray-800 transition">Buscar</button>
                        </form>
                    </div>

                    <div class="p-0 overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-100 text-gray-700 uppercase text-[10px] font-black border-b-2 border-black">
                                <tr>
                                    <th class="px-6 py-4">NF-e Nº</th>
                                    <th class="px-6 py-4">Emissão</th>
                                    <th class="px-6 py-4">Cliente</th>
                                    <th class="px-6 py-4 text-right">Valor Total</th>
                                    <th class="px-6 py-4 text-center">Chave de Acesso</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y-2 divide-gray-100">
                                @forelse($notasEmitidas as $nf)
                                    <tr class="hover:bg-blue-50 transition">
                                        <td class="px-6 py-4">
                                            <a href="{{ route('aluno.expedicao.nota', $nf->id) }}" target="_blank" class="text-blue-600 font-black hover:underline flex items-center gap-1">
                                                @if(Auth::user()->acessibilidade_visual)
    <svg class="w-5 h-5 inline" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="2"
         style="filter:none !important; stroke:#ffff00 !important;">
        <rect x="6" y="3" width="12" height="18" rx="2"></rect>
        <line x1="8" y1="7" x2="16" y2="7"></line>
        <line x1="8" y1="11" x2="16" y2="11"></line>
        <line x1="8" y1="15" x2="14" y2="15"></line>
    </svg>
@else
    📄
@endif

{{ $nf->numero_nota }}

                                            </a>
                                        </td>
                                        <td class="px-6 py-4 font-bold text-gray-600 font-mono text-xs">
                                            {{ \Carbon\Carbon::parse($nf->data_emissao)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 font-black text-gray-800 uppercase">{{ $nf->pedido->cliente->nome_razao_social ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 text-right font-black text-gray-900">
                                            R$ {{ number_format($nf->valor_total, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="text-[9px] font-mono bg-gray-100 px-2 py-1 rounded border border-gray-300 inline-block text-gray-500 tracking-tighter">
                                                {{ $nf->chave_acesso }}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="p-12 text-center text-gray-400 font-bold italic uppercase">Nenhuma nota fiscal no arquivo.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>