<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                @if(Auth::user()->acessibilidade_visual)
    <svg class="w-8 h-8 inline" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="2" style="stroke:#FFFF00!important;">
        <path d="M18 11V6a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v0"></path>
        <path d="M14 10V4a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v0"></path>
        <path d="M10 10.5V6a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v0"></path>
        <path d="M18 8a2 2 0 1 1 4 0v6a8 8 0 0 1-8 8h-2c-2.8 0-4.5-.86-5.99-2.34l-3.6-3.6a2 2 0 0 1 2.83-2.82L7 15"></path>
    </svg>
@else
    <span class="text-3xl">🤝</span>
@endif {{ __('Meus Clientes') }}
            </h2>
            
            {{-- BOTÃO VOLTAR PADRÃO INDUSTRIAL --}}
            <button onclick="history.back()" 
               class="bg-white text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                @if(Auth::user()->acessibilidade_visual)
    <svg class="w-4 h-4 inline" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="4" style="stroke:#FFFF00!important;">
        <path d="M19 12H5M12 19l-7-7 7-7"></path>
    </svg>
@else
    ⬅
@endif 
Voltar
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
                    nome: '',
                    documento: '',
                    cidade: '',
                    uf: '',

                    abrirConfirmacao() {
                        if(!this.nome) {
                            alert('⚠️ O Nome/Razão Social é obrigatório!');
                            return;
                        }
                        this.showConfirm = true;
                    }
                 }">
                
                <div class="bg-indigo-600 text-white p-4 border-b-4 border-black flex items-center gap-2">
                    @if(Auth::user()->acessibilidade_visual)
    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="2" style="stroke:#FFFF00!important;">
        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
        <path d="M12 11h4M12 16h4M8 11h.01M8 16h.01"></path>
    </svg>
@else
    <span class="text-2xl">📝</span>
@endif
                    <h3 class="font-black text-lg uppercase tracking-wide">Cadastrar Novo Cliente</h3>
                </div>
                
                <div class="p-6">
                    @if ($errors->any())
                        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-3 text-xs font-bold">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>⚠️ {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="formClienteAluno" action="{{ route('aluno.clientes.store') }}" method="POST">
                        @csrf
                        
                        <div class="space-y-6">
                            <!-- Dados Cadastrais -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-xs font-black text-gray-700 uppercase mb-1">Nome / Razão Social</label>
                                    <input type="text" name="nome_razao_social" x-model="nome" required 
                                           class="w-full border-2 border-black p-2 rounded font-bold text-sm focus:ring-0 focus:border-indigo-600 shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)]">
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-gray-700 uppercase mb-1">CNPJ / CPF</label>
                                    <input type="text" name="cnpj_cpf" x-model="documento"
                                           class="w-full border-2 border-black p-2 rounded font-bold text-sm focus:ring-0 focus:border-indigo-600 shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)] font-mono">
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-gray-700 uppercase mb-1">Telefone</label>
                                    <input type="text" name="telefone" 
                                           class="w-full border-2 border-black p-2 rounded font-bold text-sm focus:ring-0 focus:border-indigo-600 shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)]">
                                </div>
                            </div>

                            <!-- Endereço -->
                            <div class="bg-gray-50 p-4 rounded-lg border-2 border-dashed border-gray-400">
                                <h4 class="text-xs font-black text-gray-500 uppercase mb-3 flex items-center gap-1">
                                    @if(Auth::user()->acessibilidade_visual)
    <svg class="w-4 h-4 inline" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="2" style="stroke:#FFFF00!important;">
        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
        <circle cx="12" cy="10" r="3"></circle>
    </svg>
@else
    <span>📍</span>
@endif Localização
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div class="md:col-span-1">
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase">CEP</label>
                                        <input type="text" name="cep" class="w-full border-2 border-black rounded text-sm focus:ring-0">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase">Rua</label>
                                        <input type="text" name="rua" class="w-full border-2 border-black rounded text-sm focus:ring-0">
                                    </div>
                                    <div class="md:col-span-1">
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase">Número</label>
                                        <input type="text" name="numero" class="w-full border-2 border-black rounded text-sm focus:ring-0">
                                    </div>
                                    
                                    <div class="md:col-span-1">
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase">Bairro</label>
                                        <input type="text" name="bairro" class="w-full border-2 border-black rounded text-sm focus:ring-0">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase">Cidade</label>
                                        <input type="text" name="cidade" x-model="cidade" class="w-full border-2 border-black rounded text-sm focus:ring-0">
                                    </div>
                                    <div class="md:col-span-1">
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase">UF</label>
                                        <input type="text" name="estado" x-model="uf" maxlength="2" class="w-full border-2 border-black rounded text-sm text-center uppercase font-black focus:ring-0">
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end pt-2">
                                {{-- Botão modificado para disparar o Modal --}}
                                <button type="button" @click="abrirConfirmacao()" class="bg-indigo-600 text-white font-black py-3 px-8 rounded border-2 border-black shadow-[4px_4px_0px_0px_black] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none transition uppercase text-xs flex items-center gap-2">
                                    @if(Auth::user()->acessibilidade_visual)
    <svg class="w-4 h-4 inline" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="2" style="stroke:#FFFF00!important;">
        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
        <polyline points="17 21 17 13 7 13 7 21"></polyline>
        <polyline points="7 3 7 8 15 8"></polyline>
    </svg>
@else
    <span>💾</span>
@endif Cadastrar Cliente
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- MODAL DE CONFIRMAÇÃO --}}
                <div x-show="showConfirm" 
                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     style="display: none;">
                    
                    <div class="bg-white border-4 border-black p-8 max-w-sm w-full shadow-[15px_15px_0px_0px_rgba(0,0,0,1)]">
                        <div class="text-center mb-6">
                            <span class="text-5xl">🤝</span>
                            <h4 class="text-2xl font-black uppercase mt-4 tracking-tighter">Validar Cliente?</h4>
                            <p class="text-[10px] font-bold text-gray-400 uppercase italic">O cliente entrará na carteira da sua empresa</p>
                        </div>

                        <div class="bg-indigo-50 border-2 border-black p-4 mb-6 space-y-3 text-left">
                            <div>
                                <p class="text-[9px] font-black text-indigo-400 uppercase leading-none">Razão Social:</p>
                                <p class="text-sm font-black uppercase text-indigo-900" x-text="nome"></p>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 pt-2 border-t border-indigo-100">
                                <div>
                                    <p class="text-[9px] font-black text-gray-400 uppercase leading-none">Documento:</p>
                                    <p class="font-mono font-bold text-xs" x-text="documento || 'Não informado'"></p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-gray-400 uppercase leading-none">Região:</p>
                                    <p class="font-bold text-xs uppercase" x-text="(cidade + ' / ' + uf) || 'Não informada'"></p>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-indigo-100">
                                <p class="text-[9px] font-black text-gray-400 uppercase leading-none text-center">Empresa Destino:</p>
                                <p class="text-[11px] font-black text-center text-green-600 uppercase">{{ $aluno->turma->nome_empresa }}</p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button @click="showConfirm = false" :disabled="loading" 
                                    class="flex-1 bg-white border-2 border-black py-2 font-black uppercase text-xs hover:bg-gray-100 transition">
                                Revisar
                            </button>
                            <button @click="loading = true; document.getElementById('formClienteAluno').submit();" 
                                    :disabled="loading"
                                    class="flex-1 bg-indigo-600 text-white border-2 border-black py-2 font-black uppercase text-xs shadow-[3px_3px_0px_0px_black] hover:shadow-none transition disabled:bg-gray-400">
                                <span x-show="!loading">Confirmar</span>
                                <span x-show="loading">Salvando...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LISTAGEM (TABELA INDUSTRIAL) -->
            <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,0.2)] overflow-hidden">
                <div class="p-4 bg-gray-100 border-b-4 border-black">
                    <h3 class="font-black text-xl uppercase tracking-tighter text-black">Clientes da Minha Empresa</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-black text-white uppercase text-xs" style="{{ Auth::user()->acessibilidade_visual ? 'border-bottom: 2px solid #FFFF00 !important;' : '' }}">
                            <tr>
                                <th class="py-3 px-4 text-left">Nome / Razão</th>
                                <th class="py-3 px-4 text-left">Documento</th>
                                <th class="py-3 px-4 text-left">Localização</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-gray-100">
                            @forelse($clientes as $cliente)
                                <tr class="hover:bg-yellow-50 transition font-medium text-gray-700">
                                    <td class="py-3 px-4 font-black text-gray-800 uppercase">{{ $cliente->nome_razao_social }}</td>
                                    <td class="py-3 px-4 font-mono text-indigo-600 font-bold">{{ $cliente->cnpj_cpf ?? '-' }}</td>
                                    <td class="py-3 px-4 font-bold text-gray-500 uppercase text-xs">
                                        {{ $cliente->cidade ?? '' }} <span class="text-gray-300">|</span> {{ $cliente->estado ?? '' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-12 text-center text-gray-400 italic font-black uppercase tracking-widest bg-gray-50">
                                        Nenhum cliente cadastrado.
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