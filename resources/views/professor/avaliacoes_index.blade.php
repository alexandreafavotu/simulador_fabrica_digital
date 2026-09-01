<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span>🎓</span> {{ __('Avaliação de Competências (Game Master)') }}
            </h2>
            
            {{-- BOTÃO IMPORTADOR INTELIGENTE POR IA --}}
            <a href="{{ route('professor.avaliacoes.importar') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-purple-600 text-white font-black text-xs rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none hover:bg-purple-700 transition duration-150 uppercase tracking-wider">
                <span>✨</span> Importar Plano por IA
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-yellow-50 min-h-screen" x-data="{ showDeleteModal: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ALERTAS DE SUCESSO/ERRO --}}
            @if(session('success'))
                <div class="bg-green-200 border-4 border-black p-4 rounded-xl shadow-[4px_4px_0px_0px_black] flex items-center gap-3 font-black text-green-900 text-sm">
                    <span class="text-2xl">✅</span>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            {{-- FORMULÁRIO DE FILTRO DUPLO (REQUISITO CHAVE) --}}
            <div class="bg-white border-4 border-black rounded-xl p-6 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
                <h3 class="font-black text-lg text-gray-800 uppercase mb-4 flex items-center gap-2">
                    <span>🔍</span> Selecionar Turma de Avaliação
                </h3>

                <form method="GET" action="{{ route('professor.avaliacoes.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        
                        {{-- 1. SELEÇÃO DE CURSO --}}
                        <div>
                            <label class="block text-xs font-black text-gray-600 uppercase mb-1">Selecione o Curso</label>
                            <select name="curso" required class="w-full border-2 border-black rounded p-2 text-sm font-bold focus:ring-0 focus:border-indigo-600 bg-white">
                                <option value="" disabled @selected(!request('curso'))>-- Escolha o Curso --</option>
                                @foreach($cursosDisponiveis as $c)
                                    <option value="{{ $c }}" @selected(request('curso') == $c)>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 2. SELEÇÃO DE ANO LETIVO (EVITA MISTURAR ALUNOS) --}}
                        <div>
                            <label class="block text-xs font-black text-gray-600 uppercase mb-1">Ano Letivo / Semestre</label>
                            <select name="ano_letivo" required class="w-full border-2 border-black rounded p-2 text-sm font-bold focus:ring-0 focus:border-indigo-600 bg-white">
                                <option value="" disabled @selected(!request('ano_letivo'))>-- Escolha o Ano --</option>
                                @foreach($anosDisponiveis as $a)
                                    <option value="{{ $a }}" @selected(request('ano_letivo') == $a)>{{ $a }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 3. BOTÃO DE BUSCA --}}
                        <div class="flex gap-2">
                            <button type="submit" class="w-full bg-indigo-600 text-white px-6 py-2.5 rounded border-2 border-black shadow-[4px_4px_0px_0px_black] font-black uppercase text-xs hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-none transition-all duration-150">
                                Buscar Alunos
                            </button>
                            @if(request('curso') || request('ano_letivo'))
                                <a href="{{ route('professor.avaliacoes.index') }}" class="bg-gray-200 text-gray-800 px-4 py-2.5 rounded border-2 border-black shadow-[4px_4px_0px_0px_black] font-black text-xs flex items-center justify-center hover:bg-red-200 transition">
                                    Limpar
                                </a>
                            @endif
                        </div>

                    </div>
                </form>
            </div>

            {{-- TELA INICIAL (SEM BUSCA REALIZADA) --}}
            @if(!request('curso') || !request('ano_letivo'))
                <div class="bg-indigo-100 border-4 border-black p-8 rounded-xl shadow-[6px_6px_0px_0px_black] text-center">
                    <span class="text-5xl">🎓</span>
                    <h4 class="font-black text-xl text-indigo-900 uppercase mt-4">Boletim de Competências do ERP</h4>
                    <p class="text-sm font-bold text-indigo-700 mt-2 max-w-xl mx-auto">
                        Selecione o Curso e o Ano Letivo acima para listar os alunos. O simulador cruzará as ações operacionais executadas por eles no ERP com as capacidades do plano de curso oficial do SENAI-SP [2].
                    </p>
                </div>
            @else

                {{-- LISTAGEM DE ALUNOS RETORNADOS --}}
                <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_black] overflow-hidden">
                    <div class="bg-gray-800 text-white p-4 border-b-4 border-black flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">👥</span>
                            <div>
                                <h3 class="font-black text-lg uppercase tracking-tighter">Equipe de Estudantes</h3>
                                <p class="text-[10px] text-gray-400 font-bold uppercase">{{ request('curso') }} — Turma {{ request('ano_letivo') }}</p>
                            </div>
                        </div>
                        
                        {{-- BOTÃO EXCLUIR MATRIZ DO CURSO (ACIONA O MODAL NEO-BRUTALISTA) --}}
                        @if(!$alunos->isEmpty() && $alunos->first()->total_competencias > 0)
                            <button @click.prevent="showDeleteModal = true" type="button" 
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-red-600 hover:bg-red-700 text-white border-2 border-black rounded text-xs font-black uppercase shadow-[2px_2px_0px_0px_black] active:shadow-none active:translate-y-[1px] transition-all">
                                <span>🗑️</span> Excluir Matriz do Curso
                            </button>
                        @endif
                    </div>

                    <div class="p-6">
                        @if($alunos->isEmpty())
                            <div class="text-center py-12 bg-gray-50 border-4 border-dashed border-gray-200 rounded-xl">
                                <p class="text-lg font-black text-gray-400 uppercase tracking-widest">Nenhum Aluno Encontrado</p>
                                <p class="text-sm text-gray-400 font-bold mt-1">Não existem alunos ativos matriculados neste curso e ano letivo no banco de dados.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto border-2 border-black rounded-lg">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-100 uppercase text-[10px] font-black tracking-widest border-b-2 border-black">
                                        <tr>
                                            <th class="px-6 py-4">Nome do Aluno</th>
                                            <th class="px-6 py-4">E-mail Corporativo</th>
                                            <th class="px-6 py-4">Curso Associado</th>
                                            <th class="px-6 py-4 text-center">Ano Letivo</th>
                                            <th class="px-6 py-4 text-center">Rendimento</th> {{-- NOVA COLUNA --}}
                                            <th class="px-6 py-4 text-center">Ação Pedagógica</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y-2 divide-gray-100">
                                            @foreach($alunos as $aluno)
                                                <tr class="hover:bg-yellow-50 transition">
                                                    <td class="px-6 py-4 font-black text-gray-900">{{ $aluno->name }}</td>
                                                    <td class="px-6 py-4 font-mono text-xs text-gray-500">{{ $aluno->email }}</td>
                                                    <td class="px-6 py-4 font-bold text-gray-600 uppercase text-xs">{{ $aluno->curso }}</td>
                                                    <td class="px-6 py-4 text-center font-bold text-indigo-700 bg-indigo-50/50">{{ $aluno->ano_letivo }}</td>
                                                    
                                                    {{-- NOVA CELULA: BADGE DE RENDIMENTO DO ALUNO --}}
                                                    <td class="px-6 py-4 text-center">
                                                        @if($aluno->total_competencias > 0)
                                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 border-2 border-black rounded font-black text-xs uppercase shadow-[2px_2px_0px_0px_black]
                                                                @if($aluno->competencias_atingidas == $aluno->total_competencias) bg-green-500 text-white
                                                                @elseif($aluno->competencias_atingidas > 0) bg-yellow-400 text-black
                                                                @else bg-red-200 text-red-800 @endif">
                                                                🎯 {{ $aluno->competencias_atingidas }} de {{ $aluno->total_competencias }}
                                                            </span>
                                                        @else
                                                            <span class="text-xs text-gray-400 italic">Sem Matriz</span>
                                                        @endif
                                                    </td>
                                                    
                                                    <td class="px-6 py-4 text-center">
                                                        <a href="{{ route('professor.avaliacoes.aluno', $aluno->id) }}" 
                                                           class="inline-flex items-center gap-2 bg-yellow-400 text-black py-2 px-5 rounded border-2 border-black shadow-[3px_3px_0px_0px_black] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] text-xs font-black uppercase transition-all duration-150">
                                                            <span>🎓</span> Avaliar Aluno
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

            @endif

            {{-- MODAL CUSTOMIZADO NEO-BRUTALISTA DE EXCLUSÃO (ALPINE.JS) - REPOSICIONADO NO ESCOPO CORRETO --}}
            <div x-show="showDeleteModal" 
                 x-cloak
                 class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                 
                <div class="bg-white border-4 border-black rounded-xl p-6 max-w-lg w-full shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] relative space-y-6"
                     @click.away="showDeleteModal = false">
                    
                    {{-- Cabeçalho Alerta --}}
                    <div class="flex items-center gap-3 border-b-2 border-dashed border-gray-300 pb-3">
                        <span class="text-3xl">⚠️</span>
                        <h4 class="font-black text-xl text-red-600 uppercase tracking-tight">Aviso de Segurança</h4>
                    </div>

                    {{-- Corpo do Texto --}}
                    <p class="text-xs text-gray-700 font-bold leading-relaxed">
                        Você está prestes a excluir a matriz de competências do curso <strong class="text-black uppercase">"{{ request('curso') }}"</strong>.<br><br>
                        Esta ação apagará definitivamente todas as capacidades pedagógicas importadas e também <strong class="text-red-600 uppercase">todas as avaliações salvas para os alunos desta turma</strong>. Esta operação é irreversível [2].
                    </p>

                    {{-- Formulário de Confirmação --}}
                    {{-- Formulário de Confirmação com Senha --}}
            <form action="{{ route('professor.avaliacoes.excluir_curso') }}" method="POST" class="m-0">
                @csrf
                @method('DELETE')
                <input type="hidden" name="curso" value="{{ request('curso') }}">
                
                {{-- CAMPO DE SENHA OBRIGATÓRIO --}}
                <div class="mb-4">
                    <label class="block text-xs font-black uppercase text-gray-700">Digite a Senha Mestra para confirmar:</label>
                    <input type="password" name="senha_mestra" required class="w-full border-2 border-black rounded p-2 mt-1">
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t-2 border-dashed border-gray-300 justify-end">
                    <button type="button" @click="showDeleteModal = false" class="...">Cancelar</button>
                    <button type="submit" class="...">Confirmar e Excluir</button>
                </div>
            </form>
                </div>
            </div>

        </div>
    </div> {{-- py-8 bg-yellow-50 min-h-screen (FECHAMENTO DO ESCOPO DO ALPINE) --}}

    {{-- SUPORTE PARA ALPINE.JS EVITAR PISCADAS DE LAYOUT (X-CLOAK) --}}
    <style>
        [x-cloak] { display: none !important; }
    </style>

</x-app-layout>