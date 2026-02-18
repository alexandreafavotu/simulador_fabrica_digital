<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                @if(Auth::user()->acessibilidade_visual)
    <svg class="w-8 h-8 inline" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="2" style="filter: none !important; stroke: #ffff00 !important;">
        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
    </svg>
@else
    <span class="text-3xl">📦</span>
@endif {{ __('Cadastro de Matérias-Primas (Aluno)') }}
            </h2>
            
            {{-- BOTÃO VOLTAR PADRÃO INDUSTRIAL --}}
            <button onclick="history.back()" 
               class="bg-white text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                @if(Auth::user()->acessibilidade_visual)
    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="4" style="filter: none !important; stroke: #ffff00 !important;">
        <path d="M19 12H5M12 19l-7-7 7-7"></path>
    </svg>
@else
    ⬅
@endif Voltar
            </button>
        </div>
    </x-slot>

    <div class="py-12 bg-yellow-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

        <!-- FORMULÁRIO DE CADASTRO (COM TRAVA DE CONFIRMAÇÃO) -->
            <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] overflow-hidden"
                 x-data="{ 
                    showConfirm: false, 
                    loading: false,
                    sku: '',
                    nome: '',
                    unidade: '',

                    abrirConfirmacao() {
                        if(!this.sku || !this.nome || !this.unidade) {
                            alert('⚠️ Por favor, preencha todos os campos do material.');
                            return;
                        }
                        this.showConfirm = true;
                    }
                 }">
                
                <div class="bg-indigo-600 text-white p-4 border-b-4 border-black flex items-center gap-2">
                    @if(Auth::user()->acessibilidade_visual)
    <!-- Ícone da Prancheta Amarelo -->
    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="2" style="filter: none !important; stroke: #ffff00 !important;">
        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
    </svg>
@else
    <span class="text-2xl">📝</span>
@endif
                    <h3 class="font-black text-lg uppercase tracking-wide">Cadastrar Novo Material</h3>
                </div>
                
                <div class="p-6">
                    {{-- EXIBIÇÃO DE ERROS --}}
                    @if ($errors->any())
                        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-3 text-xs font-bold">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>⚠️ {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="formMaterialAluno" action="{{ route('aluno.materias-primas.store') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                            
                            <!-- SKU -->
                            <div class="md:col-span-1">
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">SKU (Código)</label>
                                <input type="text" name="sku" x-model="sku" required 
                                       class="w-full border-2 border-black p-2 rounded font-bold text-sm focus:ring-0 focus:border-indigo-600 shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)]">
                            </div>

                            <!-- Nome -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">Nome do Insumo</label>
                                <input type="text" name="nome" x-model="nome" required 
                                       class="w-full border-2 border-black p-2 rounded font-bold text-sm focus:ring-0 focus:border-indigo-600 shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)]">
                            </div>

                            <!-- Unidade e Botão -->
                            <div class="md:col-span-1 flex gap-2">
                                <div class="flex-grow">
                                    <label class="block text-xs font-black text-gray-700 uppercase mb-1">Unidade</label>
                                    <input type="text" name="unidade_medida" x-model="unidade" required placeholder="kg, lt, un" 
       class="w-full border-2 border-black p-2 rounded font-bold text-sm text-center {{ Auth::user()->acessibilidade_visual ? 'placeholder:text-yellow-400' : '' }}" 
       style="{{ Auth::user()->acessibilidade_visual ? 'background-color: #000000 !important; color: #FFFF00 !important; border-color: #FFFF00 !important;' : '' }}">
                                </div>
                                {{-- Botão chama o Modal --}}
                                <button type="button" @click="abrirConfirmacao()" 
                                        class="bg-green-600 text-white font-black px-6 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_black] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none transition uppercase text-xs h-[42px] mt-auto">
                                    Salvar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- MODAL DE CONFIRMAÇÃO (CAIXA SUSPENSA) --}}
                <div x-show="showConfirm" 
                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     style="display: none;">
                    
                    <div class="bg-white border-4 border-black p-8 max-w-sm w-full shadow-[15px_15px_0px_0px_rgba(0,0,0,1)]">
                        <div class="text-center mb-6">
                            <span class="text-5xl">📦</span>
                            <h4 class="text-2xl font-black uppercase mt-4 tracking-tighter">Validar Insumo?</h4>
                            <p class="text-[10px] font-bold text-gray-400 uppercase italic">O material será registrado na sua empresa</p>
                        </div>

                        <div class="bg-yellow-50 border-2 border-black p-4 mb-6 space-y-3">
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase">Descrição do Material:</p>
                                <p class="text-sm font-black uppercase text-indigo-700" x-text="nome"></p>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 pt-2 border-t border-black/10">
                                <div>
                                    <p class="text-[9px] font-black text-gray-400 uppercase">SKU:</p>
                                    <p class="font-mono font-bold text-xs" x-text="sku"></p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-gray-400 uppercase">Medida:</p>
                                    <p class="font-bold text-xs" x-text="unidade"></p>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button @click="showConfirm = false" :disabled="loading" 
                                    class="flex-1 bg-white border-2 border-black py-2 font-black uppercase text-xs hover:bg-gray-100 transition">
                                Editar
                            </button>
                            <button @click="loading = true; document.getElementById('formMaterialAluno').submit();" 
                                    :disabled="loading"
                                    class="flex-1 bg-green-500 text-white border-2 border-black py-2 font-black uppercase text-xs shadow-[4px_4px_0px_0px_black] hover:shadow-none transition disabled:bg-gray-400">
                                <span x-show="!loading">Confirmar</span>
                                <span x-show="loading" class="flex items-center justify-center gap-1">
                                    <svg class="animate-spin h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Gravando
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>    

            <!-- LISTAGEM (TABELA INDUSTRIAL) -->
            <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,0.2)] overflow-hidden">
                <div class="p-4 bg-gray-100 border-b-4 border-black">
                    <h3 class="font-black text-xl uppercase tracking-tighter text-black">Materiais Cadastrados</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-black text-white uppercase text-xs" style="{{ Auth::user()->acessibilidade_visual ? 'border-bottom: 2px solid #FFFF00 !important;' : '' }}">
                            <tr>
                                <th class="py-3 px-4 text-left">SKU</th>
                                <th class="py-3 px-4 text-left">Nome</th>
                                <th class="py-3 px-4 text-center">Unidade</th>
                                {{-- Coluna Ações removida conforme original --}}
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-gray-100">
                            @forelse($materiasPrimas as $mp)
                                <tr class="hover:bg-yellow-50 transition font-medium text-gray-700">
                                    <td class="py-3 px-4 font-mono font-bold text-indigo-600 border-r border-gray-100">{{ $mp->sku }}</td>
                                    <td class="py-3 px-4 font-black text-gray-800 uppercase">{{ $mp->nome }}</td>
                                    <td class="py-3 px-4 text-center border-l border-gray-100">
                                        <span class="bg-gray-200 px-2 py-1 rounded border border-black text-xs font-bold">{{ $mp->unidade_medida }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-12 text-center text-gray-400 italic font-black uppercase tracking-widest bg-gray-50">
                                        Nenhum material cadastrado.
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