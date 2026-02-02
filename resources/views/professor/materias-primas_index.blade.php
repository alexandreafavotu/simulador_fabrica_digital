<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span>📦</span> {{ __('Gerenciar Matérias-Primas') }}
            </h2>
            <a href="{{ route('professor.dashboard') }}" 
               class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                ⬅ Voltar ao Painel
            </a>
        </div>
    </x-slot>

    {{-- Lógica para identificar a turma ativa pelo filtro da URL --}}
    @php
        $turmaAtiva = null;
        if(request('filtro_turma') && request('filtro_turma') != 'global') {
            $turmaAtiva = $turmas->firstWhere('id', request('filtro_turma'));
        }
    @endphp

    <div class="py-12 bg-yellow-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- MENSAGENS DE FEEDBACK --}}
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-600 text-green-800 p-4 shadow-md font-bold">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <!-- 1. BLOCO WMS (INTELIGENTE: SÓ APARECE SE TIVER UMA TURMA SELECIONADA NO FILTRO) -->
            @if($turmaAtiva)
                <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,0.1)] p-6 flex items-center justify-between animate-fade-in-down">
                    <div>
                        <h3 class="font-black text-lg text-indigo-900 flex items-center gap-2">
                            <span>🏭</span> Status do Armazém (WMS)
                        </h3>
                        <p class="text-sm text-gray-500 mt-1 uppercase font-bold">
                            Empresa Alvo: <span class="text-indigo-600">{{ $turmaAtiva->nome_empresa }}</span>
                        </p>
                    </div>

                    @if($temMapaWMS)
                        {{-- STATUS OPERANTE (SEM BOTÃO DE DESTRUIÇÃO) --}}
                        <div class="flex items-center gap-3 bg-green-100 border-4 border-green-600 text-green-800 px-6 py-3 rounded-xl shadow-[4px_4px_0px_0px_rgba(22,101,52,1)]">
                            <span class="text-3xl">✅</span>
                            <div>
                                <p class="text-sm font-black uppercase leading-none">Armazém Ativo</p>
                                <p class="text-[10px] font-bold mt-1 uppercase tracking-widest">Estrutura Física Gerada</p>
                            </div>
                        </div>
                    @else
                        {{-- BOTÃO CONSTRUIR (APENAS SE ESTIVER VAZIO) --}}
                        <button onclick="document.getElementById('modal-wms-create').showModal()" 
                                class="bg-indigo-600 text-white border-2 border-black hover:bg-indigo-700 font-black py-3 px-6 rounded-xl transition text-xs uppercase tracking-widest flex items-center gap-2 shadow-[4px_4px_0px_0px_black] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none">
                            ⚙️ Inaugurar Estrutura WMS
                        </button>
                    @endif
                </div>

                {{-- MODAL DE CRIAÇÃO SEGURO --}}
                @if(!$temMapaWMS)
                    <dialog id="modal-wms-create" class="p-0 rounded-xl shadow-2xl backdrop:bg-indigo-900/80 w-[90%] max-w-md open:animate-fade-in border-4 border-black">
                        <div class="bg-white">
                            <div class="bg-indigo-600 text-white p-4 flex items-center justify-between border-b-4 border-black">
                                <h3 class="font-black text-xl flex items-center gap-2 uppercase"><span>🏗️</span> Construir WMS</h3>
                                <button type="button" onclick="document.getElementById('modal-wms-create').close()" class="text-white hover:text-black font-bold text-2xl leading-none">&times;</button>
                            </div>

                            <form action="{{ route('professor.estoque.gerar_mapa') }}" method="POST" class="p-6">
                                @csrf
                                <input type="hidden" name="turma_id" value="{{ $turmaAtiva->id }}">

                                <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-3">
                                    <p class="text-blue-800 font-black text-xs uppercase">Como funciona?</p>
                                    <p class="text-blue-700 text-xs mt-1 font-bold">O sistema criará automaticamente as Ruas, Prateleiras e Níveis para esta empresa. Esta ação é definitiva para o início do jogo.</p>
                                </div>

                                <div class="mb-6">
                                    <label class="block text-xs font-black text-gray-700 uppercase mb-2">Confirme com a Senha Mestra</label>
                                    <input type="password" name="senha_mestra" required placeholder="Digite a Senha" 
                                           class="w-full text-center text-xl tracking-widest border-2 border-black p-3 rounded-lg focus:border-indigo-600 focus:ring-0 outline-none font-black shadow-[3px_3px_0px_0px_rgba(0,0,0,0.1)]">
                                </div>

                                <div class="flex justify-end gap-3 border-t-2 border-gray-100 pt-4">
                                    <button type="button" onclick="document.getElementById('modal-wms-create').close()" class="px-4 py-2 text-gray-500 font-bold hover:bg-gray-100 rounded uppercase text-xs">Cancelar</button>
                                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-black py-3 px-8 rounded border-2 border-black shadow-[4px_4px_0px_0px_black] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none transition uppercase text-sm">
                                        Gerar Mapa
                                    </button>
                                </div>
                            </form>
                        </div>
                    </dialog>
                @endif
            @endif

            <!-- 2. CADASTRO DE MATÉRIA-PRIMA (COM MODAL DE CONFIRMAÇÃO) -->
            <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] overflow-hidden"
                 x-data="{ 
                    showConfirm: false, 
                    loading: false,
                    sku: '',
                    nome: '',
                    unidade: '',
                    empresaNome: '',

                    abrirConfirmacao() {
                        if(!this.sku || !this.nome || !this.unidade) {
                            alert('⚠️ Preencha todos os campos do material!');
                            return;
                        }

                        // Identifica o nome da empresa para o resumo
                        const selectTurma = document.querySelector('select[name=turma_id]');
                        if (selectTurma) {
                            this.empresaNome = selectTurma.options[selectTurma.selectedIndex].text;
                        } else {
                            this.empresaNome = '{{ $turmaAtiva->nome_empresa ?? '' }}';
                        }
                        
                        this.showConfirm = true;
                    }
                 }">
                
                <div class="bg-indigo-600 text-white p-4 border-b-4 border-black flex items-center gap-2">
                    <span class="text-2xl">📝</span>
                    <h3 class="font-black text-lg uppercase tracking-wide">Cadastrar Novo Insumo</h3>
                </div>

                <div class="p-6">
                    <form id="formMateriaPrima" action="{{ route('professor.materias-primas.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                            
                            <!-- SELETOR DE TURMA -->
                            <div>
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">Empresa Detentora</label>
                                @if($turmaAtiva)
                                    <input type="hidden" name="turma_id" value="{{ $turmaAtiva->id }}">
                                    <div class="w-full bg-green-50 border-2 border-green-500 p-2 rounded text-green-800 text-sm font-black truncate uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)]">
                                        {{ $turmaAtiva->nome_empresa }}
                                    </div>
                                @else
                                    <select name="turma_id" class="w-full border-2 border-black rounded text-sm font-bold focus:ring-0 focus:border-indigo-600 shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)]">
                                        <option value="">-- Modelo Global (Todas) --</option>
                                        @foreach($turmas as $turma)
                                            <option value="{{ $turma->id }}">{{ $turma->nome_empresa }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <div>
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">SKU (Código)</label>
                                <input type="text" name="sku" x-model="sku" required placeholder="EX: MP-001" class="w-full border-2 border-black p-2 rounded font-bold text-sm focus:ring-0">
                            </div>
                            
                            <div>
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">Nome do Material</label>
                                <input type="text" name="nome" x-model="nome" required placeholder="Ex: Chapa de Aço" class="w-full border-2 border-black p-2 rounded font-bold text-sm focus:ring-0">
                            </div>

                            <div class="flex gap-2">
                                <div class="flex-grow">
                                    <label class="block text-xs font-black text-gray-700 uppercase mb-1">Unidade</label>
                                    <input type="text" name="unidade_medida" x-model="unidade" required placeholder="kg, un, lt" class="w-full border-2 border-black p-2 rounded font-bold text-sm focus:ring-0 text-center">
                                </div>
                                {{-- Botão que dispara o Modal --}}
                                <button type="button" @click="abrirConfirmacao()" class="bg-green-600 text-white font-black px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_black] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none transition uppercase text-xs h-[42px]">
                                    Ok
                                </button>
                            </div>
                        </div>
                        @if(!$turmaAtiva)
                            <p class="text-[10px] text-gray-400 font-bold mt-2 italic uppercase">Dica: Filtre uma empresa abaixo para travar o cadastro nela.</p>
                        @endif
                    </form>
                </div>

                {{-- MODAL DE CONFIRMAÇÃO DE CADASTRO --}}
                <div x-show="showConfirm" 
                     class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     style="display: none;">
                    
                    <div class="bg-white border-4 border-black p-8 max-w-sm w-full shadow-[15px_15px_0px_0px_rgba(0,0,0,1)]">
                        <div class="text-center mb-6">
                            <span class="text-5xl">📦</span>
                            <h4 class="text-2xl font-black uppercase mt-4 tracking-tighter">Novo Insumo</h4>
                            <p class="text-[10px] font-bold text-gray-400 uppercase italic">Confirme as especificações técnicas</p>
                        </div>

                        <div class="bg-indigo-50 border-2 border-black p-4 mb-6 space-y-2">
                            <p class="text-[10px] font-black uppercase text-indigo-400">Propriedade:</p>
                            <p class="text-sm font-black uppercase leading-tight" x-text="empresaNome || 'Modelo Global'"></p>
                            
                            <div class="grid grid-cols-2 gap-4 mt-3 pt-3 border-t border-indigo-200">
                                <div>
                                    <p class="text-[9px] font-black text-gray-400 uppercase">SKU:</p>
                                    <p class="font-mono font-bold text-sm" x-text="sku"></p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-gray-400 uppercase">Unidade:</p>
                                    <p class="font-bold text-sm" x-text="unidade"></p>
                                </div>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase">Descrição:</p>
                                <p class="font-bold text-sm uppercase" x-text="nome"></p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button @click="showConfirm = false" :disabled="loading" class="flex-1 bg-white border-2 border-black py-2 font-black uppercase text-xs hover:bg-gray-100 transition disabled:opacity-50">
                                Ajustar
                            </button>
                            <button @click="loading = true; document.getElementById('formMateriaPrima').submit();" 
                                    :disabled="loading"
                                    class="flex-1 bg-green-500 text-white border-2 border-black py-2 font-black uppercase text-xs shadow-[3px_3px_0px_0px_black] hover:shadow-none transition disabled:bg-gray-400">
                                <span x-show="!loading">Confirmar</span>
                                <span x-show="loading">Gravando...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. LISTAGEM E FILTROS (ESTILO INDUSTRIAL) -->
            <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,0.2)] overflow-hidden">
                <div class="p-4 bg-yellow-400 border-b-4 border-black flex flex-col md:flex-row justify-between items-center gap-4">
                    <h3 class="font-black text-xl uppercase tracking-tighter text-black">Inventário de Matérias-Primas</h3>
                    
                    <form method="GET" action="{{ route('professor.materias-primas.index') }}" class="flex gap-2 w-full md:w-auto">
                        <select name="filtro_turma" class="text-xs font-black rounded border-2 border-black shadow-[2px_2px_0px_0px_black] focus:ring-0 w-full md:w-64" onchange="this.form.submit()">
                            <option value="">Todas as Empresas</option>
                            <option value="global" {{ request('filtro_turma') == 'global' ? 'selected' : '' }}>Somente Modelos Globais</option>
                            @foreach($turmas as $turma)
                                <option value="{{ $turma->id }}" {{ request('filtro_turma') == $turma->id ? 'selected' : '' }}>
                                    {{ $turma->nome_empresa }}
                                </option>
                            @endforeach
                        </select>
                        @if(request('filtro_turma'))
                            <a href="{{ route('professor.materias-primas.index') }}" class="bg-white text-red-600 border-2 border-black px-3 py-1 rounded font-black text-xs hover:bg-red-50 flex items-center shadow-[2px_2px_0px_0px_black]">X</a>
                        @endif
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-black text-white uppercase text-[10px] tracking-widest">
                            <tr>
                                <th class="py-3 px-4 text-left">Empresa (Dono)</th>
                                <th class="py-3 px-4 text-left">SKU</th>
                                <th class="py-3 px-4 text-left">Nome do Material</th>
                                <th class="py-3 px-4 text-center">Unidade</th>
                                <th class="py-3 px-4 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-gray-100">
                            @forelse($materiasPrimas as $mp)
                                <tr class="hover:bg-yellow-50 transition font-medium text-gray-700">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @if($mp->turma)
                                            <span class="px-2 py-1 border-2 border-indigo-200 bg-indigo-50 text-indigo-700 rounded text-[9px] font-black uppercase">
                                                {{ $mp->turma->nome_empresa }}
                                            </span>
                                        @else
                                            <span class="px-2 py-1 border-2 border-gray-200 bg-gray-50 text-gray-400 rounded text-[9px] font-black uppercase tracking-widest">
                                                GLOBAL
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 font-mono font-bold text-gray-500">{{ $mp->sku }}</td>
                                    <td class="py-3 px-4 font-black text-gray-800 uppercase">{{ $mp->nome }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="bg-gray-100 px-2 py-1 rounded border border-gray-300 text-xs font-bold">{{ $mp->unidade_medida }}</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex justify-center gap-2">
                                            <a href="{{ route('professor.materias-primas.edit', $mp->id) }}" 
                                               class="bg-white hover:bg-blue-50 text-blue-600 border-2 border-black px-3 py-1 rounded text-[10px] font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)] active:shadow-none active:translate-y-[1px] transition">
                                                Editar
                                            </a>

                                            {{-- BOTÃO EXCLUIR COM SENHA MESTRA --}}
                                            <x-modal-senha-mestra 
                                                id="del-mp-{{ $mp->id }}" 
                                                action="{{ route('professor.materias-primas.destroy', $mp->id) }}" 
                                                method="DELETE"
                                                class="bg-red-50 hover:bg-red-100 text-red-600 border-2 border-black px-3 py-1 rounded text-[10px] font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)] active:shadow-none active:translate-y-[1px] transition"
                                                title="EXCLUIR MATERIAL?"
                                                mensagem="Mover '{{ $mp->nome }}' para a lixeira?">
                                                Excluir
                                            </x-modal-senha-mestra>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-16 text-center text-gray-400 italic font-black uppercase tracking-widest bg-gray-50">
                                        Nenhum material encontrado neste filtro.
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