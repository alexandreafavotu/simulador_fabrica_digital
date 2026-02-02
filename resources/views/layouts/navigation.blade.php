@php
    $isProf = Auth::user()->tipo == 'professor';
    
    // Variáveis iniciais
    $dataJogo = null;
    $setor = 'indefinido'; 
    $nomeEmpresa = null;
    $nomeTurma = null;

    if(!$isProf) {
        // 1. BUSCA INTELIGENTE: Prioriza a turma que está com o JOGO ATIVO (Rodando)
        $alunoLogado = \App\Models\Aluno::where('user_id', Auth::id())
            ->whereHas('turma', function($q) {
                $q->where('jogo_ativo', true);
            })
            ->with('turma')
            ->first();

        // 2. FALLBACK: Se nenhuma estiver rodando, pega a última vinculada (para não quebrar a tela)
        if(!$alunoLogado) {
            $alunoLogado = \App\Models\Aluno::where('user_id', Auth::id())
                ->with('turma')
                ->latest('id') // Pega o vínculo mais recente
                ->first();
        }

        // 3. Preenche os dados do Menu
        if($alunoLogado) {
            $setor = $alunoLogado->setor;
            if($alunoLogado->turma) {
                $dataJogo = $alunoLogado->turma->data_jogo;
                $nomeEmpresa = $alunoLogado->turma->nome_empresa;
                $nomeTurma = $alunoLogado->turma->nome;
            }
        }
    }

    // Estilização (Mantida)
    $navClasses = $isProf 
        ? 'bg-gray-300 border-b-8 border-black sticky top-0 z-50' 
        : 'bg-gray-300 border-b-8 border-black shadow-sm sticky top-0 z-50';
    
    $linkBase = "inline-flex items-center px-4 py-2 border-2 border-black font-black text-xs uppercase tracking-widest transition-all duration-200 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none";
    $linkBaseAluno = "inline-flex items-center justify-center px-2 py-1 h-9 border-2 border-black font-black text-[10px] uppercase tracking-tighter transition-all duration-200 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none whitespace-nowrap gap-1";
@endphp

<nav x-data="{ open: false }" class="{{ $navClasses }}">
    
    {{-- LAYOUT DE PONTA A PONTA (w-full com pouco padding) --}}
    <div class="w-full px-2">
        <div class="flex justify-between h-20 items-center">
            
            {{-- ================================================= --}}
            {{-- 1. ESQUERDA: LOGO + IDENTIFICAÇÃO DA EMPRESA      --}}
            {{-- ================================================= --}}
            <div class="flex items-center shrink-0">
                <div class="flex flex-col justify-center items-start mr-2">
                    <span class="text-[12px] font-black tracking-[0.3em] text-gray-500 uppercase mb-[-4px] ml-1">Simulador</span>
                    <a href="{{ $isProf ? route('professor.dashboard') : route('dashboard') }}" class="flex items-center">
                        <img src="{{ asset('logo.png') }}" alt="Fábrica" class="block h-10 w-auto hover:scale-105 transition duration-300" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <span style="display:none;" class="font-black text-4xl text-gray-800 tracking-tighter uppercase">Fábrica<span class="text-red-600">Digital</span></span>
                    </a>

                    {{-- NOME DA EMPRESA (ALUNO) --}}
                    @if(!$isProf && $nomeEmpresa)
                        <div class="mt-1 leading-none border-l-2 border-indigo-500 pl-1 ml-1">
                            <span class="block text-[10px] font-black text-indigo-900 uppercase tracking-tight" title="{{ $nomeEmpresa }}">
                                {{ $nomeEmpresa }}  <span class="text-red-800">{{ $nomeTurma }}</span></span>
                            </span>
                                                    </div>
                    @endif
                </div>
            </div>

            {{-- ================================================= --}}
            {{-- 2. CENTRO: MENU (SEM ROLAGEM, ESPAÇAMENTO GAP-1)  --}}
            {{-- ================================================= --}}
            <div class="hidden sm:flex items-center justify-center flex-1 mx-1">
                <div class="flex gap-1 items-center flex-nowrap">
                    
                    @if($isProf)
                        {{-- MENU PROFESSOR --}}
                        <a href="{{ route('professor.dashboard') }}" class="{{ $linkBase }} {{ request()->routeIs('professor.dashboard') ? 'bg-yellow-400 text-black' : 'bg-white text-gray-700' }}">🏠 Painel</a>
                        <a href="{{ route('professor.turmas.index') }}" class="{{ $linkBase }} {{ request()->routeIs('professor.turmas.*') ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700' }}">🏭 Turmas</a>
                        <a href="{{ route('professor.global.alunos') }}" class="{{ $linkBase }} {{ request()->routeIs('professor.global.*') ? 'bg-blue-500 text-white' : 'bg-white text-gray-700' }}">👥 Alunos</a>
                        
                        <div class="relative ml-2">
                            <x-dropdown align="top" width="48">
                                <x-slot name="trigger">
                                    <button class="{{ $linkBase }} bg-white text-gray-700 gap-1">
                                        <span>📚 Cadastros</span>
                                        <svg class="fill-current h-3 w-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" /></svg>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <div class="border-2 border-black shadow-[4px_4px_0px_0px_black]">
                                        <x-dropdown-link :href="route('professor.materias-primas.index')">Matérias-Primas</x-dropdown-link>
                                        <x-dropdown-link :href="route('professor.produtos-acabados.index')">Produtos</x-dropdown-link>
                                        <x-dropdown-link :href="route('professor.clientes.index')">Clientes</x-dropdown-link>
                                        <x-dropdown-link :href="route('professor.fornecedores.index')">Fornecedores</x-dropdown-link>
                                    </div>
                                </x-slot>
                            </x-dropdown>
                        </div>

                    @else
                        {{-- MENU ALUNO (Otimizado: gap-1 e botões justos) --}}
                        
                        {{-- Empresa --}}
                        <a href="{{ route('aluno.empresa.perfil') }}" title="Perfil da Empresa"
                           class="{{ $linkBaseAluno }} {{ request()->routeIs('aluno.empresa.perfil') ? 'bg-gray-800 text-white' : 'bg-white text-gray-800' }}">
                            <span class="text-xs"></span> EMPRESA
                        </a>
                        <!-- Botão Gestão à Vista para o Aluno -->
<a href="{{ route('aluno.gestao_vista') }}" class="flex items-center gap-2 bg-yellow-400 text-black px-3 py-1.5 rounded border-2 border-black shadow-[3px_3px_0px_0px_black] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-[10px] font-black uppercase">
    <span class="text-sx"></span> Monitor
</a>
                        

                        @if($setor == 'vendas' || $setor == 'indefinido')
                            <a href="{{ route('aluno.vendas.index') }}" 
                               class="{{ $linkBaseAluno }} {{ request()->routeIs('aluno.vendas.*') ? 'bg-green-600 text-white' : 'bg-white text-gray-700 hover:bg-green-50' }}">
                                <span class="text-xs"></span> VENDAS
                            </a>
                        @endif

                        @if($setor == 'pcp' || $setor == 'indefinido')
                            <a href="{{ route('aluno.pcp.dashboard') }}" 
                               class="{{ $linkBaseAluno }} {{ request()->routeIs('aluno.pcp.*') ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-blue-50' }}">
                                <span class="text-xs"></span> PCP
                            </a>
                        @endif

                        @if($setor == 'compras' || $setor == 'indefinido')
                            <a href="{{ route('aluno.compras.dashboard') }}" 
                               class="{{ $linkBaseAluno }} {{ request()->routeIs('aluno.compras.*') ? 'bg-purple-600 text-white' : 'bg-white text-gray-700 hover:bg-purple-50' }}">
                                <span class="text-xs"></span> COMPRAS
                            </a>
                        @endif

                        @if($setor == 'almoxarifado' || $setor == 'indefinido')
                            <a href="{{ route('aluno.almoxarifado.dashboard') }}" 
                               class="{{ $linkBaseAluno }} {{ request()->routeIs('aluno.almoxarifado.*') ? 'bg-orange-500 text-white' : 'bg-white text-gray-700 hover:bg-orange-50' }}">
                                <span class="text-xs"></span> WMS
                            </a>
                        @endif

                        @if($setor == 'producao' || $setor == 'indefinido')
                            <a href="{{ route('aluno.producao.dashboard') }}" 
                               class="{{ $linkBaseAluno }} {{ request()->routeIs('aluno.producao.*') ? 'bg-yellow-500 text-white' : 'bg-white text-gray-700 hover:bg-yellow-50' }}">
                                <span class="text-xs"></span> PRODUÇÃO
                            </a>
                        @endif
                        @if($setor == 'embalagem' || $setor == 'indefinido')
    <a href="{{ route('aluno.embalagem.dashboard') }}" 
       class="{{ $linkBaseAluno }} {{ request()->routeIs('aluno.embalagem.*') ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-blue-50' }}">
        <span class="text-sm"></span> Embalagem
    </a>
@endif

                        @if($setor == 'expedicao' || $setor == 'indefinido')
                            <a href="{{ route('aluno.expedicao.dashboard') }}" 
                               class="{{ $linkBaseAluno }} {{ request()->routeIs('aluno.expedicao.*') ? 'bg-teal-600 text-white' : 'bg-white text-gray-700 hover:bg-teal-50' }}">
                                <span class="text-xs"></span> EXPEDIÇÃO
                            </a>
                        @endif

                        {{-- Dropdown Dados --}}
                        <div class="relative flex items-center h-full">
                            <x-dropdown align="top" width="48">
                                <x-slot name="trigger">
                                {{-- Adicionado 'flex items-center gap-1' para alinhar ícone, texto e seta --}}
                                <button class="{{ $linkBaseAluno }} bg-white text-gray-700 flex items-center gap-1">
                                    <span class="text-sm"></span>
                                    <span>DADOS</span>
                                    
                                    {{-- Seta para baixo --}}
                                    <svg class="fill-current h-3 w-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>
                                
                                <x-slot name="content">
                                    <div class="border-2 border-black shadow-[4px_4px_0px_0px_black]">
                                        <x-dropdown-link :href="route('aluno.materias-primas.index')">Matérias-Primas</x-dropdown-link>
                                        <x-dropdown-link :href="route('aluno.clientes.index')">Clientes</x-dropdown-link>
                                        <x-dropdown-link :href="route('aluno.fornecedores.index')">Fornecedores</x-dropdown-link>
                                    </div>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif
                    
                </div>
            </div>

            {{-- ================================================= --}}
            {{-- 3. DIREITA: RELÓGIO + PERFIL (COM ANO DE VOLTA)   --}}
            {{-- ================================================= --}}
            <div class="hidden sm:flex items-center gap-2 shrink-0">
                
                {{-- RELÓGIO (AGORA COM ANO) --}}
                @if(!$isProf && isset($dataJogo))
                    <div class="px-2 py-1 bg-white border-2 border-black rounded shadow-[2px_2px_0px_0px_black] flex flex-col items-center min-w-[100px]">
                        <span class="text-[10px] uppercase font-black text-gray-700 leading-none mb-0.5">Data Jogo</span>
                        <div class="flex items-center gap-1 text-indigo-700 font-black leading-none mt-0.5">
                            {{-- FORMATO COMPLETO d/m/Y --}}
                            <span class="text-xs">{{ $dataJogo->format('d/m/Y') }}</span>
                            <span class="text-[10px] text-gray-500">{{ $dataJogo->format('H:i') }}</span>
                        </div>
                    </div>
                @endif

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <div class="flex flex-col items-end cursor-pointer">
                            <button class="inline-flex items-center px-5 py-2 border-2 border-black {{ $isProf ? 'bg-yellow-400' : 'bg-yellow-400' }} rounded text-sm font-black text-gray-800 hover:bg-gray-50 transition shadow-[2px_2px_0px_0px_black] active:shadow-none active:translate-y-[1px]">
                                <div class="flex flex-col items-end mr-2 text-right">
                                    <span class="font-black text-gray-900 leading-none uppercase text-xs ">{{ Auth::user()->name }}</span>
                                    <span class="text-[10px] {{ $isProf ? 'text-indigo-800' : 'text-red-600' }} font-black uppercase tracking-tighter leading-none mt-0.5">
                                        {{ $isProf ? 'GAME MASTER' : 'ALUNO' }}
                                    </span>
                                </div>
                                <svg class="fill-current h-3 w-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                            </button>
                            
                            <span class="text-[10px] text-black-900 mt-0.5 italic block text-right leading-tight whitespace-nowrap mr-[3px]">
                                Desenvolvimento: Prof. Alexandre Felix de Araujo
                            </span>
                        </div>
                    </x-slot>

                    <x-slot name="content">
                        <div class="border-2 border-black shadow-[4px_4px_0px_0px_black]">
                            <x-dropdown-link :href="route('profile.edit')">{{ __('Meu Perfil') }}</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Sair') }}</x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger (Mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md border-2 border-black bg-white text-gray-800 hover:bg-gray-100 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Responsive Menu (Mantido) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t-4 border-black bg-white">
        @if(!$isProf && $nomeEmpresa)
            <div class="px-4 py-3 border-b border-indigo-100 bg-indigo-50">
                <div class="text-[10px] font-bold text-gray-500 uppercase tracking-widest leading-none">Empresa:</div>
                <div class="text-base font-black text-indigo-900 leading-tight uppercase">{{ $nomeEmpresa }}</div>
            </div>
        @endif
        
        <div class="pt-2 pb-3 space-y-1">
             @if($isProf)
                <x-responsive-nav-link :href="route('professor.dashboard')">Painel</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('professor.turmas.index')">Turmas</x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('aluno.vendas.index')">Vendas</x-responsive-nav-link>
                {{-- Outros links --}}
            @endif
             <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-black text-base text-gray-800 uppercase">{{ Auth::user()->name }}</div>
                </div>
                <div class="mt-3 space-y-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Sair') }}</x-responsive-nav-link>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>