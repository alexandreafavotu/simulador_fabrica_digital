<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span>✏️</span> EDITAR PRODUTO: <span class="text-indigo-600 uppercase">{{ $produto->nome }}</span>
            </h2>
            <button onclick="history.back()" 
               class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                ⬅ Voltar
            </button>
        </div>
    </x-slot>

    <div class="py-12 bg-yellow-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- MENSAGENS DE SUCESSO --}}
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-600 text-green-800 p-4 shadow-md font-bold">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <!-- 1. DADOS PRINCIPAIS DO PRODUTO -->
            <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                <div class="bg-indigo-600 text-white p-4 border-b-4 border-black flex items-center gap-2">
                    <span class="text-2xl">📦</span>
                    <h3 class="font-black text-lg uppercase tracking-wide">Dados Principais do Produto</h3>
                </div>

                <div class="p-8">
                    <form method="POST" action="{{ route('professor.produtos-acabados.update', $produto) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <!-- SKU -->
                            <div>
                                <label class="block text-[10px] font-black text-gray-700 uppercase mb-1">SKU (Código Único)</label>
                                <input type="text" name="sku" value="{{ $produto->sku }}" required 
                                       class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0 focus:border-indigo-600 font-bold">
                            </div>
                            <!-- Nome do Produto Acabado -->
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-700 uppercase mb-1">Nome do Produto</label>
                                <input type="text" name="nome" value="{{ $produto->nome }}" required 
                                       class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0 focus:border-indigo-600 font-bold">
                            </div>
                            <!-- Preço de Venda -->
                            <div>
                                <label class="block text-[10px] font-black text-gray-700 uppercase mb-1">Preço de Venda (R$)</label>
                                <input type="number" name="preco_venda" value="{{ $produto->preco_venda }}" required step="0.01" min="0" 
                                       class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0 font-bold text-green-600">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 mt-6">
                            <!-- Tempo de Produção (Dias) -->
                            <div>
                                <label class="block text-[10px] font-black text-gray-700 uppercase mb-1">Tempo de Produção (Dias)</label>
                                <input type="number" name="tempo_producao_dias" value="{{ $produto->tempo_producao_dias }}" required min="1" 
                                       class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0 font-bold">
                            </div>
                        </div>

                        <div class="flex justify-end mt-8">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-black py-3 px-10 rounded border-2 border-black shadow-[4px_4px_0px_0px_black] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none transition uppercase text-sm">
                                💾 Salvar Dados Principais
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 2. ESTRUTURA DO PRODUTO (BOM / RECEITA) -->
            <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                <div class="bg-yellow-400 text-black p-4 border-b-4 border-black flex items-center gap-2">
                    <span class="text-2xl">⚙️</span>
                    <h3 class="font-black text-lg uppercase tracking-wide">Estrutura do Produto (Receita / BOM)</h3>
                </div>

                <div class="p-8 space-y-8">
                    
                    <!-- TABELA DE COMPONENTES ATUAIS -->
                    <div>
                        <h4 class="font-black text-sm text-gray-500 uppercase mb-4 flex items-center gap-2">
                            <span>📋</span> Componentes Cadastrados
                        </h4>

                        <div class="overflow-x-auto border-2 border-black rounded-lg">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-800 text-white uppercase text-[10px] tracking-widest">
                                    <tr>
                                        <th class="px-6 py-3 text-left">SKU</th>
                                        <th class="px-6 py-3 text-left">Matéria-Prima</th>
                                        <th class="px-6 py-3 text-center">Quantidade</th>
                                        <th class="px-6 py-3 text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y-2 divide-gray-100">
                                    @forelse ($produto->materiasPrimas as $materiaPrima)
                                        <tr class="hover:bg-yellow-50 transition">
                                            <td class="px-6 py-4 font-mono font-bold text-gray-500">{{ $materiaPrima->sku }}</td>
                                            <td class="px-6 py-4 font-black text-gray-800 uppercase">{{ $materiaPrima->nome }}</td>
                                            <td class="px-6 py-4 text-center font-bold text-indigo-600 bg-gray-50">
                                                {{ $materiaPrima->pivot->quantidade }} 
                                                <span class="text-[10px] text-gray-400 font-normal uppercase">{{ $materiaPrima->unidade_medida }}</span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <form method="POST" action="{{ route('professor.produtos-acabados.removerComponente', ['produto' => $produto->id, 'materiaPrima' => $materiaPrima->id]) }}" onsubmit="return confirm('Remover este componente da receita?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-100 text-red-600 border-2 border-red-200 px-3 py-1 rounded text-[10px] font-black uppercase hover:bg-red-600 hover:text-white hover:border-black transition">
                                                        Remover
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic font-bold uppercase tracking-widest">
                                                Nenhum componente adicionado à receita.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- FORMULÁRIO PARA ADICIONAR NOVO COMPONENTE -->
                    <div class="bg-gray-50 border-4 border-dashed border-gray-300 p-6 rounded-xl">
                        <h4 class="font-black text-sm text-gray-700 uppercase mb-4 flex items-center gap-2">
                            <span>➕</span> Adicionar Novo Componente à Ficha Técnica
                        </h4>
                        
                        <form method="POST" action="{{ route('professor.produtos-acabados.adicionarComponente', $produto->id) }}">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                                <!-- Seleção de Matéria-Prima -->
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Selecione a Matéria-Prima</label>
                                    <select name="materia_prima_id" required class="w-full border-2 border-black p-2 rounded font-bold text-sm focus:ring-0">
                                        <option value="">-- Selecione o Insumo --</option>
                                        @foreach ($materiasPrimasDisponiveis as $item)
                                            <option value="{{ $item->id }}">{{ $item->nome }} ({{ $item->sku }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Quantidade -->
                                <div>
                                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Quantidade Necessária</label>
                                    <input type="number" name="quantidade" value="1" step="0.01" min="0.01" required 
                                           class="w-full border-2 border-black p-2 rounded font-bold text-sm focus:ring-0">
                                </div>
                            </div>
                            
                            <div class="flex justify-end mt-6">
                                <button type="submit" class="bg-black hover:bg-gray-800 text-white font-black py-2 px-8 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,0.3)] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none transition uppercase text-xs">
                                    Vincular Componente
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>