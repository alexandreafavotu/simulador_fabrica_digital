<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span class="text-3xl">📄</span> {{ __('Detalhes da Venda') }} <span class="text-gray-400">#{{ $pedido->id }}</span>
            </h2>
            <a href="{{ route('professor.pedidos-venda.index') }}" 
               class="bg-gray-800 text-white px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                ⬅ Voltar à Lista
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-yellow-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            {{-- CARD PRINCIPAL (FICHA TÉCNICA) --}}
            <div class="bg-white border-4 border-black rounded-xl shadow-[10px_10px_0px_0px_black] overflow-hidden">
                
                {{-- CABEÇALHO DO DOCUMENTO --}}
                <div class="bg-indigo-600 text-white p-6 border-b-4 border-black flex justify-between items-center">
                    <div>
                        <h3 class="font-black text-2xl uppercase tracking-tighter">Ficha de Pedido</h3>
                        <p class="text-xs font-bold text-indigo-200 uppercase tracking-widest">Documento Oficial de Venda</p>
                    </div>
                    <button onclick="window.print()" class="bg-white text-indigo-900 border-2 border-black px-4 py-2 rounded font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,0.5)] hover:shadow-none hover:translate-y-[1px] transition flex items-center gap-2">
                        🖨️ Imprimir
                    </button>
                </div>

                <div class="p-8 space-y-8">
                    
                    {{-- DADOS MESTRES (GRID 3 COLUNAS - Mantendo seus dados originais) --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pb-6 border-b-2 border-dashed border-gray-300">
                        
                        {{-- COLUNA 1: CLIENTE --}}
                        <div class="bg-gray-50 p-4 border-2 border-black rounded shadow-sm">
                            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Cliente / Parceiro</p>
                            <p class="text-lg font-black text-gray-800 leading-tight uppercase">{{ $pedido->cliente->nome_razao_social }}</p>
                            <p class="text-xs font-bold text-indigo-600 mt-1 font-mono">CNPJ/CPF: {{ $pedido->cliente->cnpj_cpf }}</p>
                            <p class="text-xs font-bold text-gray-500 uppercase mt-1">{{ $pedido->cliente->cidade }} - {{ $pedido->cliente->estado }}</p>
                        </div>

                        {{-- COLUNA 2: DATAS --}}
                        <div class="bg-gray-50 p-4 border-2 border-black rounded shadow-sm">
                            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Cronograma</p>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs font-bold text-gray-600 uppercase">Emissão:</span>
                                <span class="font-mono font-black text-sm">{{ \Carbon\Carbon::parse($pedido->data_pedido)->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-gray-600 uppercase">Entrega Solicitada:</span>
                                <span class="font-mono font-black text-sm text-red-600">{{ \Carbon\Carbon::parse($pedido->data_entrega_solicitada)->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        {{-- COLUNA 3: VALORES E STATUS --}}
                        <div class="bg-gray-50 p-4 border-2 border-black rounded shadow-sm flex flex-col justify-between">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Total do Pedido</p>
                                    <p class="text-2xl font-black text-green-600">R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}</p>
                                </div>
                            </div>
                            <div class="mt-2">
                                <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Status Atual</p>
                                <span class="bg-black text-white px-3 py-1 rounded text-xs font-black uppercase tracking-wide inline-block text-center">
                                    {{ $pedido->status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- TABELA DE ITENS (Mantendo seu loop original) --}}
                    <div>
                        <h4 class="font-black text-lg text-gray-800 uppercase mb-4 flex items-center gap-2">
                            <span>📦</span> Itens Inclusos no Pedido
                        </h4>
                        <div class="overflow-hidden border-2 border-black rounded-lg">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-black text-white uppercase text-xs font-black tracking-wider">
                                    <tr>
                                        <th class="px-6 py-3 text-left">SKU</th>
                                        <th class="px-6 py-3 text-left">Produto</th>
                                        <th class="px-6 py-3 text-center">Qtd.</th>
                                        <th class="px-6 py-3 text-right">Preço Unit.</th>
                                        <th class="px-6 py-3 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y-2 divide-gray-200 bg-white">
                                    @foreach ($pedido->itens as $item)
                                        <tr class="hover:bg-yellow-50 transition font-medium">
                                            <td class="px-6 py-4 font-mono font-bold text-gray-500">{{ $item->produto->sku }}</td>
                                            <td class="px-6 py-4 font-black text-gray-800 uppercase">{{ $item->produto->nome }}</td>
                                            <td class="px-6 py-4 text-center font-bold text-lg">{{ $item->quantidade }}</td>
                                            <td class="px-6 py-4 text-right font-mono text-gray-600">R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</td>
                                            <td class="px-6 py-4 text-right font-mono font-black text-indigo-700">R$ {{ number_format($item->preco_total_item, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>