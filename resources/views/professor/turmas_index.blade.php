<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight">
                🏭 Minhas Fábricas (Turmas)
            </h2>
            <button onclick="document.getElementById('modal-nova-turma').showModal()" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] border-2 border-black transition hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none flex items-center gap-2">
                <span>+</span> Nova Turma
            </button>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-12">
            
            @if(session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 shadow-md" role="alert">
                    <p class="font-bold">Sucesso!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            {{-- ========================================== --}}
            {{-- SEÇÃO 1: FÁBRICAS ATIVAS (OPERACIONAIS)    --}}
            {{-- ========================================== --}}
            <div>
                <h3 class="font-black text-xl text-gray-800 mb-6 flex items-center gap-2 border-b-4 border-black pb-2 w-fit">
                    <span>🚀</span> Em Operação
                </h3>

                @if($turmasAtivas->isEmpty())
                    <div class="flex flex-col items-center justify-center py-20 text-center bg-white border-4 border-dashed border-gray-300 rounded-xl">
                        <div class="bg-gray-100 p-4 rounded-full mb-4">
                            <span class="text-6xl grayscale opacity-50">🏗️</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-500">Nenhuma fábrica ativa no momento.</h3>
                        <p class="text-gray-400 max-w-md mt-2">Clique no botão superior para fundar uma nova empresa ou reative uma do arquivo.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($turmasAtivas as $turma)
                            {{-- CARD 3D (MANTIDO O SEU ESTILO ORIGINAL) --}}
                            <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,0.2)] relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                                
                                {{-- FAIXA DE STATUS SUPERIOR --}}
                                <div class="absolute top-0 left-0 w-full h-3 {{ $turma->jogo_ativo ? 'bg-green-500' : 'bg-red-500' }}"></div>

                                <div class="p-6 pt-8">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="overflow-hidden pr-2">
                                            <h3 class="font-black text-xl text-gray-800 leading-tight truncate" title="{{ $turma->nome }}">
                                                {{ $turma->nome }}
                                            </h3>
                                            <p class="text-xs text-gray-500 mt-1 font-mono uppercase tracking-wider truncate">
                                                {{ $turma->nome_empresa ?? 'Sem Nome Empresarial' }}
                                            </p>
                                        </div>
                                        
                                        <div class="flex flex-col items-end gap-1">
                                            <span class="shrink-0 px-2 py-1 text-[10px] font-bold border-2 border-black rounded uppercase {{ $turma->jogo_ativo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $turma->jogo_ativo ? 'Operando' : 'Pausado' }}
                                            </span>
                                            
                                            {{-- BOTÃO ADORMECER (NOVO - LUA) --}}
                                            <form action="{{ route('professor.turmas.arquivar', $turma->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="text-gray-400 hover:text-indigo-600 transition text-xs font-bold flex items-center gap-1 mt-1" title="Adormecer (Arquivar) Turma">
                                                    🌙 Arquivar
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- IDENTIFICAÇÃO DO PROFESSOR (MANTIDO) --}}
                                    <div class="mb-4 flex items-center gap-2 text-xs">
                                        @if($turma->professor_id === Auth::id())
                                            <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full font-bold border border-blue-200">
                                                👤 Minha Turma
                                            </span>
                                        @else
                                            <span class="bg-purple-100 text-purple-800 px-2 py-0.5 rounded-full font-bold border border-purple-200 truncate max-w-[200px]" title="Criado por: {{ $turma->professor->name ?? 'Admin' }}">
                                                🎓 Prof. {{ $turma->professor->name ?? 'Desconhecido' }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- INFO GRID (MANTIDO) --}}
                                    <div class="grid grid-cols-2 gap-3 mb-6">
                                        <div class="bg-gray-50 p-2 rounded border border-gray-200 text-center">
                                            <p class="text-[10px] text-gray-400 uppercase font-bold">Colaboradores</p>
                                            <p class="text-2xl font-black text-indigo-600 leading-none mt-1">
                                                {{ $turma->alunos_count }}
                                            </p>
                                        </div>
                                        <div class="bg-gray-50 p-2 rounded border border-gray-200 text-center">
                                            <p class="text-[10px] text-gray-400 uppercase font-bold">Relógio do Jogo</p>
                                            <p class="text-sm font-bold text-gray-700 mt-2 leading-none">
                                                {{ $turma->data_jogo ? $turma->data_jogo->format('d/m H:i') : '--/--' }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- BARRA DE AÇÕES (MANTIDO) --}}
                                    <div class="flex flex-col space-y-3">
                                        <form action="{{ route('professor.dashboard') }}" method="GET">
                                            <input type="hidden" name="busca_turma" value="{{ $turma->nome }}">
                                            <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-500 text-black font-extrabold py-3 px-4 rounded border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none transition flex justify-center items-center gap-2 group-hover:bg-yellow-300">
                                                <span class="text-xl">📊</span> Painel de Controle
                                            </button>
                                        </form>

                                        <div class="flex gap-2">
                                            <a href="{{ route('professor.turmas.equipe', $turma->id) }}" class="flex-1 text-center bg-white hover:bg-indigo-50 text-indigo-700 font-bold py-2 px-2 rounded border-2 border-gray-200 hover:border-indigo-300 text-xs flex items-center justify-center gap-1 transition">
                                                👥 Equipe
                                            </a>
                                            <a href="{{ route('professor.turmas.edit', $turma->id) }}" class="flex-1 text-center bg-white hover:bg-gray-50 text-gray-600 font-bold py-2 px-2 rounded border-2 border-gray-200 hover:border-gray-400 text-xs flex items-center justify-center gap-1 transition">
                                                ✏️ Editar
                                            </a>
                                            
                                            {{-- BOTÃO SEGURO COM SENHA (MANTIDO) --}}
                                            <x-modal-senha-mestra 
                                                id="del-{{ $turma->id }}" 
                                                action="{{ route('professor.turmas.destroy', $turma->id) }}" 
                                                method="DELETE"
                                                class="h-full bg-red-50 hover:bg-red-100 text-red-500 hover:text-red-700 font-bold px-3 rounded border-2 border-red-100 hover:border-red-300 transition"
                                                title="EXCLUIR FÁBRICA?"
                                                mensagem="Você vai apagar a turma '{{ $turma->nome }}' e TODOS os dados (alunos, pedidos, estoque).">
                                                🗑️
                                            </x-modal-senha-mestra>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ========================================== --}}
            {{-- SEÇÃO 2: ARQUIVO MORTO (ADORMECIDAS)       --}}
            {{-- ========================================== --}}
            @if($turmasArquivadas->isNotEmpty())
                <div class="pt-8 border-t-4 border-dashed border-gray-300">
                    <h3 class="font-bold text-lg text-gray-500 mb-6 flex items-center gap-2 uppercase tracking-widest">
                        <span>💤</span> Fábricas Adormecidas (Arquivo)
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 opacity-75 hover:opacity-100 transition duration-500">
                        @foreach($turmasArquivadas as $turma)
                            {{-- CARD ARQUIVADO (VISUAL MAIS SIMPLES) --}}
                            <div class="bg-gray-200 border-2 border-gray-400 rounded-xl p-4 flex flex-col justify-between h-full grayscale hover:grayscale-0 transition duration-300">
                                <div>
                                    <div class="flex justify-between items-start">
                                        <h4 class="font-bold text-gray-700 truncate">{{ $turma->nome }}</h4>
                                        <span class="text-[9px] bg-gray-300 px-1 rounded text-gray-600 font-mono">ID: {{ $turma->id }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">{{ $turma->nome_empresa ?? 'Sem Nome' }}</p>
                                    <p class="text-[10px] text-indigo-400 font-bold mt-1 truncate">
    🎓 {{ $turma->professor->name ?? 'Sem Professor' }}
</p>
                                    <p class="text-[10px] text-gray-400 mt-2">Parada em: {{ $turma->updated_at->format('d/m/Y') }}</p>
                                </div>

                                <div class="mt-4 flex gap-2">
                                    {{-- Botão Acordar --}}
                                    <form action="{{ route('professor.turmas.arquivar', $turma->id) }}" method="POST" class="flex-1">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="w-full bg-white border-2 border-black text-black font-bold text-xs py-2 rounded hover:bg-green-100 shadow-sm flex justify-center items-center gap-1 transition">
                                            ⚡ Reativar
                                        </button>
                                    </form>
                                    
                                    {{-- Excluir (Lixeira Segura) --}}
                                    <x-modal-senha-mestra 
                                        id="del-arq-{{ $turma->id }}" 
                                        action="{{ route('professor.turmas.destroy', $turma->id) }}" 
                                        method="DELETE"
                                        class="bg-red-200 hover:bg-red-300 text-red-700 font-bold px-3 rounded border border-red-400 transition"
                                        title="EXCLUIR ARQUIVO?"
                                        mensagem="Apagar definitivamente esta turma arquivada?">
                                        ✕
                                    </x-modal-senha-mestra>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- MODAL NOVA TURMA (MANTIDO 100% IGUAL AO SEU) --}}
    {{-- MODAL NOVA TURMA - VERSÃO CORRIGIDA E LIMPA --}}
    <dialog id="modal-nova-turma" class="p-0 rounded-xl shadow-2xl backdrop:bg-gray-900/80 w-[95%] max-w-2xl open:animate-fade-in">
        <div class="bg-white border-4 border-black">
            
            {{-- Cabeçalho Fixo --}}
            <div class="flex justify-between items-center p-4 border-b-4 border-black bg-indigo-50">
                <h3 class="text-xl font-black text-gray-800 flex items-center gap-2">
                    <span>🏢</span> FUNDAÇÃO DE NOVA EMPRESA
                </h3>
                <button onclick="document.getElementById('modal-nova-turma').close()" class="text-gray-400 hover:text-red-500 font-bold text-2xl">&times;</button>
            </div>
            
            <form action="{{ route('professor.turmas.store') }}" method="POST">
                @csrf
                
                {{-- Conteúdo com Scroll --}}
                <div class="p-6 max-h-[70vh] overflow-y-auto space-y-6">
                    
                    {{-- SEÇÃO 1 --}}
                    <div class="space-y-4">
                        <h4 class="font-black text-xs text-indigo-600 uppercase border-b-2 border-indigo-100 pb-1">1. Identificação da Empresa</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-700 uppercase">Nome da Turma / Classe</label>
                                <input type="text" name="nome" required placeholder="Ex: Logística 2025" class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-700 uppercase">Nome Fantasia (Simulação)</label>
                                <input type="text" name="nome_empresa" required placeholder="Ex: TechLog S.A." class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-700 uppercase">Setor de Atuação</label>
                                <input type="text" name="setor" placeholder="Ex: Automobilístico" class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-700 uppercase">Segmento</label>
                                <input type="text" name="segmento" placeholder="Ex: Peças" class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0">
                            </div>
                        </div>
                    </div>

                    {{-- SEÇÃO 2 --}}
                    <div class="space-y-4">
                        <h4 class="font-black text-xs text-indigo-600 uppercase border-b-2 border-indigo-100 pb-1">2. Dados Jurídicos e Contato</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    {{-- 1. CAMPO DINÂMICO CNPJ OU CPF COM MÁSCARA AUTOMÁTICA EM TEMPO REAL --}}
    <div>
        <label class="block text-[10px] font-black text-gray-700 uppercase">CNPJ ou CPF</label>
        <input type="text" name="cnpj" placeholder="00.000.000/0001-00 ou 000.000.000-00" maxlength="18"
               oninput="let v = this.value.replace(/\D/g, '').slice(0, 14); if (v.length <= 11) { v = v.replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d{1,2})$/, '$1-$2'); } else { v = v.replace(/^(\d{2})(\d)/, '$1.$2').replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3').replace(/\.(\d{3})(\d)/, '.$1/$2').replace(/(\d{4})(\d)/, '$1-$2'); } this.value = v;"
               class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0 font-mono bg-white">
    </div>

    {{-- 2. CAMPO TELEFONE COM MÁSCARA DINÂMICA (CELULAR OU FIXO) --}}
    <div>
        <label class="block text-[10px] font-black text-gray-700 uppercase">Telefone</label>
        <input type="text" name="telefone" placeholder="(11) 99999-9999" maxlength="15"
               oninput="let v = this.value.replace(/\D/g, '').slice(0, 11); if (v.length > 10) { this.value = v.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3'); } else if (v.length > 5) { this.value = v.replace(/^(\d{2})(\d{4})(\d{0,4})$/, '($1) $2-$3'); } else if (v.length > 2) { this.value = v.replace(/^(\d{2})(\d{0,5})$/, '($1) $2'); } else { this.value = v; }"
               class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0 bg-white">
    </div>
</div>
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-700 uppercase">Resumo da Empresa</label>
                                <textarea name="resumo" rows="2" placeholder="Breve descrição..." class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- SEÇÃO 3 --}}
                    <div class="space-y-4">
                        <h4 class="font-black text-xs text-indigo-600 uppercase border-b-2 border-indigo-100 pb-1">3. Localização da Sede</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="md:col-span-3">
                                <label class="block text-[10px] font-black text-gray-700 uppercase">Rua / Logradouro</label>
                                <input type="text" name="rua" class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0">
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-[10px] font-black text-gray-700 uppercase">Nº</label>
                                <input type="text" name="numero" class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-700 uppercase">Bairro</label>
                                <input type="text" name="bairro" class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0">
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-[10px] font-black text-gray-700 uppercase">Cidade</label>
                                <input type="text" name="cidade" class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] focus:ring-0">
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-[10px] font-black text-gray-700 uppercase">UF</label>
                                <input type="text" name="estado" maxlength="2" placeholder="SP" class="w-full border-2 border-black p-2 rounded shadow-[2px_2px_0px_0px_black] text-center font-bold focus:ring-0">
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Rodapé Fixo com Botões --}}
                <div class="p-4 border-t-4 border-black bg-gray-50 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modal-nova-turma').close()" class="px-6 py-2 text-gray-600 font-bold hover:bg-gray-200 rounded uppercase text-xs">Cancelar</button>
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-black py-3 px-10 rounded border-2 border-black shadow-[4px_4px_0px_0px_black] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none transition uppercase text-sm">
                        🚀 Inaugurar Fábrica
                    </button>
                </div>
            </form>
        </div>
    </dialog>
</x-app-layout>