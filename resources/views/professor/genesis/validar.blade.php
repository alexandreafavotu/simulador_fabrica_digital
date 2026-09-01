<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center no-print">
            <h2 class="font-black text-2xl uppercase tracking-tighter">✨ Gênesis: Validação Técnica de Dados</h2>
            <a href="{{ route('professor.dashboard') }}" class="bg-white border-4 border-black px-4 py-2 font-black uppercase text-xs shadow-[4px_4px_0px_0px_black]">⬅ Cancelar</a>
        </div>
    </x-slot>

    <div class="py-8 bg-yellow-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4">
            <form action="{{ route('professor.genesis.salvar') }}" method="POST">
                @csrf
                <input type="hidden" name="turma_id" value="{{ $turma->id }}">

                <div class="space-y-12">
                    
                    {{-- 1. MATERIAIS --}}
                    <div class="bg-white border-4 border-black shadow-[8px_8px_0px_0px_black] rounded-xl overflow-hidden">
                        <div class="bg-black text-white p-4 font-black uppercase text-xl">1. Matérias-Primas</div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                            @foreach($sugestao['materiais'] ?? [] as $idx => $m)
                                <div class="border-2 border-black p-4 bg-gray-50 space-y-4">
                                    <div><label class="block text-[10px] font-black uppercase text-gray-500 mb-1">SKU Código</label>
                                    <input type="text" name="materiais[{{$idx}}][sku]" value="{{$m['sku']}}" class="w-full border-2 border-black p-2 text-sm font-bold uppercase"></div>
                                    <div><label class="block text-[10px] font-black uppercase text-gray-500 mb-1">Nome do Insumo</label>
                                    <input type="text" name="materiais[{{$idx}}][nome]" value="{{$m['nome']}}" class="w-full border-2 border-black p-2 text-sm font-black uppercase"></div>
                                    <div><label class="block text-[10px] font-black uppercase text-gray-500 mb-1">Unidade</label>
                                    <input type="text" name="materiais[{{$idx}}][unidade]" value="{{$m['unidade']}}" class="w-full border-2 border-black p-2 text-sm font-bold"></div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- 2. PRODUTOS --}}
                    <div class="bg-white border-4 border-black shadow-[8px_8px_0px_0px_black] rounded-xl overflow-hidden">
                        <div class="bg-indigo-600 text-white p-4 font-black uppercase text-xl">2. Produtos Acabados e BOM</div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                            @foreach($sugestao['produtos'] ?? [] as $idx => $p)
                                <div class="border-4 border-black p-5 bg-white shadow-[6px_6px_0px_0px_rgba(79,70,229,1)] space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div><label class="block text-[10px] font-black uppercase text-gray-500 mb-1">SKU Código Interno</label>
                                        <input type="text" name="produtos[{{$idx}}][sku]" value="{{$p['sku']}}" class="w-full border-2 border-black p-2 text-sm font-bold uppercase"></div>
                                        <div><label class="block text-[10px] font-black uppercase text-gray-500 mb-1">Nome do Produto</label>
                                        <input type="text" name="produtos[{{$idx}}][nome]" value="{{$p['nome']}}" class="w-full border-2 border-black p-2 text-sm font-black uppercase"></div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div><label class="block text-[10px] font-black uppercase text-gray-500 mb-1">Preço de Venda</label>
                                        <input type="number" step="0.01" name="produtos[{{$idx}}][preco]" value="{{$p['preco_venda']}}" class="w-full border-2 border-black p-2 text-sm font-bold"></div>
                                        <div><label class="block text-[10px] font-black uppercase text-gray-500 mb-1">Tempo de Produção (Dias)</label>
                                        <input type="number" name="produtos[{{$idx}}][tempo]" value="{{$p['tempo_producao']}}" class="w-full border-2 border-black p-2 text-sm font-bold"></div>
                                    </div>
                                    <div class="bg-yellow-50 border-2 border-black p-3 rounded">
                                        <p class="text-xs font-black uppercase text-indigo-700 mb-2 italic">📋 Receita (Quantidade Inteira):</p>
                                        @foreach($p['receita'] ?? [] as $rIdx => $r)
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-xs font-bold">{{ $r['material_sku'] }}</span>
                                                <div class="flex items-center gap-2">
                                                    <label class="text-[9px] font-black uppercase">Qtd:</label>
                                                    <input type="number" step="1" name="produtos[{{$idx}}][receita][{{$rIdx}}][quantidade]" value="{{ (int)$r['quantidade'] }}" class="w-16 border-2 border-black p-1 text-xs font-black text-center">
                                                </div>
                                                <input type="hidden" name="produtos[{{$idx}}][receita][{{$rIdx}}][material_sku]" value="{{ $r['material_sku'] }}">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- 3. FORNECEDORES --}}
                    <div class="bg-white border-4 border-black shadow-[8px_8px_0px_0px_black] rounded-xl overflow-hidden">
                        <div class="bg-green-600 text-white p-4 font-black uppercase text-xl">3. Mercado de Fornecedores</div>
                        <div class="p-6 space-y-10">
                            @foreach($sugestao['fornecedores'] ?? [] as $idx => $f)
                                <div class="border-4 border-black p-6 bg-green-50 grid grid-cols-1 md:grid-cols-4 gap-6">
                                    <div class="md:col-span-2">
                                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-1">Nome Razão Social</label>
                                        <input type="text" name="fornecedores[{{$idx}}][nome]" value="{{$f['nome_razao_social'] ?? $f['nome']}}" class="w-full border-2 border-black p-2 text-sm font-black uppercase">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-1">CNPJ/CPF</label>
                                        <input type="text" name="fornecedores[{{$idx}}][cnpj]" value="{{$f['cnpj']}}" class="w-full border-2 border-black p-2 text-sm font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-1">Telefone</label>
                                        <input type="text" name="fornecedores[{{$idx}}][telefone]" value="{{$f['telefone']}}" class="w-full border-2 border-black p-2 text-sm font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-1">Material SKU</label>
                                        <input type="text" name="fornecedores[{{$idx}}][material_sku]" value="{{$f['material_sku']}}" class="w-full border-2 border-black p-2 text-sm font-black bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-1">Preço Unitário</label>
                                        <input type="number" step="0.01" name="fornecedores[{{$idx}}][preco]" value="{{$f['preco_unitario'] ?? $f['preco']}}" class="w-full border-2 border-black p-2 text-sm font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-1">Prazo de Entrega Dias</label>
                                        <input type="number" name="fornecedores[{{$idx}}][prazo]" value="{{$f['prazo_entrega'] ?? $f['prazo']}}" class="w-full border-2 border-black p-2 text-sm font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-1">CEP</label>
                                        <input type="text" name="fornecedores[{{$idx}}][cep]" value="{{$f['cep']}}" class="w-full border-2 border-black p-2 text-sm font-bold">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-1">Rua</label>
                                        <input type="text" name="fornecedores[{{$idx}}][rua]" value="{{$f['rua']}}" class="w-full border-2 border-black p-2 text-sm font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-1">Número</label>
                                        <input type="text" name="fornecedores[{{$idx}}][numero]" value="{{$f['numero']}}" class="w-full border-2 border-black p-2 text-sm font-bold text-center">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-1">Bairro</label>
                                        <input type="text" name="fornecedores[{{$idx}}][bairro]" value="{{$f['bairro']}}" class="w-full border-2 border-black p-2 text-sm font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-1">Cidade</label>
                                        <input type="text" name="fornecedores[{{$idx}}][cidade]" value="{{$f['cidade']}}" class="w-full border-2 border-black p-2 text-sm font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-1">Estado UF</label>
                                        <input type="text" name="fornecedores[{{$idx}}][uf]" value="{{$f['uf']}}" class="w-full border-2 border-black p-2 text-sm font-bold text-center">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- 4. CLIENTES --}}
                    <div class="bg-white border-4 border-black shadow-[8px_8px_0px_0px_black] rounded-xl overflow-hidden">
                        <div class="bg-orange-500 text-white p-4 font-black uppercase text-xl">4. Carteira de Clientes</div>
                        <div class="p-6 space-y-10">
                            @foreach($sugestao['clientes'] ?? [] as $idx => $c)
                                <div class="border-4 border-black p-6 bg-orange-50 grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="md:col-span-2">
                                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-1">Razão Social</label>
                                        <input type="text" name="clientes[{{$idx}}][nome]" value="{{$c['razao_social'] ?? $c['nome']}}" class="w-full border-2 border-black p-2 text-sm font-black uppercase">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-1">CNPJ/CPF</label>
                                        <input type="text" name="clientes[{{$idx}}][cnpj]" value="{{$c['cnpj']}}" class="w-full border-2 border-black p-2 text-sm font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-1">Telefone</label>
                                        <input type="text" name="clientes[{{$idx}}][telefone]" value="{{$c['telefone']}}" class="w-full border-2 border-black p-2 text-sm font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-1">CEP</label>
                                        <input type="text" name="clientes[{{$idx}}][cep]" value="{{$c['cep']}}" class="w-full border-2 border-black p-2 text-sm font-bold">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-1">Rua</label>
                                        <input type="text" name="clientes[{{$idx}}][rua]" value="{{$c['rua']}}" class="w-full border-2 border-black p-2 text-sm font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-1">Número</label>
                                        <input type="text" name="clientes[{{$idx}}][numero]" value="{{$c['numero']}}" class="w-full border-2 border-black p-2 text-sm font-bold text-center">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-1">Bairro</label>
                                        <input type="text" name="clientes[{{$idx}}][bairro]" value="{{$c['bairro']}}" class="w-full border-2 border-black p-2 text-sm font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-1">Cidade</label>
                                        <input type="text" name="clientes[{{$idx}}][cidade]" value="{{$c['cidade']}}" class="w-full border-2 border-black p-2 text-sm font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-1">Estado UF</label>
                                        <input type="text" name="clientes[{{$idx}}][uf]" value="{{$c['uf']}}" class="w-full border-2 border-black p-2 text-sm font-bold text-center">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-12 mb-20 flex flex-col md:flex-row gap-8 no-print">
                    <button type="submit" class="flex-1 bg-green-500 border-4 border-black p-8 font-black uppercase text-2xl shadow-[10px_10px_0px_0px_black] hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all">🚀 SALVAR AUTOMÁTICO</button>
                    <button type="button" onclick="window.print()" class="flex-1 bg-white border-4 border-black p-8 font-black uppercase text-2xl shadow-[10px_10px_0px_0px_black] hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all">🖨️ IMPRIMIR PARA MANUAL</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>