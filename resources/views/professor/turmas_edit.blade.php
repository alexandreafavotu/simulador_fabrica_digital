<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span>✏️</span> EDITAR PERFIL: <span class="text-indigo-600">{{ $turma->nome_empresa }}</span>
            </h2>
            <a href="{{ route('professor.turmas.index') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_black] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase">
                ⬅ Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-yellow-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                <form action="{{ route('professor.turmas.update', $turma->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="p-8 space-y-8">
                        
                        {{-- SEÇÃO 1: IDENTIFICAÇÃO --}}
                        <div class="space-y-4">
                            <h4 class="font-black text-sm text-indigo-600 uppercase border-b-2 border-indigo-100 pb-1 flex items-center gap-2">
                                <span>🆔</span> 1. Identificação da Empresa
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-700 uppercase">Nome da Turma / Classe</label>
                                    <input type="text" name="nome" value="{{ $turma->nome }}" required class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0 focus:border-indigo-600">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-700 uppercase">Nome Fantasia (Simulação)</label>
                                    <input type="text" name="nome_empresa" value="{{ $turma->nome_empresa }}" required class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0 focus:border-indigo-600">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-700 uppercase">Setor de Atuação</label>
                                    <input type="text" name="setor" value="{{ $turma->setor }}" placeholder="Ex: Automobilístico" class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-700 uppercase">Segmento</label>
                                    <input type="text" name="segmento" value="{{ $turma->segmento }}" placeholder="Ex: Peças" class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0">
                                </div>
                            </div>
                        </div>

                        {{-- SEÇÃO 2: DADOS JURÍDICOS --}}
                        <div class="space-y-4">
                            <h4 class="font-black text-sm text-indigo-600 uppercase border-b-2 border-indigo-100 pb-1 flex items-center gap-2">
                                <span>📄</span> 2. Dados Jurídicos e Contato
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-700 uppercase">CNPJ</label>
                                    <input type="text" name="cnpj" value="{{ $turma->cnpj }}" placeholder="00.000.000/0001-00" class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0 font-mono">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-700 uppercase">Telefone</label>
                                    <input type="text" name="telefone" value="{{ $turma->telefone }}" placeholder="(11) 4002-8922" class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-700 uppercase">Resumo da Empresa / Missão</label>
                                    <textarea name="resumo" rows="3" class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0">{{ $turma->resumo }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- SEÇÃO 3: ENDEREÇO --}}
                        <div class="space-y-4">
                            <h4 class="font-black text-sm text-indigo-600 uppercase border-b-2 border-indigo-100 pb-1 flex items-center gap-2">
                                <span>📍</span> 3. Localização da Sede
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="md:col-span-3">
                                    <label class="block text-[10px] font-black text-gray-700 uppercase">Rua / Logradouro</label>
                                    <input type="text" name="rua" value="{{ $turma->rua }}" class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0">
                                </div>
                                <div class="md:col-span-1">
                                    <label class="block text-[10px] font-black text-gray-700 uppercase">Nº</label>
                                    <input type="text" name="numero" value="{{ $turma->numero }}" class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-700 uppercase">Bairro</label>
                                    <input type="text" name="bairro" value="{{ $turma->bairro }}" class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0">
                                </div>
                                <div class="md:col-span-1">
                                    <label class="block text-[10px] font-black text-gray-700 uppercase">Cidade</label>
                                    <input type="text" name="cidade" value="{{ $turma->cidade }}" class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0">
                                </div>
                                <div class="md:col-span-1">
                                    <label class="block text-[10px] font-black text-gray-700 uppercase">UF</label>
                                    <input type="text" name="estado" maxlength="2" value="{{ $turma->estado }}" class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] text-center font-bold focus:ring-0 uppercase">
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Rodapé de Ações --}}
                    <div class="p-6 border-t-4 border-black bg-gray-50 flex justify-end gap-3">
                        <a href="{{ route('professor.turmas.index') }}" class="px-6 py-2 text-gray-600 font-bold hover:bg-gray-200 rounded uppercase text-xs flex items-center">Cancelar</a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-black py-3 px-10 rounded border-2 border-black shadow-[4px_4px_0px_0px_black] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none transition uppercase text-sm">
                            💾 Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>