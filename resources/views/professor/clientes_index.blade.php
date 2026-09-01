<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span>🤝</span> {{ __('Gerenciar Carteira de Clientes') }}
            </h2>
            <a href="{{ route('professor.dashboard') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                ⬅ Voltar ao Painel
            </a>
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

            <!-- 1. FORMULÁRIO DE CADASTRO (COM TRAVA DE SEGURANÇA) -->
            <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] overflow-hidden"
                 x-data="{ 
                    showConfirm: false, 
                    loading: false,
                    nome: '',
                    documento: '',
                    cidade: '',
                    estado: '',
                    empresaNome: '',

                    abrirConfirmacao() {
                        if(!this.nome) {
                            alert('⚠️ O Nome/Razão Social é obrigatório para o cadastro.');
                            return;
                        }

                        // Busca o nome da empresa selecionada no dropdown
                        const selectTurma = document.getElementById('turma_id');
                        this.empresaNome = selectTurma.options[selectTurma.selectedIndex].text;
                        
                        this.showConfirm = true;
                    }
                 }">
                
                <div class="bg-indigo-600 text-white p-4 border-b-4 border-black flex items-center gap-2">
                    <span class="text-2xl">📝</span>
                    <h3 class="font-black text-lg uppercase tracking-wide">Cadastrar Novo Cliente</h3>
                </div>
                
                <div class="p-6">
                    <form id="formClienteProfessor" method="POST" action="{{ route('professor.clientes.store') }}">
                        @csrf
                        
                        <!-- SELETOR DE TURMA (ISOLAMENTO) -->
                        <div class="mb-6 bg-indigo-50 p-4 rounded-lg border-2 border-dashed border-indigo-300">
                            <label for="turma_id" class="block text-xs font-black text-indigo-700 uppercase mb-1">Pertence à Empresa:</label>
                            <select name="turma_id" id="turma_id" class="w-full border-2 border-black rounded text-sm font-bold focus:ring-0 focus:border-indigo-600">
                                <option value="">-- Modelo Global (Disponível para Todos) --</option>
                                @foreach($turmas as $turma)
                                    <option value="{{ $turma->id }}">{{ $turma->nome_empresa }} ({{ $turma->nome }})</option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-indigo-400 mt-1 font-bold">Selecione uma empresa específica ou deixe global.</p>
                        </div>

                        <div class="space-y-4">
                            <!-- Linha 1: Dados Básicos -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-black text-gray-700 uppercase">Nome / Razão Social</label>
                                    <input type="text" name="nome_razao_social" x-model="nome" required class="w-full border-2 border-black rounded text-sm font-bold p-2 focus:ring-0">
                                </div>
                                {{-- 1. CAMPO DINÂMICO DE CNPJ/CPF INTEGRADO AO ALPINE.JS (X-MODEL) --}}
                                <div>
                                    <label class="block text-xs font-black text-gray-700 uppercase">CNPJ / CPF</label>
                                    <input type="text" name="cnpj_cpf" x-model="documento" maxlength="18"
                                           x-on:input="documento = documento.replace(/\D/g, '').slice(0, 14); if (documento.length <= 11) { documento = documento.replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d{1,2})$/, '$1-$2'); } else { documento = documento.replace(/^(\d{2})(\d)/, '$1.$2').replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3').replace(/\.(\d{3})(\d)/, '.$1/$2').replace(/(\d{4})(\d)/, '$1-$2'); }"
                                           class="w-full border-2 border-black rounded text-sm font-bold p-2 focus:ring-0 font-mono bg-white">
                                </div>

                                {{-- 2. CAMPO TELEFONE COM MÁSCARA DINÂMICA (CELULAR OU FIXO) --}}
                                <div>
                                    <label class="block text-xs font-black text-gray-700 uppercase">Telefone</label>
                                    <input type="text" name="telefone" placeholder="(11) 99999-9999" maxlength="15"
                                           oninput="let v = this.value.replace(/\D/g, '').slice(0, 11); if (v.length > 10) { this.value = v.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3'); } else if (v.length > 5) { this.value = v.replace(/^(\d{2})(\d{4})(\d{0,4})$/, '($1) $2-$3'); } else if (v.length > 2) { this.value = v.replace(/^(\d{2})(\d{0,5})$/, '($1) $2'); } else { this.value = v; }"
                                           class="w-full border-2 border-black rounded text-sm font-bold p-2 focus:ring-0 bg-white">
                                </div>
                            </div>

                            <!-- Linha 2: Endereço Parte 1 -->
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                {{-- CAMPO CEP COM MÁSCARA AUTOMÁTICA EM TEMPO REAL --}}
                                <div>
                                    <label class="block text-xs font-black text-gray-700 uppercase">CEP</label>
                                    <input type="text" name="cep" placeholder="00000-000" maxlength="9"
                                           oninput="this.value = this.value.replace(/\D/g, '').slice(0, 8).replace(/^(\d{5})(\d)/, '$1-$2')"
                                           class="w-full border-2 border-black rounded text-sm font-bold p-2 focus:ring-0 bg-white">
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-xs font-black text-gray-700 uppercase">Rua</label>
                                    <input type="text" name="rua" class="w-full border-2 border-black rounded text-sm font-bold p-2 focus:ring-0">
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-gray-700 uppercase">Nº</label>
                                    <input type="text" name="numero" class="w-full border-2 border-black rounded text-sm font-bold p-2 focus:ring-0">
                                </div>
                            </div>

                            <!-- Linha 3: Endereço Parte 2 -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-black text-gray-700 uppercase">Bairro</label>
                                    <input type="text" name="bairro" class="w-full border-2 border-black rounded text-sm font-bold p-2 focus:ring-0">
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-gray-700 uppercase">Cidade</label>
                                    <input type="text" name="cidade" x-model="cidade" class="w-full border-2 border-black rounded text-sm font-bold p-2 focus:ring-0">
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-gray-700 uppercase">Estado (UF)</label>
                                    <input type="text" name="estado" x-model="estado" maxlength="2" placeholder="SP" class="w-full border-2 border-black rounded text-sm font-bold p-2 text-center uppercase focus:ring-0">
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-end mt-8">
                            {{-- Botão que abre a confirmação --}}
                            <button type="button" @click="abrirConfirmacao()" class="bg-green-600 hover:bg-green-700 text-white font-black py-3 px-10 rounded border-2 border-black shadow-[4px_4px_0px_0px_black] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none transition uppercase text-sm flex items-center gap-2">
                                <span>💾</span> Cadastrar Cliente
                            </button>
                        </div>
                    </form>
                </div>

                {{-- MODAL DE CONFIRMAÇÃO DO PROFESSOR --}}
                <div x-show="showConfirm" 
                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     style="display: none;">
                    
                    <div class="bg-white border-4 border-black p-8 max-w-sm w-full shadow-[15px_15px_0px_0px_rgba(0,0,0,1)]">
                        <div class="text-center mb-6">
                            <span class="text-5xl">📡</span>
                            <h4 class="text-2xl font-black uppercase mt-4 tracking-tighter">Validar Cadastro?</h4>
                            <p class="text-[10px] font-bold text-gray-400 uppercase italic text-center">Visão Master do Professor</p>
                        </div>

                        <div class="bg-indigo-50 border-2 border-black p-4 mb-6 space-y-3">
                            <div>
                                <p class="text-[9px] font-black text-indigo-400 uppercase leading-none">Dono do Registro:</p>
                                <p class="text-xs font-black uppercase text-indigo-900" x-text="empresaNome"></p>
                            </div>
                            
                            <div class="pt-2 border-t border-indigo-200">
                                <p class="text-[9px] font-black text-gray-400 uppercase leading-none">Razão Social:</p>
                                <p class="text-sm font-black uppercase text-black" x-text="nome"></p>
                            </div>

                            <div class="grid grid-cols-2 gap-4 pt-2 border-t border-indigo-200">
                                <div>
                                    <p class="text-[9px] font-black text-gray-400 uppercase leading-none">Documento:</p>
                                    <p class="font-mono font-bold text-xs" x-text="documento || '---'"></p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-gray-400 uppercase leading-none">Cidade/UF:</p>
                                    <p class="font-bold text-xs uppercase" x-text="(cidade + ' / ' + estado) || '---'"></p>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button @click="showConfirm = false" :disabled="loading" class="flex-1 bg-white border-2 border-black py-2 font-black uppercase text-xs hover:bg-gray-100 transition">
                                Ajustar
                            </button>
                            <button @click="loading = true; document.getElementById('formClienteProfessor').submit();" 
                                    :disabled="loading"
                                    class="flex-1 bg-green-500 text-white border-2 border-black py-2 font-black uppercase text-xs shadow-[3px_3px_0px_0px_black] hover:shadow-none transition disabled:bg-gray-400">
                                <span x-show="!loading">Confirmar</span>
                                <span x-show="loading">Gravando...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. TABELA DE LISTAGEM (INDUSTRIAL) -->
            <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,0.2)] overflow-hidden">
                <div class="p-4 bg-yellow-400 border-b-4 border-black flex flex-col md:flex-row justify-between items-center gap-4">
                    <h3 class="font-black text-xl uppercase tracking-tighter">Clientes Cadastrados</h3>
                    
                    {{-- Filtro de Turma --}}
                    <form method="GET" action="{{ route('professor.clientes.index') }}" class="flex gap-2">
                        <select name="filtro_turma" class="text-xs font-bold rounded border-2 border-black shadow-[2px_2px_0px_0px_black] focus:ring-0" onchange="this.form.submit()">
                            <option value="">Todas as Empresas</option>
                            <option value="global" {{ request('filtro_turma') == 'global' ? 'selected' : '' }}>Modelos Globais</option>
                            @foreach($turmas as $turma)
                                <option value="{{ $turma->id }}" {{ request('filtro_turma') == $turma->id ? 'selected' : '' }}>
                                    {{ $turma->nome_empresa }}
                                </option>
                            @endforeach
                        </select>
                        @if(request('filtro_turma'))
                            <a href="{{ route('professor.clientes.index') }}" class="bg-white text-red-600 border-2 border-black px-2 py-1 rounded font-black text-xs hover:bg-red-50 flex items-center shadow-[2px_2px_0px_0px_black]">X</a>
                        @endif
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-black text-white uppercase text-xs">
                            <tr>
                                <th class="px-6 py-3 text-left">Empresa (Dono)</th>
                                <th class="px-6 py-3 text-left">Nome / Razão</th>
                                <th class="px-6 py-3 text-left">CNPJ / CPF</th>
                                <th class="px-6 py-3 text-left">Cidade</th>
                                <th class="px-6 py-3 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($clientes as $cliente)
                                <tr class="hover:bg-yellow-50 transition">
                                    <td class="px-6 py-4">
                                        @if($cliente->turma)
                                            <span class="px-2 py-1 border-2 border-indigo-200 bg-indigo-50 text-indigo-700 rounded text-[10px] font-black uppercase">
                                                {{ $cliente->turma->nome_empresa }}
                                            </span>
                                        @else
                                            <span class="px-2 py-1 border-2 border-gray-200 bg-gray-50 text-gray-500 rounded text-[10px] font-black uppercase">
                                                🌐 GLOBAL
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 font-bold text-gray-800">{{ $cliente->nome_razao_social }}</td>
                                    <td class="px-6 py-4 font-mono text-xs">{{ $cliente->cnpj_cpf ?? '---' }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-600">{{ $cliente->cidade ?? '---' }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-center gap-2">
                                            {{-- BOTÃO EDITAR --}}
                                            <a href="{{ route('professor.clientes.edit', $cliente) }}" class="bg-white hover:bg-blue-50 text-blue-600 border-2 border-black px-3 py-1 rounded text-[10px] font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)] active:shadow-none active:translate-y-[1px] transition">
                                                Editar
                                            </a>
                                            
                                            {{-- BOTÃO EXCLUIR COM SENHA MESTRA --}}
                                            <x-modal-senha-mestra 
                                                id="del-cli-{{ $cliente->id }}" 
                                                action="{{ route('professor.clientes.destroy', $cliente->id) }}" 
                                                method="DELETE"
                                                class="bg-red-50 hover:bg-red-100 text-red-600 border-2 border-black px-3 py-1 rounded text-[10px] font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)] active:shadow-none active:translate-y-[1px] transition"
                                                title="EXCLUIR CLIENTE?"
                                                mensagem="Remover '{{ $cliente->nome_razao_social }}'? Ele será movido para a lixeira.">
                                                Excluir
                                            </x-modal-senha-mestra>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic bg-gray-50 font-bold">
                                        Nenhum cliente cadastrado neste filtro.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>