<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span>✏️</span> EDITAR INSUMO: <span class="text-indigo-600 uppercase">{{ $materiaPrima->nome }}</span>
            </h2>
            <button onclick="history.back()" 
               class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                ⬅ Voltar
            </button>
        </div>
    </x-slot>

    <div class="py-12 bg-yellow-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            {{-- CARD PRINCIPAL --}}
            <div class="bg-white border-4 border-black rounded-xl shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                <div class="bg-indigo-600 text-white p-4 border-b-4 border-black flex items-center gap-2">
                    <span class="text-2xl">📋</span>
                    <h3 class="font-black text-lg uppercase tracking-wide">Ficha Técnica da Matéria-Prima</h3>
                </div>

                <form method="POST" action="{{ route('professor.materias-primas.update', $materiaPrima) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- SKU -->
                            <div>
                                <label for="sku" class="block text-[10px] font-black text-gray-700 uppercase mb-1">SKU (Código Interno)</label>
                                <input id="sku" type="text" name="sku" value="{{ old('sku', $materiaPrima->sku) }}" required 
                                       class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0 focus:border-indigo-600 font-bold font-mono">
                            </div>

                            <!-- Nome da Matéria-Prima -->
                            <div>
                                <label for="nome" class="block text-[10px] font-black text-gray-700 uppercase mb-1">Nome do Material</label>
                                <input id="nome" type="text" name="nome" value="{{ old('nome', $materiaPrima->nome) }}" required 
                                       class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0 focus:border-indigo-600 font-bold">
                            </div>

                            <!-- Unidade de Medida -->
                            <div>
                                <label for="unidade_medida" class="block text-[10px] font-black text-gray-700 uppercase mb-1">Unidade de Medida</label>
                                <input id="unidade_medida" type="text" name="unidade_medida" value="{{ old('unidade_medida', $materiaPrima->unidade_medida) }}" required 
                                       placeholder="Ex: kg, un, lt" 
                                       class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0 focus:border-indigo-600 font-bold text-center">
                            </div>
                        </div>

                        {{-- INFO DE CONTEXTO (AVISO) --}}
                        <div class="mt-4 p-4 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg">
                            <p class="text-[10px] text-gray-500 font-bold uppercase">Nota do Sistema:</p>
                            <p class="text-xs text-gray-400">Alterar o SKU ou Nome afetará como este item aparece em todas as Ordens de Compra e Produção ativas que utilizam este insumo.</p>
                        </div>
                    </div>

                    {{-- RODAPÉ DE AÇÕES --}}
                    <div class="p-6 border-t-4 border-black bg-gray-50 flex justify-end gap-3">
                        <a href="{{ route('professor.materias-primas.index') }}" 
                           class="px-6 py-2 text-gray-600 font-bold hover:bg-gray-200 rounded uppercase text-xs flex items-center transition">
                           Cancelar
                        </a>
                        <button type="submit" 
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-black py-3 px-10 rounded border-2 border-black shadow-[4px_4px_0px_0px_black] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none transition uppercase text-sm flex items-center gap-2">
                            💾 Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>