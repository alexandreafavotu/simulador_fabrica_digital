<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span>✏️</span> EDITAR CLIENTE: <span class="text-indigo-600 uppercase">{{ $cliente->nome_razao_social }}</span>
            </h2>
            <button onclick="history.back()" 
               class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                ⬅ Voltar
            </button>
        </div>
    </x-slot>

    <div class="py-12 bg-yellow-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                <form method="POST" action="{{ route('professor.clientes.update', $cliente) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="p-8 space-y-8">
                        
                        {{-- SEÇÃO 1: IDENTIFICAÇÃO --}}
                        <div class="space-y-4">
                            <h4 class="font-black text-sm text-indigo-600 uppercase border-b-2 border-indigo-100 pb-1 flex items-center gap-2">
                                <span>🆔</span> 1. Identificação do Cliente
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-700 uppercase mb-1">Nome / Razão Social</label>
                                    <input type="text" name="nome_razao_social" value="{{ old('nome_razao_social', $cliente->nome_razao_social) }}" required 
                                           class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0 focus:border-indigo-600 font-bold">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-700 uppercase mb-1">CNPJ / CPF</label>
                                    <input type="text" name="cnpj_cpf" value="{{ old('cnpj_cpf', $cliente->cnpj_cpf) }}" 
                                           class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0 font-mono font-bold">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-700 uppercase mb-1">Telefone</label>
                                    <input type="text" name="telefone" value="{{ old('telefone', $cliente->telefone) }}" 
                                           class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0 font-bold">
                                </div>
                            </div>
                        </div>

                        {{-- SEÇÃO 2: LOCALIZAÇÃO --}}
                        <div class="space-y-4">
                            <h4 class="font-black text-sm text-indigo-600 uppercase border-b-2 border-indigo-100 pb-1 flex items-center gap-2">
                                <span>📍</span> 2. Localização e Sede
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                <div class="md:col-span-1">
                                    <label class="block text-[10px] font-black text-gray-700 uppercase mb-1">CEP</label>
                                    <input type="text" name="cep" value="{{ old('cep', $cliente->cep) }}" 
                                           class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0 font-bold">
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-[10px] font-black text-gray-700 uppercase mb-1">Rua / Logradouro</label>
                                    <input type="text" name="rua" value="{{ old('rua', $cliente->rua) }}" 
                                           class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0 font-bold">
                                </div>
                                <div class="md:col-span-1">
                                    <label class="block text-[10px] font-black text-gray-700 uppercase mb-1">Nº</label>
                                    <input type="text" name="numero" value="{{ old('numero', $cliente->numero) }}" 
                                           class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0 font-bold">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-700 uppercase mb-1">Bairro</label>
                                    <input type="text" name="bairro" value="{{ old('bairro', $cliente->bairro) }}" 
                                           class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0 font-bold">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-700 uppercase mb-1">Cidade</label>
                                    <input type="text" name="cidade" value="{{ old('cidade', $cliente->cidade) }}" 
                                           class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0 font-bold">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-700 uppercase mb-1">Estado (UF)</label>
                                    <input type="text" name="estado" value="{{ old('estado', $cliente->estado) }}" maxlength="2" 
                                           class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] text-center font-black uppercase focus:ring-0">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- RODAPÉ DE AÇÕES --}}
                    <div class="p-6 border-t-4 border-black bg-gray-50 flex justify-end gap-3">
                        <a href="{{ route('professor.clientes.index') }}" 
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