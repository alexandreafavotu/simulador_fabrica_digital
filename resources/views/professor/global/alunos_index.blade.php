<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
            <span>👥</span> {{ __('Banco de Talentos (Cadastro Global)') }}
        </h2>
    </x-slot>

    <div class="py-8 bg-yellow-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- 1. FORMULÁRIO DE CADASTRO (MANTIDO INTACTO) -->
            <div class="bg-white border-4 border-black rounded-xl shadow- overflow-hidden">
                <div class="bg-indigo-600 text-white p-4 border-b-4 border-black flex items-center gap-2">
                    <span class="text-2xl">📝</span>
                    <div>
                        <h3 class="font-black text-lg uppercase tracking-wide">Matricular Novo Aluno</h3>
                        <p class="text-xs text-indigo-200 font-bold">Preencha com dados reais. O e-mail será verificado.</p>
                    </div>
                </div>
                
                <div class="p-6">
                    <form method="POST" action="{{ route('professor.global.store') }}">
                        @csrf
                        @if ($errors->any())
                            <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-2 text-xs font-bold">
                                <ul>
                                    @foreach ($errors->all() as $error) <li>⚠️ {{ $error }}</li> @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-4">
                            <div class="lg:col-span-5">
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">Nome Completo</label>
                                <input type="text" name="name" value="{{ old('name') }}" required class="w-full border-2 border-black rounded shadow- focus:ring-0 focus:border-indigo-600">
                            </div>
                            <div class="lg:col-span-4">
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">E-mail</label>
                                <input type="email" name="email" value="{{ old('email') }}" required class="w-full border-2 border-black rounded shadow- focus:ring-0 focus:border-indigo-600">
                            </div>
                            <div class="lg:col-span-3">
                                <label class="block text-xs font-black text-red-600 uppercase mb-1">Senha Inicial (3 Números)</label>
                                <div class="flex items-center">
                                    <span class="bg-gray-200 border-2 border-r-0 border-black px-2 py-2 text-xs font-bold text-gray-500 rounded-l">Senai</span>
                                    <input type="text" name="rg_finais" maxlength="3" required placeholder="123" class="w-full border-2 border-black rounded-r text-center font-black tracking-widest focus:ring-0 focus:border-red-500">
                                </div>
                            </div>
                            <div class="lg:col-span-5">
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">Curso</label>
                                <input type="text" name="curso" value="{{ old('curso') }}" placeholder="Ex: Logística" class="w-full border-2 border-black rounded shadow- focus:ring-0 focus:border-indigo-600">
                            </div>
                            <div class="lg:col-span-4">
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">Ano / Turma</label>
                                <input type="text" name="ano_letivo" value="{{ old('ano_letivo') }}" placeholder="Ex: 2026 - Manhã" class="w-full border-2 border-black rounded shadow- focus:ring-0 focus:border-indigo-600">
                            </div>
                            <div class="lg:col-span-3 flex items-end">
                                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-2.5 px-6 rounded border-2 border-black shadow- hover:translate-x- hover:translate-y- hover:shadow-none transition flex items-center justify-center gap-2">
                                    <span>💾</span> CADASTRAR
                                </button>
                            </div>
                        </div>
                    </form>
            </div>

            <!-- 1.5 IMPORTAÇÃO EM MASSA DE ALUNOS (EXCEL / CSV) -->
            <div class="bg-white border-4 border-black rounded-xl shadow- overflow-hidden">
                <div class="bg-green-700 text-white p-4 border-b-4 border-black flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">📊</span>
                        <div>
                            <h3 class="font-black text-lg uppercase tracking-wide">Importar Alunos em Massa (Excel / CSV)</h3>
                            <p class="text-xs text-green-200 font-bold">Envie uma planilha com Nome, E-mail, Curso e Ano/Turma para matricular a turma inteira de uma só vez.</p>
                        </div>
                    </div>
                    
                    <!-- BOTÃO DOWNLOAD MODELO -->
                    <a href="{{ route('professor.global.modelo_csv') }}" class="bg-yellow-400 hover:bg-yellow-500 text-black border-2 border-black font-black text-xs px-4 py-2.5 rounded shadow- active:shadow-none active:translate-y- transition flex items-center justify-center gap-2 shrink-0">
                        <span>📥</span> BAIXAR PLANILHA MODELO (.CSV)
                    </a>
                </div>

                <div class="p-6 bg-green-50">
                    <form method="POST" action="{{ route('professor.global.importar') }}" enctype="multipart/form-data" class="flex flex-col md:flex-row items-center gap-4">
                        @csrf
                        <div class="flex-1 w-full">
                            <label class="block text-xs font-black text-gray-800 uppercase mb-1">Selecione o Arquivo de Alunos (.csv, .txt ou .xlsx)</label>
                            <input type="file" name="arquivo" required accept=".csv,.txt,.xlsx,.xls" class="w-full bg-white border-2 border-black rounded p-2 text-xs font-mono file:mr-4 file:py-1 file:px-3 file:rounded file:border-2 file:border-black file:text-xs file:font-black file:bg-green-600 file:text-white hover:file:bg-green-700">
                        </div>

                        <div class="w-full md:w-auto flex items-end">
                            <button type="submit" class="w-full md:w-auto bg-green-600 hover:bg-green-700 text-white font-black py-3 px-8 rounded border-2 border-black shadow- hover:translate-x- hover:translate-y- transition flex items-center justify-center gap-2">
                                <span>🚀</span> IMPORTAR TURMA EM MASSA
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- LÓGICA DE DIVISÃO DOS ALUNOS NO BLADE      -->
            <!-- ========================================== -->
            @php
                $alunosAtivos = $todosAlunos->where('ativo', true);
                $alunosBloqueados = $todosAlunos->where('ativo', false);
            @endphp

            <!-- 2. LISTAGEM DE ATIVOS -->
            <div>
                <div class="flex items-center gap-2 mb-4 border-b-4 border-green-600 pb-2">
                    <span class="text-2xl">🟢</span>
                    <h3 class="font-black text-xl text-green-800 uppercase tracking-tighter">Alunos Ativos</h3>
                    <span class="ml-auto bg-green-600 text-white text-xs font-bold px-3 py-1 rounded-full border-2 border-black">{{ $alunosAtivos->count() }} Registros</span>
                </div>

                @if($alunosAtivos->isEmpty())
                    <div class="bg-white border-4 border-dashed border-gray-300 p-6 rounded-xl text-center">
                        <p class="text-gray-400 font-bold text-sm uppercase tracking-widest">Nenhum aluno ativo no momento.</p>
                    </div>
                @else
                    <div class="flex flex-col gap-4">
                        @foreach ($alunosAtivos as $aluno)
                            <!-- CARD ALONGADO (ATIVO) -->
                            <div class="bg-white border-4 border-black rounded-xl shadow- p-4 flex flex-col md:flex-row items-center gap-6">
                                
                                <!-- IDENTIFICAÇÃO -->
                                <div class="flex items-center gap-4 md:w-1/3 w-full border-r-0 md:border-r-2 border-gray-100 pr-4">
                                    <div class="bg-white border-2 border-black rounded p-2 shrink-0 relative">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="overflow-hidden">
                                        <h4 class="font-black text-lg text-gray-900 leading-tight uppercase truncate">{{ $aluno->name }}</h4>
                                        <p class="text-xs font-mono text-gray-500 truncate">{{ $aluno->email }}</p>
                                    </div>
                                </div>

                                <!-- CURSO E ANO -->
                                <div class="flex flex-col gap-1 md:w-1/4 w-full">
                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 border-2 border-blue-300 rounded text- font-black uppercase w-fit">
                                        {{ $aluno->curso ?? 'Geral' }}
                                    </span>
                                    <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 border-2 border-yellow-300 rounded text- font-black uppercase w-fit">
                                        {{ $aluno->ano_letivo ?? '---' }}
                                    </span>
                                </div>

                                <!-- SENHA OCULTA -->
                                <div class="md:w-1/4 w-full flex items-center justify-center">
                                    <span class="text-xs text-gray-400 font-bold italic bg-gray-100 px-2 py-1 rounded border border-gray-200">
                                        Senha Oculta
                                    </span>
                                </div>

                                <!-- BOTÕES DE AÇÃO -->
                                <div class="md:ml-auto flex gap-3">
                                    <!-- EDITAR ACESSIBILIDADE -->
                                    <a href="{{ route('professor.alunos.editar', $aluno->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white border-2 border-black shadow- active:shadow-none active:translate-y- px-4 py-2 rounded text-xs font-black transition flex items-center gap-2" title="Configurar Acessibilidade">
                                        <span>✏️</span> EDITAR ACESSIBILIDADE
                                    </a>
                                    
                                    <!-- RESETAR SENHA -->
                                    <x-modal-confirmacao id="reset-pass-{{ $aluno->id }}" title="RESETAR SENHA?" message="Deseja resetar a senha deste aluno para o padrão: Senaisp?" action="{{ route('professor.global.resetar', $aluno->id) }}" method="POST" type="warning" buttonText="Sim, Resetar" class="bg-white hover:bg-orange-100 text-orange-600 border-2 border-black shadow- active:shadow-none active:translate-y- px-4 py-2 rounded text-xs font-black transition flex items-center gap-2">
                                        <span>🔑</span> RESET
                                    </x-modal-confirmacao>
                                    
                                    <!-- BLOQUEAR -->
                                    <form action="{{ route('professor.usuario.toggle', $aluno->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 border-2 border-black shadow- active:shadow-none active:translate-y- px-4 py-2 rounded text-xs font-black transition flex items-center gap-2" title="Bloquear acesso">
                                            🔒 BLOQUEAR
                                        </button>
                                    </form>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- 3. LISTAGEM DE BLOQUEADOS -->
            <div class="mt-8">
                <div class="flex items-center gap-2 mb-4 border-b-4 border-red-600 pb-2 opacity-80">
                    <span class="text-2xl">🔴</span>
                    <h3 class="font-black text-xl text-red-800 uppercase tracking-tighter">Alunos Bloqueados (Acesso Negado)</h3>
                    <span class="ml-auto bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-full border-2 border-black">{{ $alunosBloqueados->count() }} Registros</span>
                </div>

                @if($alunosBloqueados->isEmpty())
                    <div class="bg-gray-100 border-4 border-dashed border-gray-300 p-6 rounded-xl text-center opacity-80">
                        <p class="text-gray-400 font-bold text-sm uppercase tracking-widest">Nenhum aluno bloqueado.</p>
                    </div>
                @else
                    <div class="flex flex-col gap-4">
                        @foreach ($alunosBloqueados as $aluno)
                            <!-- CARD ALONGADO (BLOQUEADO - Design Diferenciado) -->
                            <div class="bg-gray-100 opacity-80 border-4 border-gray-400 rounded-xl shadow- p-4 flex flex-col md:flex-row items-center gap-6 grayscale-">
                                
                                <!-- IDENTIFICAÇÃO -->
                                <div class="flex items-center gap-4 md:w-1/3 w-full border-r-0 md:border-r-2 border-gray-300 pr-4">
                                    <div class="bg-gray-200 border-2 border-gray-400 rounded p-2 shrink-0 relative">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                        <div class="absolute -top-2 -right-2 bg-red-600 text-white text- font-black px-1 rounded border border-black">BLOQ</div>
                                    </div>
                                    <div class="overflow-hidden">
                                        <h4 class="font-black text-lg text-gray-700 leading-tight uppercase truncate line-through decoration-red-500">{{ $aluno->name }}</h4>
                                        <p class="text-xs font-mono text-gray-500 truncate">{{ $aluno->email }}</p>
                                    </div>
                                </div>

                                <!-- CURSO E ANO -->
                                <div class="flex flex-col gap-1 md:w-1/4 w-full">
                                    <span class="px-2 py-0.5 bg-gray-200 text-gray-600 border-2 border-gray-300 rounded text- font-black uppercase w-fit">
                                        {{ $aluno->curso ?? 'Geral' }}
                                    </span>
                                    <span class="px-2 py-0.5 bg-gray-200 text-gray-600 border-2 border-gray-300 rounded text- font-black uppercase w-fit">
                                        {{ $aluno->ano_letivo ?? '---' }}
                                    </span>
                                </div>

                                <!-- SENHA OCULTA -->
                                <div class="md:w-1/4 w-full flex items-center justify-center">
                                    <span class="text-xs text-gray-500 font-bold italic">
                                        Acesso Suspenso
                                    </span>
                                </div>

                                <!-- BOTÕES DE AÇÃO -->
                                <div class="md:ml-auto flex gap-3">
                                    <!-- ATIVAR -->
                                    <form action="{{ route('professor.usuario.toggle', $aluno->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="bg-green-100 hover:bg-green-200 text-green-700 border-2 border-green-700 shadow- active:shadow-none active:translate-y- px-6 py-2 rounded text-xs font-black transition flex items-center gap-2" title="Liberar acesso">
                                            🔓 REATIVAR ALUNO
                                        </button>
                                    </form>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>