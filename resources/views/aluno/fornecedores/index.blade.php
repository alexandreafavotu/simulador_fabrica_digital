<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span class="text-3xl">🚚</span> {{ __('Meus Fornecedores') }}
            </h2>
            
            {{-- BOTÃO VOLTAR PADRÃO INDUSTRIAL --}}
            <button onclick="history.back()" 
               class="bg-white text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                ⬅ Voltar
            </button>
        </div>
    </x-slot>

    <div class="py-12 bg-yellow-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- FORMULÁRIO DE CADASTRO (COM TRAVA DE SEGURANÇA E MODAL) -->
            <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] overflow-hidden"
                 x-data="{ 
                    showConfirm: false, 
                    loading: false,
                    nome: '',
                    preco: '0.00',
                    prazo: '1',
                    materialNome: '',

                    abrirConfirmacao() {
                        if(!this.nome || !this.preco || !this.prazo) {
                            alert('⚠️ Preencha Nome, Preço e Prazo do fornecedor!');
                            return;
                        }

                        // Captura o texto do material selecionado no select
                        const select = document.getElementById('materia_prima_id');
                        this.materialNome = select.options[select.selectedIndex].text;
                        
                        this.showConfirm = true;
                    }
                 }">
                <div class="bg-indigo-600 text-white p-4 border-b-4 border-black flex items-center gap-2">
                    <span class="text-2xl">📝</span>
                    <h3 class="font-black text-lg uppercase tracking-wide">Cadastrar Novo Fornecedor</h3>
                </div>
                
                <div class="p-6">
                    <form id="formFornecedorAluno" action="{{ route('aluno.fornecedores.store') }}" method="POST">
                        @csrf
                        
                        <div class="space-y-6">
                            <!-- 1. Dados Cadastrais e Contato -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-xs font-black text-gray-700 uppercase mb-1">Nome / Razão Social</label>
                                    <input type="text" name="nome_razao_social" x-model="nome" required 
                                           class="w-full border-2 border-black p-2 rounded font-bold text-sm focus:ring-0 focus:border-indigo-600 shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)]">
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-gray-700 uppercase mb-1">CNPJ / CPF</label>
                                    <input type="text" name="cnpj_cpf" 
                                           class="w-full border-2 border-black p-2 rounded font-bold text-sm focus:ring-0 focus:border-indigo-600 shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)] font-mono">
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-gray-700 uppercase mb-1">Telefone</label>
                                    <input type="text" name="telefone" 
                                           class="w-full border-2 border-black p-2 rounded font-bold text-sm focus:ring-0 focus:border-indigo-600 shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)]">
                                </div>
                            </div>

                            <!-- 2. Condições Comerciais -->
                            <div class="bg-blue-50 p-4 rounded-lg border-2 border-black shadow-sm">
                                <h4 class="text-xs font-black text-blue-800 uppercase mb-3 border-b border-blue-200 pb-1 flex items-center gap-2">
                                    <span>💰</span> Dados Comerciais
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-[10px] font-black text-blue-700 uppercase mb-1">Material Fornecido</label>
                                        <select name="materia_prima_id" id="materia_prima_id" class="w-full border-2 border-black rounded text-sm font-bold focus:ring-0">
                                            <option value="">(Genérico - Vários)</option>
                                            @foreach($materiasPrimas as $mp)
                                                <option value="{{ $mp->id }}">{{ $mp->nome }} ({{ $mp->unidade_medida }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-blue-700 uppercase mb-1">Preço Unitário (R$)</label>
                                        <input type="number" step="0.01" name="preco_unitario" x-model="preco" required 
                                               class="w-full border-2 border-black p-2 rounded text-sm font-bold focus:ring-0">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-blue-700 uppercase mb-1">Prazo Entrega (Dias)</label>
                                        <input type="number" name="tempo_entrega_dias" x-model="prazo" min="0" required 
                                               class="w-full border-2 border-black p-2 rounded text-sm font-bold focus:ring-0">
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Endereço -->
                            <div class="bg-gray-50 p-4 rounded-lg border-2 border-dashed border-gray-400">
                                <h4 class="text-xs font-black text-gray-600 uppercase mb-3 flex items-center gap-2">
                                    <span>📍</span> Endereço
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase">CEP</label>
                                        <input type="text" name="cep" class="w-full border-2 border-black rounded text-sm font-bold p-2">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase">Rua</label>
                                        <input type="text" name="rua" class="w-full border-2 border-black rounded text-sm font-bold p-2">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase">Número</label>
                                        <input type="text" name="numero" class="w-full border-2 border-black rounded text-sm font-bold p-2">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase">Bairro</label>
                                        <input type="text" name="bairro" class="w-full border-2 border-black rounded text-sm font-bold p-2">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase">Cidade</label>
                                        <input type="text" name="cidade" class="w-full border-2 border-black rounded text-sm font-bold p-2">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase">Estado (UF)</label>
                                        <input type="text" name="estado" maxlength="2" class="w-full border-2 border-black rounded text-sm font-bold p-2 text-center uppercase">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-8">
                            <button type="button" @click="abrirConfirmacao()" class="bg-purple-600 hover:bg-purple-700 text-white font-black py-3 px-8 rounded border-2 border-black shadow-[4px_4px_0px_0px_black] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none transition uppercase text-xs flex items-center gap-2">
                                <span>💾</span> Salvar Fornecedor
                            </button>
                        </div>
                    </form>
                </div>

                {{-- MODAL DE CONFIRMAÇÃO DE FORNECEDOR (ALUNO) --}}
                <div x-show="showConfirm" 
                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     style="display: none;">
                    
                    <div class="bg-white border-4 border-black p-8 max-w-sm w-full shadow-[15px_15px_0px_0px_rgba(0,0,0,1)]">
                        <div class="text-center mb-6">
                            <span class="text-5xl">🚛</span>
                            <h4 class="text-2xl font-black uppercase mt-4 tracking-tighter">Homologar Fornecedor?</h4>
                            <p class="text-[10px] font-bold text-gray-400 uppercase italic">Revise as condições de compra</p>
                        </div>

                        <div class="bg-blue-50 border-2 border-black p-4 mb-6 space-y-3 text-left">
                            <div>
                                <p class="text-[9px] font-black text-blue-400 uppercase leading-none">Fornecedor:</p>
                                <p class="text-sm font-black uppercase text-blue-900" x-text="nome"></p>
                            </div>
                            
                            <div class="pt-2 border-t border-blue-200">
                                <p class="text-[9px] font-black text-gray-400 uppercase leading-none">Especialidade / Material:</p>
                                <p class="text-xs font-bold text-black uppercase" x-text="materialNome"></p>
                            </div>

                            <div class="grid grid-cols-2 gap-4 pt-2 border-t border-blue-200">
                                <div>
                                    <p class="text-[9px] font-black text-gray-400 uppercase leading-none">Preço Unitário:</p>
                                    <p class="font-black text-green-600 text-sm" x-text="'R$ ' + parseFloat(preco).toLocaleString('pt-BR', {minimumFractionDigits: 2})"></p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-gray-400 uppercase leading-none">Prazo de Entrega:</p>
                                    <p class="font-black text-gray-800 text-sm" x-text="prazo + ' dia(s)'"></p>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-blue-200 text-center">
                                <p class="text-[9px] font-black text-gray-400 uppercase leading-none">Empresa Destino:</p>
                                <p class="text-[11px] font-black text-indigo-600 uppercase">{{ $aluno->turma->nome_empresa }}</p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button @click="showConfirm = false" :disabled="loading" class="flex-1 bg-white border-2 border-black py-2 font-black uppercase text-xs hover:bg-gray-100 transition">
                                Revisar
                            </button>
                            <button @click="loading = true; document.getElementById('formFornecedorAluno').submit();" 
                                    :disabled="loading"
                                    class="flex-1 bg-purple-600 text-white border-2 border-black py-2 font-black uppercase text-xs shadow-[3px_3px_0px_0px_black] hover:shadow-none transition disabled:bg-gray-400">
                                <span x-show="!loading">Confirmar</span>
                                <span x-show="loading" class="flex items-center justify-center gap-1">
                                    <svg class="animate-spin h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Salvando
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LISTAGEM (TABELA INDUSTRIAL) -->
            <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,0.2)] overflow-hidden">
                <div class="p-4 bg-gray-100 border-b-4 border-black">
                    <h3 class="font-black text-xl uppercase tracking-tighter text-black">Fornecedores da Minha Empresa</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-black text-white uppercase text-xs">
                            <tr>
                                <th class="py-3 px-4 text-left">Fornecedor</th>
                                <th class="py-3 px-4 text-left">Especialidade</th>
                                <th class="py-3 px-4 text-center">Preço</th>
                                <th class="py-3 px-4 text-center">Prazo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-gray-100">
                            @forelse($fornecedores as $fornecedor)
                                <tr class="hover:bg-yellow-50 transition font-medium text-gray-700">
                                    <td class="py-3 px-4 font-black text-gray-800 uppercase">{{ $fornecedor->nome_razao_social }}</td>
                                    <td class="py-3 px-4 text-blue-600 font-bold text-xs">
                                        {{ $fornecedor->materiaPrima->nome ?? 'Genérico' }}
                                    </td>
                                    <td class="py-3 px-4 text-center font-bold text-gray-800">
                                        R$ {{ number_format($fornecedor->preco_unitario, 2, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="bg-gray-100 px-2 py-1 rounded border border-gray-300 text-xs font-bold">{{ $fornecedor->tempo_entrega_dias }} dias</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-12 text-center text-gray-400 italic font-black uppercase tracking-widest bg-gray-50">
                                        Nenhum fornecedor cadastrado.
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