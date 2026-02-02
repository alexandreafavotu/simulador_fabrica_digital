<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span class="text-3xl">📦</span> {{ __('Painel de Compras & Suprimentos') }} 
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
                    ⬅ Menu Compras
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
                        <h3 class="font-black text-xl uppercase tracking-tighter">Plantão de Suprimentos:</h3>
                        <p class="font-bold text-lg leading-tight">{{ $aluno->turma->mensagem_plantao_caos }}</p>
                    </div>
                </div>
            @endif

            {{-- ======================================================= --}}
            {{-- MODO 1: MENU INICIAL (LAUNCHER CARDS 3D)                --}}
            {{-- ======================================================= --}}
            @if($modo == 'menu')
                <div class="mb-8 text-center">
                    <h3 class="text-3xl font-black text-gray-800 uppercase tracking-tighter">Central de Aquisições</h3>
                    <p class="font-bold text-indigo-600 uppercase text-xs tracking-widest">Olá, Comprador {{ explode(' ', $aluno->nome)[0] }}! Gerencie o abastecimento:</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                    
                    @php
                        $rotaBase = Auth::user()->tipo == 'professor' ? 'professor.monitoramento.compras' : 'aluno.compras.dashboard';
                        $params = Auth::user()->tipo == 'professor' ? ['turma_id' => $aluno->turma_id] : [];
                    @endphp

                    {{-- CARD 1: COTAÇÃO (ROXO) --}}
                    <a href="{{ route($rotaBase, array_merge($params, ['tela' => 'cotacao'])) }}" 
                       class="group bg-white border-4 border-black p-6 shadow-[10px_10px_0px_0px_rgba(168,85,247,1)] hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all duration-200">
                        <div class="flex flex-col items-center text-center">
                            <div class="bg-purple-100 border-2 border-black p-5 rounded-full mb-4 group-hover:bg-purple-400 transition">
                                <span class="text-4xl">💰</span>
                            </div>
                            <h4 class="text-xl font-black text-gray-800 uppercase leading-none">Realizar Cotações</h4>
                            <p class="text-[10px] font-bold text-gray-500 mt-2 uppercase tracking-widest">Homologação e Compra</p>
                            
                            @if($stats['pendentes'] > 0)
                                <span class="mt-4 bg-purple-600 text-white text-[10px] font-black px-3 py-1 rounded-full animate-pulse border border-black shadow-[2px_2px_0px_0px_black]">
                                    {{ $stats['pendentes'] }} PENDENTES
                                </span>
                            @else
                                <span class="mt-4 bg-gray-100 text-gray-400 text-[10px] font-black px-3 py-1 rounded-full border border-gray-200 uppercase">Nada Pendente</span>
                            @endif
                        </div>
                    </a>

                    {{-- CARD 2: OCORRÊNCIAS (VERMELHO) --}}
                    <a href="{{ route($rotaBase, array_merge($params, ['tela' => 'recusados'])) }}" 
                       class="group bg-white border-4 border-black p-6 shadow-[10px_10px_0px_0px_rgba(239,68,68,1)] hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all duration-200">
                        <div class="flex flex-col items-center text-center">
                            <div class="bg-red-100 border-2 border-black p-5 rounded-full mb-4 group-hover:bg-red-400 transition">
                                <span class="text-4xl">⚠️</span>
                            </div>
                            <h4 class="text-xl font-black text-gray-800 uppercase leading-none">Log de Ocorrências</h4>
                            <p class="text-[10px] font-bold text-gray-500 mt-2 uppercase tracking-widest">Tratativa de Recusas</p>
                            
                            @if($stats['recusados'] > 0)
                                <span class="mt-4 bg-red-600 text-white text-[10px] font-black px-3 py-1 rounded-full animate-bounce border border-black shadow-[2px_2px_0px_0px_black]">
                                    {{ $stats['recusados'] }} A RESOLVER!
                                </span>
                            @else
                                <span class="mt-4 bg-green-100 text-green-700 text-[10px] font-black px-3 py-1 rounded-full border border-green-200 uppercase">Sem Problemas</span>
                            @endif
                        </div>
                    </a>

                    {{-- CARD 3: HISTÓRICO (AZUL) --}}
                    <a href="{{ route($rotaBase, array_merge($params, ['tela' => 'historico'])) }}" 
                       class="group bg-white border-4 border-black p-6 shadow-[10px_10px_0px_0px_rgba(59,130,246,1)] hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all duration-200">
                        <div class="flex flex-col items-center text-center">
                            <div class="bg-blue-100 border-2 border-black p-5 rounded-full mb-4 group-hover:bg-blue-400 transition">
                                <span class="text-4xl">🚚</span>
                            </div>
                            <h4 class="text-xl font-black text-gray-800 uppercase leading-none">Histórico de Pedidos</h4>
                            <p class="text-[10px] font-bold text-gray-500 mt-2 uppercase tracking-widest">Rastreio de Entregas</p>
                            <span class="mt-4 bg-blue-100 text-blue-800 border border-blue-400 text-[10px] font-black px-3 py-1 rounded-full uppercase">
                                {{ $stats['chegando'] }} A Caminho
                            </span>
                        </div>
                    </a>

                </div>
            @endif

            {{-- ======================================================= --}}
            {{-- MODO 2: TELA DE COTAÇÃO (COM DESTAQUE URGENTE)          --}}
            {{-- ======================================================= --}}
            @if($modo == 'cotacao')
                <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_black] overflow-hidden">
                    <div class="bg-purple-600 text-white p-4 border-b-4 border-black flex flex-col md:flex-row justify-between items-center gap-4">
                        <h3 class="font-black text-xl uppercase tracking-tighter">Novas Solicitações (PCP)</h3>
                        
                        <form method="GET" action="{{ request()->url() }}" class="flex gap-2">
                            <input type="hidden" name="tela" value="cotacao">
                            <input type="text" name="busca" placeholder="Buscar material..." value="{{ request('busca') }}" class="text-[10px] font-black border-2 border-black rounded p-1 text-black focus:ring-0 w-40">
                            <button class="bg-black text-white text-[10px] px-3 rounded font-black uppercase hover:bg-gray-800 transition">Buscar</button>
                        </form>
                    </div>

                    <div class="p-0 overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-100 text-gray-700 uppercase text-[10px] font-black border-b-2 border-black">
                                <tr>
                                    <th class="px-6 py-4">Prioridade</th>
                                    <th class="px-6 py-4">Item Solicitado</th>
                                    <th class="px-6 py-4 text-center">Volume Necessário</th>
                                    <th class="px-6 py-4 text-center">Ação</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y-2 divide-gray-100">
                                @forelse($dados as $item)
                                    <tr class="hover:bg-yellow-50 transition {{ $item->urgente ? 'bg-red-50' : '' }}">
                                        <td class="px-6 py-4">
                                            @if($item->urgente)
                                                <span class="bg-red-600 text-white text-[9px] font-black px-2 py-1 rounded border border-black animate-pulse uppercase">
                                                    ⚠ URGENTE
                                                </span>
                                            @else
                                                <span class="bg-gray-200 text-gray-600 text-[9px] font-black px-2 py-1 rounded border border-gray-400 uppercase">
                                                    NORMAL
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-black text-gray-800 uppercase">{{ $item->materiaPrima->nome }}</div>
                                            <div class="text-[10px] font-bold text-gray-400">Ref: Venda #{{ $item->pedido->id }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-center font-black text-lg text-purple-700">
                                            {{ number_format($item->quantidade, 2, ',', '.') }} 
                                            <span class="text-[10px] text-gray-400 font-bold uppercase">{{ $item->materiaPrima->unidade_medida }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if(Auth::user()->tipo == 'aluno')
                                                <a href="{{ route('aluno.compras.confirmar', $item->id) }}" class="bg-purple-600 hover:bg-purple-700 text-white py-2 px-6 rounded border-2 border-black shadow-[3px_3px_0px_0px_black] active:shadow-none active:translate-y-[1px] text-[10px] font-black uppercase transition">
                                                    Cotar Agora
                                                </a>
                                            @else
                                                <span class="text-[10px] font-bold text-gray-400 italic uppercase">Visão Mestre</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="p-12 text-center text-gray-400 font-black italic uppercase">Nenhuma solicitação de compra pendente.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- ======================================================= --}}
            {{-- MODO 3: RECUSADOS (HISTÓRICO DE FALHAS)                 --}}
            {{-- ======================================================= --}}
            @if($modo == 'recusados')
                <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(220,38,38,0.3)] overflow-hidden">
                    <div class="bg-red-600 text-white p-4 border-b-4 border-black flex flex-col md:flex-row justify-between items-center gap-4">
                        <div>
                            <h3 class="font-black text-xl uppercase tracking-tighter">Ocorrências de Recebimento</h3>
                            <p class="text-[10px] font-bold opacity-80 uppercase">Materiais rejeitados pelo Almoxarifado (WMS)</p>
                        </div>
                        
                        <form method="GET" action="{{ request()->url() }}" class="flex flex-wrap gap-2 items-center justify-end">
                            <input type="hidden" name="tela" value="recusados">
                            <input type="text" name="filtro_fornecedor" placeholder="Fornecedor..." value="{{ request('filtro_fornecedor') }}" class="text-[10px] font-black border-2 border-black rounded p-1 text-black w-24 focus:ring-0">
                            <input type="text" name="busca_material" placeholder="Material..." value="{{ request('busca_material') }}" class="text-[10px] font-black border-2 border-black rounded p-1 text-black w-24 focus:ring-0">
                            <button class="bg-black text-white text-[10px] px-3 py-1.5 rounded font-black uppercase hover:bg-gray-800 transition">Filtrar</button>
                            
                            @if(request()->anyFilled(['filtro_data', 'filtro_fornecedor', 'busca_material']))
                                @php
                                    $linkLimpar = Auth::user()->tipo == 'professor' 
                                        ? route('professor.monitoramento.compras', ['turma_id' => $aluno->turma_id, 'tela' => 'recusados'])
                                        : route('aluno.compras.dashboard', ['tela' => 'recusados']);
                                @endphp
                                <a href="{{ $linkLimpar }}" class="bg-white text-red-600 border-2 border-black px-2 py-0.5 rounded text-[10px] font-black uppercase hover:bg-red-50">X</a>
                            @endif
                        </form>
                    </div>

                    <div class="p-0 overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-100 text-gray-700 uppercase text-[10px] font-black border-b-2 border-black">
                                <tr>
                                    <th class="px-6 py-4">Data/Hora</th>
                                    <th class="px-6 py-4">Fornecedor (Falha)</th>
                                    <th class="px-6 py-4">Material / Motivo da Recusa</th>
                                    <th class="px-6 py-4 text-center">Status Tratativa</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y-2 divide-gray-100">
                                @forelse($dados as $item)
                                    <tr class="hover:bg-red-50 transition {{ $item->recompra_gerada ? 'opacity-60 grayscale' : '' }}">
                                        <td class="px-6 py-4 font-mono text-xs font-bold text-gray-500">
                                            {{ $item->updated_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 font-bold text-red-600 uppercase text-xs">
                                            {{ $item->fornecedor->nome_razao_social ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-black text-gray-800 uppercase">{{ $item->materiaPrima->nome }}</div>
                                            <div class="text-xs font-bold italic text-gray-500 border-l-4 border-red-300 pl-2 mt-1">
                                                "{{ $item->motivo_recusa ?? 'Sem motivo registrado' }}"
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($item->recompra_gerada)
                                                <span class="text-[10px] font-black text-green-600 border-2 border-green-200 px-2 py-1 rounded bg-green-50 uppercase tracking-widest">
                                                    RESOLVIDO
                                                </span>
                                            @else
                                                @if(Auth::user()->tipo == 'aluno')
                                                {{-- BOTÃO RECOMPRAR (COM MODAL INDUSTRIAL) --}}
<x-modal-confirmacao 
    id="recompra-{{ $item->id }}"
    title="RECOMPRA URGENTE?"
    message="Você está prestes a gerar uma nova solicitação de compra para substituir o material recusado. Deseja prosseguir?"
    action="{{ route('aluno.compras.recomprar', $item->id) }}"
    method="POST"
    type="danger" {{-- Vermelho para indicar urgência/correção de erro --}}
    buttonText="SIM, INICIAR RECOMPRA"
    class="bg-red-600 hover:bg-red-700 text-white font-black py-1.5 px-4 rounded border-2 border-black shadow-[3px_3px_0px_0px_black] active:shadow-none active:translate-y-[1px] text-[10px] uppercase transition animate-pulse"
>
    ↻ Recomprar
</x-modal-confirmacao>    
                                                @else
                                                    <span class="text-[10px] font-bold text-red-400 italic uppercase">Pendente</span>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="p-12 text-center text-gray-400 font-bold italic uppercase">Nenhuma falha de entrega registrada.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- ======================================================= --}}
            {{-- MODO 4: HISTÓRICO GERAL                                 --}}
            {{-- ======================================================= --}}
            @if($modo == 'historico')
                <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_black] overflow-hidden">
                    <div class="bg-blue-600 text-white p-4 border-b-4 border-black flex flex-col md:flex-row justify-between items-center gap-4">
                        <h3 class="font-black text-xl uppercase tracking-tighter">Rastreio de Pedidos (Compras Aceitas)</h3>
                        
                        <form method="GET" action="{{ request()->url() }}">
                            <input type="hidden" name="tela" value="historico">
                            <select name="filtro_status" class="text-[10px] font-black uppercase border-2 border-black rounded p-1 text-black focus:ring-0" onchange="this.form.submit()">
                                <option value="">Status (Todos)</option>
                                <option value="Aguardando Entrega" {{ request('filtro_status') == 'Aguardando Entrega' ? 'selected' : '' }}>Aguardando Entrega</option>
                                <option value="Concluído" {{ request('filtro_status') == 'Concluído' ? 'selected' : '' }}>Concluído</option>
                            </select>
                        </form>
                    </div>

                    <div class="p-0 overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-100 text-gray-700 uppercase text-[10px] font-black border-b-2 border-black">
                                <tr>
                                    <th class="px-6 py-4">OC ID</th>
                                    <th class="px-6 py-4">Material Comprado</th>
                                    <th class="px-6 py-4">Fornecedor</th>
                                    <th class="px-6 py-4 text-center">Previsão Entrega</th>
                                    <th class="px-6 py-4 text-center">Status Logístico</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y-2 divide-gray-100">
                                @forelse($dados as $item)
                                    <tr class="hover:bg-yellow-50 transition">
                                        <td class="px-6 py-4 font-black text-gray-400">#{{ $item->id }}</td>
                                        <td class="px-6 py-4 font-black text-gray-800 uppercase">{{ $item->materiaPrima->nome }}</td>
                                        <td class="px-6 py-4 text-xs font-bold text-gray-600 uppercase">{{ $item->fornecedor->nome_razao_social ?? '---' }}</td>
                                        <td class="px-6 py-4 text-center text-xs font-mono font-bold text-blue-600">
                                            {{ $item->data_entrega_prevista ? \Carbon\Carbon::parse($item->data_entrega_prevista)->format('d/m/Y') : '--' }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $badgeStatus = match($item->status) {
                                                    'Aguardando Entrega' => 'bg-blue-100 text-blue-700 border-blue-400 animate-pulse',
                                                    'Concluído' => 'bg-green-100 text-green-700 border-green-400',
                                                    default => 'bg-gray-100 text-gray-500 border-gray-300'
                                                };
                                            @endphp
                                            <span class="px-2 py-1 border-2 {{ $badgeStatus }} rounded text-[9px] font-black uppercase tracking-widest">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="p-12 text-center text-gray-400 font-bold italic uppercase">Histórico vazio.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>