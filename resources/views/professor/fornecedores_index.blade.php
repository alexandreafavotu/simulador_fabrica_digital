<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span>🚛</span> {{ __('Gestão de Fornecedores') }}
            </h2>
            <a href="{{ route('professor.dashboard') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_black] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
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

            <!-- 1. FORMULÁRIO DE CADASTRO (COM FILTRO INTELIGENTE E BLINDAGEM) -->
            <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] overflow-hidden"
                 x-data="{ 
                    showConfirm: false, 
                    loading: false,
                    nome: '',
                    documento: '',
                    preco: '0.00',
                    prazo: '1',
                    materialNome: '',
                    empresaNome: '',

                    abrirConfirmacao() {
                        if(!this.nome || !this.preco || !this.prazo) {
                            alert('⚠️ Preencha Nome, Preço e Prazo do fornecedor!');
                            return;
                        }

                        // Captura o nome da Empresa Detentora
                        const selectTurma = document.getElementById('turma_id_fornecedor');
                        this.empresaNome = selectTurma.options[selectTurma.selectedIndex].text;

                        // Captura o nome do Material/Especialidade
                        const selectMaterial = document.getElementById('materia_prima_id');
                        this.materialNome = selectMaterial.options[selectMaterial.selectedIndex].text;
                        
                        this.showConfirm = true;
                    }
                 }">
                <div class="bg-indigo-600 text-white p-4 border-b-4 border-black flex items-center gap-2">
                    <span class="text-2xl">📝</span>
                    <h3 class="font-black text-lg uppercase tracking-wide">Cadastrar Novo Fornecedor</h3>
                </div>
                
                <div class="p-6">
                    <form id="formFornecedor" action="{{ route('professor.fornecedores.store') }}" method="POST">
                        @csrf
                        
                        <!-- SELETOR DE TURMA (Gatilho do Filtro) -->
                        <div class="mb-6 bg-indigo-50 p-4 rounded-lg border-2 border-dashed border-indigo-300">
                            <label class="block text-xs font-black text-indigo-700 uppercase mb-1">Pertence à Empresa:</label>
                            <select name="turma_id" id="turma_id_fornecedor" onchange="filtrarMateriasPorEmpresa(this.value)" class="w-full border-2 border-black rounded text-sm font-bold focus:ring-0 focus:border-indigo-600">
                                <option value="">-- Modelo Global (Disponível para Todos) --</option>
                                @foreach($turmas as $turma)
                                    <option value="{{ $turma->id }}">{{ $turma->nome_empresa }} ({{ $turma->nome }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-6">
                            <!-- Dados Comerciais -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-black text-gray-700 uppercase mb-1">Nome / Razão Social</label>
                                    <input type="text" name="nome_razao_social" x-model="nome" required class="w-full border-2 border-black rounded text-sm font-bold p-2 focus:border-indigo-600">
                                </div>
                                {{-- 1. CAMPO DINÂMICO DE CNPJ/CPF INTEGRADO AO ALPINE.JS (X-MODEL) --}}
                                <div>
                                    <label class="block text-xs font-black text-gray-700 uppercase mb-1">CNPJ / CPF</label>
                                    <input type="text" name="cnpj_cpf" x-model="documento" maxlength="18"
                                           x-on:input="documento = documento.replace(/\D/g, '').slice(0, 14); if (documento.length <= 11) { documento = documento.replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d{1,2})$/, '$1-$2'); } else { documento = documento.replace(/^(\d{2})(\d)/, '$1.$2').replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3').replace(/\.(\d{3})(\d)/, '.$1/$2').replace(/(\d{4})(\d)/, '$1-$2'); }"
                                           class="w-full border-2 border-black rounded text-sm font-bold p-2 font-mono bg-white">
                                </div>

                                {{-- 2. CAMPO TELEFONE COM MÁSCARA DINÂMICA (CELULAR OU FIXO) --}}
                                <div>
                                    <label class="block text-xs font-black text-gray-700 uppercase mb-1">Telefone</label>
                                    <input type="text" name="telefone" placeholder="(11) 99999-9999" maxlength="15"
                                           oninput="let v = this.value.replace(/\D/g, '').slice(0, 11); if (v.length > 10) { this.value = v.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3'); } else if (v.length > 5) { this.value = v.replace(/^(\d{2})(\d{4})(\d{0,4})$/, '($1) $2-$3'); } else if (v.length > 2) { this.value = v.replace(/^(\d{2})(\d{0,5})$/, '($1) $2'); } else { this.value = v; }"
                                           class="w-full border-2 border-black rounded text-sm font-bold p-2 bg-white">
                                </div>
                            </div>

                            <!-- Especialidade e Condições (Filtro Aplicado Aqui) -->
                            <div class="bg-blue-50 p-4 rounded-lg border-2 border-black">
                                <h4 class="text-xs font-black text-blue-700 uppercase mb-3 border-b border-blue-200 pb-1">Especialidade e Condições</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs font-black text-gray-700 uppercase mb-1">Material Fornecido (Filtrado)</label>
                                        <select name="materia_prima_id" id="materia_prima_id" class="select-material w-full border-2 border-black rounded text-sm font-bold">
                                            <option value="">(Genérico - Nenhum Específico)</option>
                                            @foreach($materiasPrimas as $mp)
                                                {{-- data-turma identifica se o material é de uma empresa ou global --}}
                                                <option value="{{ $mp->id }}" data-turma="{{ $mp->turma_id ?? 'global' }}">
                                                    {{ $mp->nome }} ({{ $mp->unidade_medida }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-black text-gray-700 uppercase mb-1">Preço Unitário (R$)</label>
                                        <input type="number" step="0.01" name="preco_unitario" x-model="preco" class="w-full border-2 border-black rounded text-sm font-bold p-2">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-black text-gray-700 uppercase mb-1">Prazo de Entrega (Dias)</label>
                                        <input type="number" name="tempo_entrega_dias" x-model="prazo" min="0" class="w-full border-2 border-black rounded text-sm font-bold p-2">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-8">
                            <button type="button" @click="abrirConfirmacao()" class="bg-gray-800 text-white px-10 py-3 rounded border-2 border-black shadow-[4px_4px_0px_0px_black] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none transition font-black uppercase text-sm flex items-center gap-2">
                                💾 Registrar Fornecedor
                            </button>
                        </div>
                    </form>
                </div>

                {{-- MODAL DE CONFIRMAÇÃO (MANTIDO) --}}
                <div x-show="showConfirm" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" style="display: none;">
                    <div class="bg-white border-4 border-black p-8 max-w-sm w-full shadow-[15px_15px_0px_0px_rgba(0,0,0,1)]">
                        <div class="text-center mb-6">
                            <span class="text-5xl">🚛</span>
                            <h4 class="text-2xl font-black uppercase mt-4 tracking-tighter">Homologar Parceiro?</h4>
                        </div>
                        <div class="bg-blue-50 border-2 border-black p-4 mb-6 space-y-3">
                            <p class="text-[9px] font-black text-blue-400 uppercase leading-none">Contexto:</p>
                            <p class="text-xs font-black uppercase text-blue-900" x-text="empresaNome"></p>
                            <p class="text-[9px] font-black text-gray-400 uppercase leading-none pt-2 border-t border-blue-200">Fornecedor:</p>
                            <p class="text-sm font-black uppercase text-black" x-text="nome"></p>
                            <p class="text-[9px] font-black text-gray-400 uppercase leading-none pt-2 border-t border-blue-200">Especialidade:</p>
                            <p class="font-bold text-blue-600 text-xs uppercase" x-text="materialNome"></p>
                        </div>
                        <div class="flex gap-3">
                            <button @click="showConfirm = false" :disabled="loading" class="flex-1 bg-white border-2 border-black py-2 font-black uppercase text-xs hover:bg-gray-100 transition">Ajustar</button>
                            <button @click="loading = true; document.getElementById('formFornecedor').submit();" :disabled="loading" class="flex-1 bg-green-500 text-white border-2 border-black py-2 font-black uppercase text-xs shadow-[3px_3px_0px_0px_black] hover:shadow-none transition disabled:bg-gray-400">
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
                    <h3 class="font-black text-xl uppercase tracking-tighter text-black">Fornecedores Homologados</h3>
                    
                    {{-- Filtro de Turma --}}
                    <form method="GET" action="{{ route('professor.fornecedores.index') }}" class="flex gap-2">
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
                            <a href="{{ route('professor.fornecedores.index') }}" class="bg-white text-red-600 border-2 border-black px-2 py-1 rounded font-black text-xs hover:bg-red-50 flex items-center shadow-[2px_2px_0px_0px_black]">X</a>
                        @endif
                    </form>
                </div>

                <div class="overflow-x-auto">
                 <table class="min-w-full text-sm">
    <thead class="bg-black text-white uppercase text-xs">
        <tr>
            <th class="py-3 px-4 text-left">Contexto</th>
            <th class="py-3 px-4 text-left">Fornecedor</th>
            <th class="py-3 px-4 text-left">Localização</th>
            <th class="py-3 px-4 text-left">Especialidade</th>
            <th class="py-3 px-4 text-center">Preço</th>
            <th class="py-3 px-4 text-center">Prazo</th> {{-- NOVA COLUNA --}}
            <th class="py-3 px-4 text-center">Ações</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
        @forelse($fornecedores as $fornecedor)
            <tr class="hover:bg-yellow-50 transition font-medium">
                <td class="px-4 py-4">
                    @if($fornecedor->turma)
                        <span class="px-2 py-1 border-2 border-indigo-200 bg-indigo-50 text-indigo-700 rounded text-[9px] font-black uppercase">
                            {{ $fornecedor->turma->nome_empresa }}
                        </span>
                    @else
                        <span class="px-2 py-1 border-2 border-gray-200 bg-gray-50 text-gray-400 rounded text-[9px] font-black uppercase tracking-widest">
                            GLOBAL
                        </span>
                    @endif
                </td>
                <td class="py-2 px-4 font-black text-gray-800">{{ $fornecedor->nome_razao_social }}</td>
                <td class="py-2 px-4 text-xs font-bold text-gray-600 uppercase">
                    {{ $fornecedor->cidade ?? '---' }} - {{ $fornecedor->estado ?? '--' }}
                </td>
                <td class="py-2 px-4 text-blue-600 font-bold">
                    {{ $fornecedor->materiaPrima->nome ?? 'Geral' }}
                </td>
                <td class="py-2 px-4 text-center font-bold text-gray-700">
                    R$ {{ number_format($fornecedor->preco_unitario, 2, ',', '.') }}
                </td>

                {{-- DADO DA NOVA COLUNA: PRAZO --}}
                <td class="py-2 px-4 text-center">
                    <span class="bg-gray-100 px-2 py-1 rounded border border-gray-300 text-[10px] font-black text-gray-600">
                        {{ $fornecedor->tempo_entrega_dias }} DIAS
                    </span>
                </td>

                <td class="py-2 px-4">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('professor.fornecedores.edit', $fornecedor->id) }}" class="bg-white hover:bg-blue-50 text-blue-600 border-2 border-black px-3 py-1 rounded text-[10px] font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)] active:shadow-none active:translate-y-[1px] transition">
                            Editar
                        </a>
                        <x-modal-senha-mestra 
                            id="del-forn-{{ $fornecedor->id }}" 
                            action="{{ route('professor.fornecedores.destroy', $fornecedor->id) }}" 
                            method="DELETE"
                            class="bg-red-50 hover:bg-red-100 text-red-600 border-2 border-black px-3 py-1 rounded text-[10px] font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)] active:shadow-none active:translate-y-[1px] transition"
                            title="EXCLUIR FORNECEDOR?"
                            mensagem="Mover '{{ $fornecedor->nome_razao_social }}' para a lixeira?">
                            Excluir
                        </x-modal-senha-mestra>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="py-12 text-center text-gray-400 italic bg-gray-50 font-bold uppercase tracking-widest">
                    Nenhum fornecedor localizado com este filtro.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>   
                </div>
            </div>

        </div>
    </div>
    <script>
    function filtrarMateriasPorEmpresa(turmaId) {
        const selectMaterial = document.getElementById('materia_prima_id');
        const options = selectMaterial.options;
        
        for (let i = 0; i < options.length; i++) {
            const opt = options[i];
            const turmaDono = opt.getAttribute('data-turma');

            // 1. Sempre mostra a primeira opção ("Genérico")
            if (i === 0) {
                opt.style.display = "block";
                continue;
            }

            // 2. Se nenhuma turma for selecionada, mostra tudo (Visão Global do Professor)
            if (!turmaId) {
                opt.style.display = "block";
                continue;
            }

            // 3. Mostra se o material pertencer à turma selecionada OU for Global
            if (turmaDono == turmaId || turmaDono === 'global') {
                opt.style.display = "block";
            } else {
                opt.style.display = "none";
            }
        }
        
        // Reseta o valor selecionado para evitar que fique um material de outra turma selecionado "fantasma"
        selectMaterial.value = "";
    }
</script>
</x-app-layout>