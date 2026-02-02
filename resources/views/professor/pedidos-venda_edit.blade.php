<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span class="text-3xl">✏️</span> {{ __('Editar Pedido') }} <span class="text-gray-400 font-mono text-lg">#{{ $pedido->id }}</span>
            </h2>
            
            <a href="{{ route('professor.pedidos-venda.index') }}" 
               class="bg-gray-800 text-white px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                ⬅ Voltar à Lista
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-yellow-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- 1. CARD DE DADOS MESTRES (CABEÇALHO) --}}
            <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                <div class="bg-indigo-600 text-white p-4 border-b-4 border-black flex justify-between items-center">
                    <div>
                        <h3 class="font-black text-lg uppercase tracking-wide">Dados do Contrato</h3>
                        <p class="text-xs text-indigo-200 font-bold uppercase">Edição de Cabeçalho</p>
                    </div>
                    <div class="bg-white text-indigo-900 px-3 py-1 rounded border-2 border-black font-black text-sm shadow-sm">
                        TOTAL: R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}
                    </div>
                </div>

                <div class="p-6">
                    <form method="POST" action="{{ route('professor.pedidos-venda.update', $pedido) }}">
                        @csrf
                        @method('PUT')
                        
                        @if ($errors->any())
                            <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-3 text-xs font-bold uppercase">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>⚠️ {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <!-- Cliente -->
                            <div>
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">Cliente</label>
                                <select name="cliente_id" id="cliente_id" class="w-full border-2 border-black rounded text-sm font-bold focus:ring-0 focus:border-indigo-600 shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)]">
                                    @foreach ($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" @selected($pedido->cliente_id == $cliente->id)>
                                            {{ $cliente->nome_razao_social }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Turma -->
                            <div>
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">Empresa (Turma)</label>
                                <select name="turma_id" id="turma_id" class="w-full border-2 border-black rounded text-sm font-bold focus:ring-0 focus:border-indigo-600 shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)]">
                                    @foreach ($turmas as $turma)
                                        <option value="{{ $turma->id }}" @selected($pedido->turma_id == $turma->id)>
                                            {{ $turma->nome_empresa }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Data Pedido -->
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase mb-1">Data Emissão (Fixo)</label>
                                <input type="date" value="{{ $pedido->data_pedido }}" readonly 
                                       class="w-full bg-gray-100 border-2 border-gray-300 rounded text-sm font-bold text-gray-500 cursor-not-allowed">
                            </div>
                            <!-- Entrega -->
                            <div>
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">Nova Data de Entrega</label>
                                <input type="date" name="data_entrega_solicitada" value="{{ $pedido->data_entrega_solicitada }}" required 
                                       class="w-full border-2 border-black rounded text-sm font-bold focus:ring-0 focus:border-indigo-600 shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)]">
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-black py-2 px-6 rounded border-2 border-black shadow-[3px_3px_0px_0px_black] hover:shadow-none hover:translate-y-[1px] transition uppercase text-xs flex items-center gap-2">
                                <span>💾</span> Atualizar Cabeçalho
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- 2. CARD DE ITENS (TABELA INDUSTRIAL) --}}
            <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,0.2)] overflow-hidden">
                <div class="p-4 bg-gray-100 border-b-4 border-black flex justify-between items-center">
                    <h3 class="font-black text-lg uppercase tracking-tighter text-black flex items-center gap-2">
                        <span>📦</span> Itens do Pedido <span class="bg-black text-white px-2 rounded text-xs">{{ $pedido->itens->count() }}</span>
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-black text-white uppercase text-[10px] tracking-widest">
                            <tr>
                                <th class="py-3 px-6 text-left">Produto</th>
                                <th class="py-3 px-6 text-center">Quantidade</th>
                                <th class="py-3 px-6 text-right">Unitário</th>
                                <th class="py-3 px-6 text-right">Subtotal</th>
                                <th class="py-3 px-6 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-gray-100">
                            @foreach($pedido->itens as $item)
                                <tr class="hover:bg-yellow-50 transition font-medium text-gray-700" x-data="{ editing: false }">
                                    <td class="py-4 px-6 font-black text-gray-800 uppercase">{{ $item->produto->nome }}</td>
                                    
                                    {{-- Célula de Edição Alpine.js --}}
                                    <td class="py-4 px-6 text-center">
                                        <div x-show="!editing">
                                            <span class="bg-gray-100 border border-gray-300 px-3 py-1 rounded font-mono font-bold text-lg">{{ $item->quantidade }}</span>
                                        </div>
                                        <div x-show="editing" style="display: none;" class="flex justify-center">
                                            <form action="{{ route('professor.pedidos-venda.updateItem', $item->id) }}" method="POST" class="flex gap-1">
                                                @csrf @method('PUT')
                                                <input type="number" name="quantidade" value="{{ $item->quantidade }}" min="1" class="w-16 border-2 border-indigo-500 rounded p-1 text-center font-bold text-xs focus:ring-0">
                                                <button type="submit" class="bg-green-500 text-white p-1 rounded border border-green-700 hover:bg-green-600 shadow-sm" title="Salvar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>

                                    <td class="py-4 px-6 text-right font-mono text-gray-500">R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</td>
                                    <td class="py-4 px-6 text-right font-black text-green-600">R$ {{ number_format($item->preco_total_item, 2, ',', '.') }}</td>
                                    
                                    <td class="py-4 px-6 text-center">
                                        <div class="flex justify-center gap-2">
                                            {{-- Botão Editar --}}
                                            <button @click="editing = !editing" x-show="!editing" class="text-indigo-600 hover:text-indigo-800 bg-indigo-50 border border-indigo-200 px-2 py-1 rounded text-[10px] font-black uppercase transition">
                                                Editar
                                            </button>
                                            <button @click="editing = false" x-show="editing" style="display: none;" class="text-gray-500 hover:text-gray-700 text-[10px] font-bold uppercase underline">
                                                Cancelar
                                            </button>

                                            {{-- Botão Remover --}}
                                            <form action="{{ route('professor.pedidos-venda.removeItem', $item->id) }}" method="POST" onsubmit="return confirm('Remover este item?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 bg-red-50 border border-red-200 px-2 py-1 rounded text-[10px] font-black uppercase transition">
                                                    Remover
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ADICIONAR NOVO ITEM --}}
                <div class="bg-gray-50 p-6 border-t-4 border-black">
                    <h4 class="text-xs font-black text-gray-500 uppercase tracking-widest mb-3 border-b border-gray-300 pb-1">Adicionar Novo Produto</h4>
                    <form method="POST" action="{{ route('professor.pedidos-venda.addItem', $pedido) }}">
                        @csrf
                        <div class="flex gap-4 items-end">
                            <div class="flex-grow">
                                <label class="block text-[10px] font-black text-gray-700 uppercase mb-1">Produto</label>
                                <select name="produto_id" class="w-full border-2 border-black rounded text-sm font-bold focus:ring-0">
                                    @foreach ($produtos as $produto_item)
                                        <option value="{{ $produto_item->id }}">{{ $produto_item->nome }} ({{ $produto_item->sku }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-24">
                                <label class="block text-[10px] font-black text-gray-700 uppercase mb-1">Qtd.</label>
                                <input type="number" name="quantidade" value="1" min="1" class="w-full border-2 border-black rounded text-sm font-bold text-center p-2 focus:ring-0">
                            </div>
                            <button class="bg-green-600 hover:bg-green-700 text-white font-black px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_black] active:shadow-none active:translate-y-[1px] transition uppercase text-xs h-[42px]">
                                + Adicionar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>