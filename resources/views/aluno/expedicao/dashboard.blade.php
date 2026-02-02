<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span class="text-3xl">🚚</span> {{ __('Expedição e Faturamento') }} 
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
                        ⬅ Sair do Setor
                    </a>
                @endif
            @elseif(isset($modo) && $modo != 'menu')
                <a href="{{ url()->current() }}" 
                   class="bg-white text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                    ⬅ Menu Expedição
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
                        <span class="text-4xl">🛑</span>
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
                    <span class="text-3xl">📢</span>
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
                                <span class="text-5xl">📦</span>
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
                                <span class="text-5xl">📄</span>
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
                                                    <span>📦</span> Conferir
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
                            <input type="text" name="busca" placeholder="NF ou Cliente..." value="{{ request('busca') }}" class="text-[10px] font-black border-2 border-black rounded p-1 text-black focus:ring-0">
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
                                                <span class="text-lg">📄</span> {{ $nf->numero_nota }}
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