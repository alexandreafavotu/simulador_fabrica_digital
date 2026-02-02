<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span>✏️</span> EDITAR FORNECEDOR: <span class="text-indigo-600 uppercase">{{ $fornecedor->nome_razao_social }}</span>
            </h2>
            <a href="{{ route('professor.fornecedores.index') }}" 
               class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                ⬅ Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-yellow-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            {{-- CARD PRINCIPAL --}}
            <div class="bg-white border-4 border-black rounded-xl shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                <form action="{{ route('professor.fornecedores.update', $fornecedor->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="p-8 space-y-8">
                        
                        {{-- SEÇÃO 1: VÍNCULO E IDENTIFICAÇÃO --}}
                        <div class="space-y-4">
                            <h4 class="font-black text-sm text-indigo-600 uppercase border-b-2 border-indigo-100 pb-1 flex items-center gap-2">
                                <span>🆔</span> 1. Identificação e Vínculo
                            </h4>
                            
                            <div class="bg-indigo-50 p-4 rounded-lg border-2 border-dashed border-indigo-200 mb-4">
                                <label class="block text-[10px] font-black text-indigo-700 uppercase mb-1">Empresa Detentora (Dono do Registro)</label>
                                <select name="turma_id" class="w-full border-2 border-black rounded text-sm font-bold focus:ring-0 focus:border-indigo-600 bg-white">
                                    <option value="" {{ is_null($fornecedor->turma_id) ? 'selected' : '' }}>-- Modelo Global (Público) --</option>
                                    @foreach($turmas as $turma)
                                        <option value="{{ $turma->id }}" {{ $fornecedor->turma_id == $turma->id ? 'selected' : '' }}>
                                            {{ $turma->nome_empresa }} ({{ $turma->nome }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-700 uppercase">Razão Social / Nome</label>
                                    <input type="text" name="nome_razao_social" value="{{ $fornecedor->nome_razao_social }}" required class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-700 uppercase">CNPJ / CPF</label>
                                    <input type="text" name="cnpj_cpf" value="{{ $fornecedor->cnpj_cpf }}" class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0 font-mono">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-700 uppercase">Telefone</label>
                                    <input type="text" name="telefone" value="{{ $fornecedor->telefone }}" class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0">
                                </div>
                            </div>
                        </div>

                        {{-- SEÇÃO 2: CONDIÇÕES COMERCIAIS --}}
                        <div class="space-y-4">
                            <h4 class="font-black text-sm text-blue-600 uppercase border-b-2 border-blue-100 pb-1 flex items-center gap-2">
                                <span>💰</span> 2. Condições Comerciais
                            </h4>
                            <div class="bg-blue-50 border-2 border-black p-6 rounded-xl grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-[10px] font-black text-blue-800 uppercase mb-1">Material Fornecido</label>
                                    <select name="materia_prima_id" class="w-full border-2 border-black rounded text-sm font-bold bg-white">
                                        <option value="">(Fornecimento Geral)</option>
                                        @foreach($materiasPrimas as $mp)
                                            <option value="{{ $mp->id }}" {{ $fornecedor->materia_prima_id == $mp->id ? 'selected' : '' }}>
                                                {{ $mp->nome }} ({{ $mp->unidade_medida }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-blue-800 uppercase mb-1">Preço Unitário (R$)</label>
                                    <input type="number" step="0.01" name="preco_unitario" value="{{ $fornecedor->preco_unitario }}" class="w-full border-2 border-black p-2 rounded font-bold">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-blue-800 uppercase mb-1">Lead Time (Dias)</label>
                                    <input type="number" name="tempo_entrega_dias" value="{{ $fornecedor->tempo_entrega_dias }}" class="w-full border-2 border-black p-2 rounded font-bold">
                                </div>
                            </div>
                        </div>

                        {{-- SEÇÃO 3: LOCALIZAÇÃO --}}
                        <div class="space-y-4">
                            <h4 class="font-black text-sm text-gray-700 uppercase border-b-2 border-gray-100 pb-1 flex items-center gap-2">
                                <span>📍</span> 3. Localização Sede
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-700 uppercase">CEP</label>
                                    <input type="text" name="cep" value="{{ $fornecedor->cep }}" class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-700 uppercase">Rua / Logradouro</label>
                                    <input type="text" name="rua" value="{{ $fornecedor->rua }}" class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-700 uppercase">Número</label>
                                    <input type="text" name="numero" value="{{ $fornecedor->numero }}" class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-700 uppercase">Bairro</label>
                                    <input type="text" name="bairro" value="{{ $fornecedor->bairro }}" class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0">
                                </div>
                                <div class="md:col-span-1">
                                    <label class="block text-[10px] font-black text-gray-700 uppercase">Cidade</label>
                                    <input type="text" name="cidade" value="{{ $fornecedor->cidade }}" class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0">
                                </div>
                                <div class="md:col-span-1">
                                    <label class="block text-[10px] font-black text-gray-700 uppercase">UF</label>
                                    <input type="text" name="estado" maxlength="2" value="{{ $fornecedor->estado }}" class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] text-center font-bold uppercase focus:ring-0">
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- RODAPÉ DE AÇÕES --}}
                    <div class="p-6 border-t-4 border-black bg-gray-50 flex justify-end gap-3">
                        <a href="{{ route('professor.fornecedores.index') }}" 
                           class="px-6 py-2 text-gray-600 font-bold hover:bg-gray-200 rounded uppercase text-xs flex items-center transition">
                           Cancelar
                        </a>
                        <button type="submit" 
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-black py-3 px-10 rounded border-2 border-black shadow-[4px_4px_0px_0px_black] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none transition uppercase text-sm">
                            💾 Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>