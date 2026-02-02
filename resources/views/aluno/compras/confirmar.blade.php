<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span class="text-3xl">💰</span> {{ __('Cotação e Compra') }}
            </h2>

            {{-- NAVEGAÇÃO INTELIGENTE --}}
            @if(Auth::user()->tipo == 'professor')
                <a href="{{ route('professor.monitoramento.compras', $compra->pedido->turma_id) }}" 
                   class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                    ⬅ Voltar às Compras
                </a>
            @else
                <a href="{{ route('aluno.compras.dashboard', ['tela' => 'cotacao']) }}" 
                   class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                    ⬅ Voltar
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12 bg-yellow-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- 1. RESUMO DA REQUISIÇÃO (FICHA TÉCNICA) -->
            <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                <div class="bg-purple-600 text-white p-4 border-b-4 border-black flex items-center gap-3">
                    <span class="text-2xl">📋</span>
                    <div>
                        <h3 class="font-black text-xl uppercase tracking-tighter">Requisição de Compra #{{ $compra->id }}</h3>
                        <p class="text-xs text-purple-200 font-bold uppercase">Detalhes da Necessidade</p>
                    </div>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gray-50 p-4 rounded border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,0.1)]">
                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Material Solicitado</p>
                        <p class="text-xl font-black text-gray-800 mt-1 uppercase">{{ $compra->materiaPrima->nome }}</p>
                        <p class="text-xs text-gray-500 font-mono font-bold">{{ $compra->materiaPrima->sku }}</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,0.1)]">
                        <p class="text-[10px] font-black text-purple-800 uppercase tracking-widest">Volume de Compra</p>
                        <p class="text-3xl font-black text-purple-600 mt-1">
                            {{ number_format($compra->quantidade, 2, ',', '.') }} 
                            <span class="text-sm text-gray-500 font-bold">{{ $compra->materiaPrima->unidade_medida }}</span>
                        </p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,0.1)]">
                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Origem da Demanda</p>
                        <p class="text-sm font-bold text-gray-800 mt-1">Venda #{{ $compra->pedido_id }}</p>
                        <p class="text-xs text-gray-500 truncate">Cliente: {{ $compra->pedido->cliente->nome_razao_social ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- 2. PAINEL DE COTAÇÃO (FILTROS) -->
            <div class="bg-gray-100 border-4 border-black rounded-xl p-6 shadow-[8px_8px_0px_0px_rgba(0,0,0,0.2)]">
                <h4 class="font-black text-lg text-gray-800 uppercase mb-4 flex items-center gap-2">
                    <span>🔍</span> Análise de Mercado (Filtros)
                </h4>
                
                <form method="GET" action="{{ route('aluno.compras.confirmar', $compra->id) }}">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        
                        <!-- Busca -->
                        <div>
                            <label class="block text-[10px] font-black text-gray-600 uppercase mb-1">Buscar Fornecedor</label>
                            <input type="text" name="filtro_nome" value="{{ request('filtro_nome') }}" class="w-full border-2 border-black rounded p-2 text-sm font-bold focus:ring-0 focus:border-purple-600" placeholder="Nome...">
                        </div>

                        <!-- Ordenar -->
                        <div>
                            <label class="block text-[10px] font-black text-gray-600 uppercase mb-1">Ordenar Por</label>
                            <select name="ordenar_por" class="w-full border-2 border-black rounded p-2 text-sm font-bold focus:ring-0 focus:border-purple-600">
                                <option value="preco_unitario" @selected(request('ordenar_por') == 'preco_unitario' || !request('ordenar_por'))>💰 Menor Preço</option>
                                <option value="tempo_entrega_dias" @selected(request('ordenar_por') == 'tempo_entrega_dias')>🚚 Menor Prazo</option>
                            </select>
                        </div>

                        <!-- Direção -->
                        <div>
                            <label class="block text-[10px] font-black text-gray-600 uppercase mb-1">Direção</label>
                            <select name="direcao" class="w-full border-2 border-black rounded p-2 text-sm font-bold focus:ring-0 focus:border-purple-600">
                                <option value="asc" @selected(request('direcao') == 'asc' || !request('direcao'))>Crescente (A-Z / 0-9)</option>
                                <option value="desc" @selected(request('direcao') == 'desc')>Decrescente (Z-A / 9-0)</option>
                            </select>
                        </div>

                        <!-- Botões -->
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-black text-white px-4 py-2.5 rounded border-2 border-transparent hover:border-gray-600 shadow-md font-black uppercase text-xs transition hover:scale-105">
                                Filtrar
                            </button>
                            <a href="{{ route('aluno.compras.confirmar', $compra->id) }}" class="bg-white text-black px-4 py-2.5 rounded border-2 border-black shadow-md font-black uppercase text-xs transition hover:bg-gray-100 flex items-center justify-center">
                                ✕
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- 3. DECISÃO DE COMPRA (FORMULÁRIO PRINCIPAL) -->
            <form action="{{ route('aluno.compras.efetivar', $compra->id) }}" method="POST">
                @csrf
                
                <div class="bg-white border-4 border-black rounded-xl shadow-[10px_10px_0px_0px_rgba(34,197,94,1)] overflow-hidden">
                    <div class="bg-green-100 p-8">
                        <label class="block text-2xl font-black text-green-900 mb-4 uppercase tracking-tight flex items-center gap-2">
                            <span>✅</span> Selecione o Fornecedor Vencedor
                        </label>
                        
                        <div class="relative">
                            <select name="fornecedor_id" class="block w-full border-4 border-black rounded-xl shadow-lg focus:ring-0 focus:border-green-600 p-4 text-lg font-bold bg-white appearance-none cursor-pointer" required size="5">
                                @forelse($fornecedores as $forn)
                                    @php
                                        $custoTotal = $forn->preco_unitario * $compra->quantidade;
                                    @endphp
                                    <option value="{{ $forn->id }}" class="py-2 px-2 border-b border-gray-100 hover:bg-green-50">
                                        {{ str_pad($forn->nome_razao_social, 30) }} 
                                        | Prazo: {{ str_pad($forn->tempo_entrega_dias . ' dias', 10) }} 
                                        | Total: R$ {{ number_format($custoTotal, 2, ',', '.') }}
                                    </option>
                                @empty
                                    <option value="" disabled>⛔ NENHUM FORNECEDOR DISPONÍVEL PARA ESTE ITEM!</option>
                                @endforelse
                            </select>
                            
                            {{-- Aviso se vazio --}}
                            @if($fornecedores->isEmpty())
                                <div class="mt-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 text-sm font-bold">
                                    ERRO: Não existem fornecedores cadastrados para este material ou genéricos. Avise o Professor.
                                </div>
                            @endif
                        </div>
                        
                        <p class="text-xs text-green-800 mt-4 font-bold uppercase tracking-wide">
                            * Selecione um item na lista acima para habilitar o contrato.
                        </p>
                    </div>

                    <div class="p-6 bg-white border-t-4 border-black flex justify-end gap-4">
                        <a href="{{ route('aluno.compras.dashboard') }}" class="px-6 py-3 bg-gray-200 text-gray-700 border-2 border-gray-400 rounded font-black uppercase text-xs hover:bg-gray-300 transition">
                            Cancelar Operação
                        </a>
                        
                        @if($fornecedores->isNotEmpty())
                            <button type="submit" class="px-8 py-3 bg-green-600 text-white border-2 border-black shadow-[4px_4px_0px_0px_black] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] rounded font-black uppercase text-sm transition flex items-center gap-2">
                                <span>✍️</span> Assinar Contrato de Compra
                            </button>
                        @endif
                    </div>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>