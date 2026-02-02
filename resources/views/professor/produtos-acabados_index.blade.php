<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span>🧸</span> {{ __('Gerenciar Produtos Acabados') }}
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

            <!-- 1. FORMULÁRIO DE CADASTRO (COM TRAVA DE SEGURANÇA E MODAL) -->
            <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] overflow-hidden"
                 x-data="{ 
                    showConfirm: false, 
                    loading: false,
                    sku: '',
                    nome: '',
                    preco: '0.00',
                    tempo: '1',
                    empresaNome: '',

                    abrirConfirmacao() {
                        if(!this.sku || !this.nome || !this.preco || !this.tempo) {
                            alert('⚠️ Preencha todos os campos técnicos do produto!');
                            return;
                        }

                        // Identifica o nome da empresa selecionada no dropdown
                        const selectTurma = document.getElementById('turma_id');
                        this.empresaNome = selectTurma.options[selectTurma.selectedIndex].text;
                        
                        this.showConfirm = true;
                    }
                 }">
                
                <div class="bg-indigo-600 text-white p-4 border-b-4 border-black flex items-center gap-2">
                    <span class="text-2xl">📝</span>
                    <h3 class="font-black text-lg uppercase tracking-wide">Cadastrar Novo Produto</h3>
                </div>
                
                <div class="p-6">
                    <form id="formProdutoProfessor" method="POST" action="{{ route('professor.produtos-acabados.store') }}">
                        @csrf
                        
                        <!-- SELETOR DE TURMA (ISOLAMENTO) -->
                        <div class="mb-6 bg-indigo-50 p-4 rounded-lg border-2 border-dashed border-indigo-300">
                            <label for="turma_id" class="block text-xs font-black text-indigo-700 uppercase mb-1">Empresa Detentora do Produto:</label>
                            <select name="turma_id" id="turma_id" class="w-full border-2 border-black rounded text-sm font-bold focus:ring-0 focus:border-indigo-600">
                                <option value="">-- Modelo Global (Disponível para Todas as Fábricas) --</option>
                                @foreach($turmas as $turma)
                                    <option value="{{ $turma->id }}">{{ $turma->nome_empresa }} ({{ $turma->nome }})</option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-indigo-400 mt-1 font-bold italic text-center uppercase">Modelos globais servem de base para a economia do simulador.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <!-- SKU -->
                            <div>
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">SKU (Código Interno)</label>
                                <input type="text" name="sku" x-model="sku" required placeholder="Ex: PRD-001" class="w-full border-2 border-black rounded text-sm font-bold p-2 focus:ring-0">
                            </div>

                            <!-- Nome do Produto -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">Nome do Produto Acabado</label>
                                <input type="text" name="nome" x-model="nome" required placeholder="Ex: Skate Profissional" class="w-full border-2 border-black rounded text-sm font-bold p-2 focus:ring-0">
                            </div>
                            
                            <!-- Preço -->
                            <div>
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">Preço de Venda (R$)</label>
                                <input type="number" name="preco_venda" x-model="preco" required step="0.01" min="0" class="w-full border-2 border-black rounded text-sm font-bold p-2 focus:ring-0">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 mt-6">
                            <!-- Tempo de Produção -->
                            <div>
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">Tempo de Produção (Dias)</label>
                                <input type="number" name="tempo_producao_dias" x-model="tempo" required min="1" class="w-full border-2 border-black rounded text-sm font-bold p-2 focus:ring-0">
                            </div>
                        </div>

                        <div class="flex justify-end mt-8">
                            {{-- Botão de Disparo do Modal --}}
                            <button type="button" @click="abrirConfirmacao()" class="bg-green-600 hover:bg-green-700 text-white font-black py-3 px-10 rounded border-2 border-black shadow-[4px_4px_0px_0px_black] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none transition uppercase text-sm flex items-center gap-2">
                                💾 Registrar Produto
                            </button>
                        </div>
                    </form>
                </div>

                {{-- MODAL DE CONFIRMAÇÃO DE ENGENHARIA --}}
                <div x-show="showConfirm" 
                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     style="display: none;">
                    
                    <div class="bg-white border-4 border-black p-8 max-w-sm w-full shadow-[15px_15px_0px_0px_rgba(0,0,0,1)]">
                        <div class="text-center mb-6">
                            <span class="text-5xl">🧸</span>
                            <h4 class="text-2xl font-black uppercase mt-4 tracking-tighter">Validar Produto?</h4>
                            <p class="text-[10px] font-bold text-gray-400 uppercase italic">Confirmar entrada no catálogo industrial</p>
                        </div>

                        <div class="bg-indigo-50 border-2 border-black p-4 mb-6 space-y-3 text-left">
                            <div>
                                <p class="text-[9px] font-black text-indigo-400 uppercase leading-none">Empresa Vinculada:</p>
                                <p class="text-xs font-black uppercase text-indigo-900" x-text="empresaNome"></p>
                            </div>
                            
                            <div class="pt-2 border-t border-indigo-200">
                                <p class="text-[9px] font-black text-gray-400 uppercase leading-none">Descrição:</p>
                                <p class="text-sm font-black uppercase text-black" x-text="nome"></p>
                                <p class="font-mono text-xs text-gray-500" x-text="'SKU: ' + sku"></p>
                            </div>

                            <div class="grid grid-cols-2 gap-4 pt-2 border-t border-indigo-200">
                                <div>
                                    <p class="text-[9px] font-black text-gray-400 uppercase leading-none">Preço Venda:</p>
                                    <p class="font-black text-green-600 text-sm" x-text="'R$ ' + parseFloat(preco).toLocaleString('pt-BR', {minimumFractionDigits: 2})"></p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-gray-400 uppercase leading-none">Lead Time:</p>
                                    <p class="font-black text-gray-800 text-sm" x-text="tempo + ' dia(s)'"></p>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button @click="showConfirm = false" :disabled="loading" class="flex-1 bg-white border-2 border-black py-2 font-black uppercase text-xs hover:bg-gray-100 transition disabled:opacity-50">
                                Revisar
                            </button>
                            <button @click="loading = true; document.getElementById('formProdutoProfessor').submit();" 
                                    :disabled="loading"
                                    class="flex-1 bg-green-500 text-white border-2 border-black py-2 font-black uppercase text-xs shadow-[3px_3px_0px_0px_black] hover:shadow-none transition disabled:bg-gray-400">
                                <span x-show="!loading">Confirmar</span>
                                <span x-show="loading" class="flex items-center justify-center gap-1">
                                    <svg class="animate-spin h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Registrando
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. TABELA DE LISTAGEM (INDUSTRIAL) -->
            <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,0.2)] overflow-hidden">
                <div class="p-4 bg-yellow-400 border-b-4 border-black flex flex-col md:flex-row justify-between items-center gap-4">
                    <h3 class="font-black text-xl uppercase tracking-tighter">Produtos Cadastrados</h3>
                    
                    {{-- Filtro de Turma --}}
                    <form method="GET" action="{{ route('professor.produtos-acabados.index') }}" class="flex gap-2">
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
                            <a href="{{ route('professor.produtos-acabados.index') }}" class="bg-white text-red-600 border-2 border-black px-2 py-1 rounded font-black text-xs hover:bg-red-50 flex items-center shadow-[2px_2px_0px_0px_black]">X</a>
                        @endif
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-black text-white uppercase text-xs">
                            <tr>
                                <th class="px-6 py-3 text-left">Empresa</th>
                                <th class="px-6 py-3 text-left">SKU</th>
                                <th class="px-6 py-3 text-left">Produto</th>
                                <th class="px-6 py-3 text-right">Preço</th>
                                <th class="px-6 py-3 text-center">Tempo</th>
                                <th class="px-6 py-3 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($produtosAcabados as $produto)
                                <tr class="hover:bg-yellow-50 transition font-medium">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($produto->turma)
                                            <span class="px-2 py-1 border-2 border-indigo-200 bg-indigo-50 text-indigo-700 rounded text-[10px] font-black uppercase">
                                                {{ $produto->turma->nome_empresa }}
                                            </span>
                                        @else
                                            <span class="px-2 py-1 border-2 border-gray-200 bg-gray-50 text-gray-500 rounded text-[10px] font-black uppercase">
                                                🌐 GLOBAL
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-mono text-xs font-bold text-gray-500">
                                        {{ $produto->sku }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-black text-gray-800">
                                        {{ $produto->nome }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-bold text-green-600">
                                        R$ {{ number_format($produto->preco_venda, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="bg-gray-100 px-2 py-1 rounded border border-gray-300 text-xs">
                                            {{ $produto->tempo_producao_dias }} dias
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-center gap-2">
                                            {{-- BOTÃO EDITAR 3D --}}
                                            <a href="{{ route('professor.produtos-acabados.edit', $produto) }}" 
                                               class="bg-white hover:bg-blue-50 text-blue-600 border-2 border-black px-3 py-1 rounded text-[10px] font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)] active:shadow-none active:translate-y-[1px] transition">
                                                Editar
                                            </a>
                                            
                                            {{-- BOTÃO EXCLUIR COM SENHA MESTRA --}}
                                            <x-modal-senha-mestra 
                                                id="del-prod-{{ $produto->id }}" 
                                                action="{{ route('professor.produtos-acabados.destroy', $produto->id) }}" 
                                                method="DELETE"
                                                class="bg-red-50 hover:bg-red-100 text-red-600 border-2 border-black px-3 py-1 rounded text-[10px] font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)] active:shadow-none active:translate-y-[1px] transition"
                                                title="EXCLUIR PRODUTO?"
                                                mensagem="Mover '{{ $produto->nome }}' para a lixeira? A composição (receita) será preservada no histórico.">
                                                Excluir
                                            </x-modal-senha-mestra>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic bg-gray-50 font-bold">
                                        Nenhum produto acabado localizado com este filtro.
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