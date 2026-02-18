<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                @if(Auth::user()->acessibilidade_visual)
    <!-- SVG protegido contra os filtros do CSS global -->
    <svg class="w-8 h-8 inline" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="2" 
         style="filter: none !important; background-color: transparent !important; stroke: #ffff00 !important;">
        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
    </svg>
@else
    <span>🏢</span>
@endif
PERFIL CORPORATIVO:<span class="text-indigo-600">{{ $turma->nome_empresa }}</span>
            </h2>
            
            {{-- Botão Voltar Inteligente --}}
            @php $isPcd = Auth::user()->acessibilidade_visual; @endphp

            @if(Auth::user()->tipo == 'professor')
                <a href="{{ route('professor.dashboard') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_black] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                    @if($isPcd)
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="4" style="filter: none !important; stroke: #ffff00 !important;"><path d="M19 12H5M12 19l-7-7 7-7"></path></svg>
                    @else
                        ⬅
                    @endif 
                    Voltar ao Painel
                </a>
            @else
                <button onclick="history.back()" class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_black] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                    @if($isPcd)
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="4" style="filter: none !important; stroke: #ffff00 !important;"><path d="M19 12H5M12 19l-7-7 7-7"></path></svg>
                    @else
                        ⬅
                    @endif 
                    Voltar
                </button>
            @endif
        </div>
    </x-slot>

    <div class="py-12 bg-yellow-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            {{-- FICHA DA EMPRESA --}}
            <div class="bg-white border-4 border-black rounded-xl shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                
                {{-- Cabeçalho Estilizado --}}
<div class="bg-indigo-600 text-white p-8 border-b-4 border-black relative overflow-hidden">
    
    <!-- 1. MARCA D'ÁGUA: Posição original (top-4) protegida pelo overflow-hidden -->
    <div class="absolute top-4 right-4 text-6xl font-black italic uppercase pointer-events-none" 
         style="z-index: 0; background-color: transparent !important; 
                {{ Auth::user()->acessibilidade_visual ? 'color: #ffff00 !important; opacity: 50 !important;' : 'opacity: 20;' }}">
        EMPRESA
    </div>

    <!-- 2. CONTEÚDO: Usando w-fit para não criar faixas pretas horizontais -->
    <div class="relative" style="z-index: 10; background-color: transparent !important;">
        <p class="w-fit text-xs font-bold uppercase tracking-[0.3em] opacity-80 mb-1" 
           style="background-color: transparent !important;">
           Registro de Atividade Industrial
        </p>
        
        <h1 class="w-fit text-4xl font-black tracking-tighter uppercase" 
            style="background-color: transparent !important;">
            {{ $turma->nome_empresa }}
        </h1>
        
        <!-- mt-12 garante que os selos fiquem bem longe da palavra ao fundo -->
        <div class="mt-12 flex flex-wrap gap-4" style="background-color: transparent !important;">
            <span class="px-3 py-1 rounded border-2 font-black text-xs uppercase {{ Auth::user()->acessibilidade_visual ? 'bg-black text-yellow-400 border-yellow-400' : 'bg-white text-indigo-900 border-black' }}">
                {{ $turma->setor ?? 'Setor não definido' }}
            </span>
            <span class="px-3 py-1 rounded border-2 font-bold text-xs uppercase {{ Auth::user()->acessibilidade_visual ? 'bg-black text-yellow-400 border-yellow-400' : 'bg-indigo-400 text-white border-indigo-900' }}">
                {{ $turma->segmento ?? 'Segmento não definido' }}
            </span>
        </div>
    </div>
</div>

                <div class="p-8 space-y-8">
                    
                    {{-- MISSÃO E RESUMO --}}
                    <div>
                        <h4 class="font-black text-xs text-gray-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                            @if(Auth::user()->acessibilidade_visual)
    <svg class="w-5 h-5 inline" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="2" 
         style="filter: none !important; background-color: transparent !important; stroke: #ffff00 !important;">
        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
    </svg>
@else
    <span>📝</span>
@endif
Resumo e Missão
                            
                        </h4>
                        <div class="bg-gray-50 border-2 border-black p-4 rounded-lg italic text-gray-700 leading-relaxed">
                            "{{ $turma->resumo ?? 'Nenhuma descrição cadastrada para esta unidade fabril.' }}"
                        </div>
                    </div>

                    {{-- DADOS TÉCNICOS --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="border-2 border-black p-4 rounded-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] bg-white">
                            <p class="text-[10px] font-black text-gray-400 uppercase">Número do CNPJ</p>
                            <p class="text-lg font-mono font-black text-gray-800">{{ $turma->cnpj ?? '---' }}</p>
                        </div>
                        <div class="border-2 border-black p-4 rounded-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] bg-white">
                            <p class="text-[10px] font-black text-gray-400 uppercase">Contato Telefônico</p>
                            <p class="text-lg font-black text-gray-800">{{ $turma->telefone ?? '---' }}</p>
                        </div>
                    </div>

                    {{-- LOCALIZAÇÃO SEDE --}}
                    <div class="bg-gray-100 border-4 border-black p-6 rounded-xl relative overflow-hidden">
                        @if(Auth::user()->acessibilidade_visual)
    <svg class="w-16 h-16" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="2" 
         style="filter: none !important; background-color: transparent !important; stroke: #ffff00 !important;">
        <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
        <circle cx="12" cy="11" r="3"></circle>
    </svg>
@else
    📍
@endif
                        <h4 class="font-black text-sm text-gray-800 uppercase mb-4 border-b-2 border-gray-300 pb-2">Endereço da Unidade</h4>
                        
                        <div class="space-y-2">
                            <p class="text-lg font-bold text-gray-800">
                                {{ $turma->rua ?? 'Rua não cadastrada' }}, {{ $turma->numero ?? 's/n' }}
                            </p>
                            <p class="text-sm font-medium text-gray-600 uppercase">
                                Bairro: {{ $turma->bairro ?? '---' }}
                            </p>
                            <div class="flex gap-4">
                                <div class="flex-1 bg-white border-2 border-black p-2 rounded">
                                    <p class="text-[9px] font-black text-gray-400 uppercase leading-none">Cidade</p>
                                    <p class="text-sm font-black text-gray-800">{{ $turma->cidade ?? '---' }}</p>
                                </div>
                                <div class="w-20 bg-white border-2 border-black p-2 rounded text-center">
                                    <p class="text-[9px] font-black text-gray-400 uppercase leading-none">Estado</p>
                                    <p class="text-sm font-black text-gray-800 uppercase">{{ $turma->estado ?? '--' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RODAPÉ DA FICHA --}}
                    <div class="pt-6 border-t-2 border-dashed border-gray-200 flex justify-between items-center opacity-50">
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Sistema Fábrica-Digital v1.5 • Registro Eletrônico</p>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">ID: #{{ $turma->id }}</p>
                    </div>
                </div>
            </div>

            {{-- Aviso de Segurança para o Aluno --}}
            @if(Auth::user()->tipo == 'aluno')
                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-400 font-bold italic">
                        ⚠️ Visualização restrita. Alterações apenas via Gerência (Professor).
                    </p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>