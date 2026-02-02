<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span class="text-3xl">📊</span> {{ __('Painel de Controle - PCP') }} 
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
                    ⬅ Menu PCP
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
                        <h3 class="font-black text-xl uppercase tracking-tighter">Plantão do Planejamento:</h3>
                        <p class="font-bold text-lg leading-tight">{{ $aluno->turma->mensagem_plantao_caos }}</p>
                    </div>
                </div>
            @endif

            {{-- ======================================================= --}}
            {{-- MODO 1: MENU INICIAL (LAUNCHER CARDS 3D)                --}}
            {{-- ======================================================= --}}
            @if($modo == 'menu')
                <div class="mb-8 text-center">
                    <h3 class="text-3xl font-black text-gray-800 uppercase tracking-tighter">Console de Planejamento e Controle</h3>
                    <p class="font-bold text-indigo-600 uppercase text-xs tracking-widest">Olá, Programador {{ explode(' ', $aluno->nome)[0] }}! Selecione o fluxo de análise:</p>
                </div>

                @php
                    $rotaBase = Auth::user()->tipo == 'professor' ? 'professor.monitoramento.pcp' : 'aluno.pcp.dashboard';
                    $params = Auth::user()->tipo == 'professor' ? ['turma_id' => $aluno->turma_id] : [];
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    {{-- CARD 1: VENDAS (DEMANDA) --}}
                    <a href="{{ route($rotaBase, array_merge($params, ['tabela' => 'vendas'])) }}" 
                       class="group bg-white border-4 border-black p-6 shadow-[10px_10px_0px_0px_rgba(59,130,246,1)] hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all duration-200">
                        <div class="flex flex-col items-center text-center">
                            <div class="bg-blue-100 border-2 border-black p-5 rounded-full mb-4 group-hover:bg-blue-400 transition">
                                <span class="text-4xl">🛒</span>
                            </div>
                            <h4 class="text-xl font-black text-gray-800 uppercase leading-none">Vendas</h4>
                            <p class="text-[10px] font-bold text-gray-500 mt-2 uppercase tracking-widest">Análise de Demanda Bruta</p>
                            @if($dados['qtd_vendas'] > 0)
                                <span class="mt-4 bg-red-600 text-white text-[10px] font-black px-3 py-1 rounded-full animate-pulse border border-black shadow-[2px_2px_0px_0px_black]">
                                    {{ $dados['qtd_vendas'] }} NOVOS PEDIDOS
                                </span>
                            @else
                                <span class="mt-4 bg-gray-100 text-gray-400 text-[10px] font-black px-3 py-1 rounded-full border border-gray-200 uppercase">Sem pendências</span>
                            @endif
                        </div>
                    </a>

                    {{-- CARD 2: FÁBRICA (ORDENS) --}}
                    <a href="{{ route($rotaBase, array_merge($params, ['tabela' => 'producao'])) }}" 
                       class="group bg-white border-4 border-black p-6 shadow-[10px_10px_0px_0px_rgba(249,115,22,1)] hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all duration-200">
                        <div class="flex flex-col items-center text-center">
                            <div class="bg-orange-100 border-2 border-black p-5 rounded-full mb-4 group-hover:bg-orange-400 transition">
                                <span class="text-4xl">🏭</span>
                            </div>
                            <h4 class="text-xl font-black text-gray-800 uppercase leading-none">Produção</h4>
                            <p class="text-[10px] font-bold text-gray-500 mt-2 uppercase tracking-widest">Gestão de Carga e Capacidade</p>
                            <span class="mt-4 bg-orange-100 text-orange-800 border border-orange-400 text-[10px] font-black px-3 py-1 rounded-full uppercase">
                                {{ $dados['qtd_producao'] }} OPs Ativas
                            </span>
                        </div>
                    </a>

                    {{-- CARD 3: SUPRIMENTOS --}}
                    <a href="{{ route($rotaBase, array_merge($params, ['tabela' => 'compras'])) }}" 
                       class="group bg-white border-4 border-black p-6 shadow-[10px_10px_0px_0px_rgba(168,85,247,1)] hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all duration-200">
                        <div class="flex flex-col items-center text-center">
                            <div class="bg-purple-100 border-2 border-black p-5 rounded-full mb-4 group-hover:bg-purple-400 transition">
                                <span class="text-4xl">📦</span>
                            </div>
                            <h4 class="text-xl font-black text-gray-800 uppercase leading-none">Suprimentos</h4>
                            <p class="text-[10px] font-bold text-gray-500 mt-2 uppercase tracking-widest">Status de Abastecimento</p>
                            <span class="mt-4 bg-purple-100 text-purple-800 border border-purple-400 text-[10px] font-black px-3 py-1 rounded-full uppercase">
                                {{ $dados['qtd_compras'] }} OCs Pendentes
                            </span>
                        </div>
                    </a>
                </div>
            @else
                {{-- ======================================================= --}}
                {{-- MODOS DE TABELA (VENDAS / PRODUÇÃO / COMPRAS)          --}}
                {{-- ======================================================= --}}
                <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_black] overflow-hidden">
                    
                    {{-- === TELA DE VENDAS === --}}
                    @if($modo == 'vendas')
                        <div class="bg-blue-600 text-white p-4 border-b-4 border-black flex flex-col md:flex-row justify-between items-center gap-4">
                            <h3 class="font-black text-xl uppercase tracking-tighter">Análise de Demanda de Vendas</h3>
                            
                            <form method="GET" action="{{ request()->url() }}" class="flex gap-2">
                                <input type="hidden" name="tabela" value="vendas">
                                <select name="filtro_cliente" class="text-[10px] font-black uppercase border-2 border-black rounded p-1 text-black focus:ring-0">
                                    <option value="">Cliente (Todos)</option>
                                    @foreach($clientes as $c) <option value="{{$c->id}}" {{ request('filtro_cliente') == $c->id ? 'selected' : '' }}>{{ $c->nome_razao_social }}</option> @endforeach
                                </select>
                                <select name="filtro_produto" class="text-[10px] font-black uppercase border-2 border-black rounded p-1 text-black focus:ring-0">
                                    <option value="">Produto (Todos)</option>
                                    @foreach($produtos as $p) <option value="{{$p->id}}" {{ request('filtro_produto') == $p->id ? 'selected' : '' }}>{{ $p->nome }}</option> @endforeach
                                </select>
                                <button class="bg-black text-white text-[10px] px-3 rounded font-black uppercase hover:bg-gray-800 transition">Filtrar</button>
                            </form>
                        </div>

                        <div class="p-0 overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-100 text-gray-700 uppercase text-[10px] font-black border-b-2 border-black">
                                    <tr>
                                        <th class="px-6 py-4">ID</th>
                                        <th class="px-6 py-4">Cliente</th>
                                        <th class="px-6 py-4 text-center">Data Prometida</th>
                                        <th class="px-6 py-4 text-center">Processamento</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y-2 divide-gray-100">
                                    @foreach($dados as $p)
                                    <tr class="hover:bg-yellow-50 transition">
                                        <td class="px-6 py-4 font-black text-gray-400">#{{ $p->id }}</td>
                                        <td class="px-6 py-4 font-black text-gray-800 uppercase">{{ $p->cliente->nome_razao_social ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="bg-red-50 text-red-600 px-2 py-1 rounded border border-red-200 font-bold">
                                                {{ \Carbon\Carbon::parse($p->data_entrega_solicitada)->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if(Auth::user()->tipo == 'aluno')
                                                <a href="{{ route('aluno.pcp.analise', $p->id) }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-1.5 rounded border-2 border-black shadow-[2px_2px_0px_0px_black] font-black text-[10px] transition active:shadow-none active:translate-y-[1px]">
                                                    EXPLODIR MRP
                                                </a>
                                            @else
                                                <span class="text-[10px] font-bold text-gray-400 italic">Visualização Mestre</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                    @if($dados->isEmpty()) <tr><td colspan="4" class="p-10 text-center text-gray-400 font-bold italic">Sem novos pedidos na fila.</td></tr> @endif
                                </tbody>
                            </table>
                        </div>
                    @endif

                    {{-- === TELA DE PRODUÇÃO === --}}
                    @if($modo == 'producao')
                        <div class="bg-orange-500 text-white p-4 border-b-4 border-black flex flex-col md:flex-row justify-between items-center gap-4">
                            <h3 class="font-black text-xl uppercase tracking-tighter">Status Global do Chão de Fábrica</h3>
                            
                            <form method="GET" action="{{ request()->url() }}" class="flex gap-2">
                                <input type="hidden" name="tabela" value="producao">
                                <select name="filtro_status_op" class="text-[10px] font-black uppercase border-2 border-black rounded p-1 text-black focus:ring-0">
                                    <option value="">Status (Todos)</option>
                                    <option value="Aberta" {{ request('filtro_status_op') == 'Aberta' ? 'selected' : '' }}>Aberta</option>
                                    <option value="Em Produção" {{ request('filtro_status_op') == 'Em Produção' ? 'selected' : '' }}>Em Produção</option>
                                </select>
                                <button class="bg-black text-white text-[10px] px-3 rounded font-black uppercase">Filtrar</button>
                            </form>
                        </div>

                        <div class="p-0 overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-100 text-gray-700 uppercase text-[10px] font-black border-b-2 border-black">
                                    <tr>
                                        <th class="px-6 py-4">OP</th>
                                        <th class="px-6 py-4">Produto</th>
                                        <th class="px-6 py-4 text-center">Quantidade</th>
                                        <th class="px-6 py-4 text-center">Status Operacional</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y-2 divide-gray-100">
                                    @foreach($dados as $op)
                                    <tr class="hover:bg-yellow-50 transition">
                                        <td class="px-6 py-4 font-black text-gray-400">#{{ $op->id }}</td>
                                        <td class="px-6 py-4 font-black text-gray-800 uppercase">{{ $op->produto->nome }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="bg-gray-800 text-white px-3 py-1 rounded font-mono font-bold">{{ $op->quantidade }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $corOp = $op->status == 'Em Produção' ? 'bg-blue-100 text-blue-800 border-blue-400 animate-pulse' : 'bg-gray-100 text-gray-600 border-gray-300';
                                            @endphp
                                            <span class="px-2 py-1 rounded text-[10px] font-black uppercase border-2 {{ $corOp }}">
                                                {{ $op->status }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @if($dados->isEmpty()) <tr><td colspan="4" class="p-10 text-center text-gray-400 font-bold italic uppercase">Fábrica inativa no momento.</td></tr> @endif
                                </tbody>
                            </table>
                        </div>
                    @endif

                    {{-- === TELA DE COMPRAS === --}}
                    @if($modo == 'compras')
                        <div class="bg-purple-600 text-white p-4 border-b-4 border-black flex flex-col md:flex-row justify-between items-center gap-4">
                            <h3 class="font-black text-xl uppercase tracking-tighter">Gestão de Suprimentos e Abastecimento</h3>
                            
                            <form method="GET" action="{{ request()->url() }}" class="flex flex-wrap gap-2 items-center justify-end">
                                <input type="hidden" name="tabela" value="compras">
                                <input type="text" name="busca_material" placeholder="Buscar SKU/NOME..." value="{{ request('busca_material') }}" class="text-[10px] font-black border-2 border-black rounded p-1 text-black w-32 focus:ring-0">
                                <select name="filtro_status_compra" class="text-[10px] font-black uppercase border-2 border-black rounded p-1 text-black focus:ring-0">
                                    <option value="">Status (Todos)</option>
                                    <option value="Pendente" {{ request('filtro_status_compra') == 'Pendente' ? 'selected' : '' }}>Pendente</option>
                                    <option value="Concluído" {{ request('filtro_status_compra') == 'Concluído' ? 'selected' : '' }}>Concluído</option>
                                </select>
                                <button class="bg-black text-white text-[10px] px-3 py-1.5 rounded font-black uppercase">Filtrar</button>
                            </form>
                        </div>

                        <div class="p-0 overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-100 text-gray-700 uppercase text-[10px] font-black border-b-2 border-black">
                                    <tr>
                                        <th class="px-6 py-4">OC</th>
                                        <th class="px-6 py-4">Material / Insumo</th>
                                        <th class="px-6 py-4 text-center">Volume</th>
                                        <th class="px-6 py-4 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y-2 divide-gray-100">
                                    @foreach($dados as $oc)
                                    <tr class="hover:bg-yellow-50 transition">
                                        <td class="px-6 py-4 font-black text-gray-400">#{{ $oc->id }}</td>
                                        <td class="px-6 py-4">
                                            <div class="font-black text-gray-800 uppercase">{{ $oc->materiaPrima->nome }}</div>
                                            <div class="text-[9px] font-bold text-gray-400">SKU: {{ $oc->materiaPrima->sku }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-center font-bold text-gray-700">
                                            {{ number_format($oc->quantidade, 2, ',', '.') }} 
                                            <span class="text-[10px] text-gray-400 font-normal uppercase">{{ $oc->materiaPrima->unidade_medida }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $badgeC = match($oc->status) {
                                                    'Recusado' => 'bg-red-100 text-red-700 border-red-400',
                                                    'Pendente' => 'bg-yellow-100 text-yellow-700 border-yellow-400',
                                                    'Concluído' => 'bg-green-100 text-green-700 border-green-400',
                                                    'Aguardando Entrega' => 'bg-blue-100 text-blue-700 border-blue-400',
                                                    default => 'bg-gray-100 text-gray-500'
                                                };
                                            @endphp
                                            <span class="px-2 py-1 border-2 {{ $badgeC }} rounded text-[9px] font-black uppercase tracking-widest">
                                                {{ $oc->status }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @if($dados->isEmpty()) 
                                        <tr><td colspan="4" class="p-12 text-center text-gray-400 font-black uppercase tracking-tighter italic">Nenhuma movimentação de suprimentos.</td></tr> 
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>