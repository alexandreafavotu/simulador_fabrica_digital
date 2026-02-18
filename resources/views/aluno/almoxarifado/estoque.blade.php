<x-app-layout>

@if(Auth::user()->acessibilidade_visual)
<style>
    /* Placeholder amarelo */
    input::placeholder {
        color: #FFFF00 !important;
        opacity: 1 !important; /* mantém visível */
    }

    /* Texto digitado também amarelo */
    input {
        color: #FFFF00 !important;
        background-color: black !important;
    }

    /* Borda mantém o padrão */
</style>
@endif


    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                @if(Auth::user()->acessibilidade_visual)
    <!-- Ícone prancheta amarelo -->
    <svg class="w-8 h-8" fill="none" stroke="#FFFF00" stroke-width="2" viewBox="0 0 24 24">
        <rect x="4" y="3" width="16" height="18" rx="2"></rect>
        <line x1="8" y1="7" x2="16" y2="7"></line>
    </svg>
@else
    <span class="text-3xl">📋</span>
@endif
 {{ __('Consulta de Inventário') }}
            </h2>

            {{-- NAVEGAÇÃO CORRIGIDA (SEM ERRO DE SINTAXE) --}}
            @if(Auth::user()->tipo == 'professor')
                <a href="{{ route('professor.monitoramento.almoxarifado', $aluno->turma_id) }}" 
                   class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                    ⬅ Voltar ao WMS
                </a>
            @else
                <a href="{{ route('aluno.almoxarifado.dashboard') }}" 
                   class="bg-white text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                    @if(Auth::user()->acessibilidade_visual)
    <!-- Seta Amarela Travada para PCD -->
    <svg class="w-4 h-4 inline" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="4" 
         style="filter: none !important; stroke: #ffff00 !important; background-color: transparent !important;">
        <path d="M19 12H5M12 19l-7-7 7-7"></path>
    </svg>
@else
    ⬅
@endif Voltar ao Pátio
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12 bg-yellow-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- CARD PRINCIPAL (ESTILO PRANCHETA) --}}
            <div class="bg-white border-4 border-black rounded-xl shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                
                {{-- BARRA DE CONTROLE (TITULO + BUSCA) --}}
                <div class="bg-gray-800 text-white p-4 border-b-4 border-black flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <h3 class="font-black text-xl uppercase tracking-tighter flex items-center gap-2">
                           @if(Auth::user()->acessibilidade_visual)
    <svg class="w-6 h-6" fill="none" stroke="#FFFF00" stroke-width="2" viewBox="0 0 24 24">
        <path d="M3 7l9-4 9 4v10l-9 4-9-4V7z"></path>
        <path d="M3 7l9 5 9-5"></path>
        <path d="M12 12v9"></path>
    </svg>
@else
    <span>📦</span>
@endif
Estoque Atual
 
                        </h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">
                            Empresa: {{ isset($turma) ? $turma->nome : ($aluno->turma->nome ?? 'N/A') }}
                        </p>
                    </div>

                    {{-- BUSCA INTELIGENTE --}}
                    @php
                        $rotaBusca = Auth::user()->tipo == 'professor' 
                            ? route('professor.monitoramento.estoque', $aluno->turma_id)
                            : route('aluno.almoxarifado.estoque');
                    @endphp

                    <form method="GET" action="{{ $rotaBusca }}" class="flex gap-2 w-full md:w-auto">
                        <input type="text" name="busca" placeholder="Buscar por Nome ou SKU..." value="{{ request('busca') }}" 
                               class="text-xs font-bold text-black rounded border-2 border-black shadow-[2px_2px_0px_0px_rgba(255,255,255,0.3)] focus:ring-0 focus:border-yellow-400 w-full md:w-64">
                        <button type="submit" class="bg-yellow-400 text-black px-4 py-2 rounded border-2 border-black font-black text-xs uppercase hover:bg-yellow-300 shadow-[2px_2px_0px_0px_rgba(255,255,255,0.3)] active:translate-y-[1px] active:shadow-none transition">
                            Buscar
                        </button>
                    </form>
                </div>

                <div class="p-0 overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-[10px] font-black border-b-2 border-black">
                            <tr>
                                <th class="px-6 py-4">SKU (Código)</th>
                                <th class="px-6 py-4">Material / Insumo</th>
                                <th class="px-6 py-4">Endereço (WMS)</th>
                                <th class="px-6 py-4 text-right">Saldo Físico</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-gray-100">
                            @forelse($estoque as $item)
                                <tr class="hover:bg-yellow-50 transition group">
                                    <td class="px-6 py-4 font-mono font-bold text-gray-500 group-hover:text-black">
                                        {{ $item->sku }}
                                    </td>
                                    
                                    <td class="px-6 py-4 font-black text-gray-800 uppercase text-xs md:text-sm">
                                        {{ $item->nome }}
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        @if($item->local)
                                            {{-- Badge de Localização (Estilo Etiqueta) --}}
                                            <span class="bg-white text-blue-800 px-3 py-1 rounded border-2 border-blue-600 font-mono font-bold text-xs shadow-[2px_2px_0px_0px_rgba(37,99,235,0.2)]">
                                             @if(Auth::user()->acessibilidade_visual)
    <svg class="w-4 h-4 inline" fill="none" stroke="#FFFF00" stroke-width="2" viewBox="0 0 24 24">
        <path d="M12 2C8 2 5 5 5 9c0 5 7 13 7 13s7-8 7-13c0-4-3-7-7-7z"></path>
        <circle cx="12" cy="9" r="2.5"></circle>
    </svg>
@else
    📍
@endif
{{ $item->local->codigo_visual }}
   
                                            </span>
                                        @else
                                            {{-- Badge de Alerta (No Chão) --}}
                                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded border-2 border-yellow-500 text-[10px] font-black uppercase shadow-sm flex items-center gap-1 w-fit">
                                                <span>⚠️</span> NO CHÃO (Sem Local)
                                            </span>
                                        @endif
                                    </td>
                                    
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-lg font-black text-gray-800">
                                            {{ number_format($item->quantidade_estoque, 2, ',', '.') }} 
                                        </span>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase bg-gray-100 px-1 rounded border border-gray-200">
                                            {{ $item->unidade_medida }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-16 text-center text-gray-400 bg-gray-50">
                                        <div class="flex flex-col items-center">
                                            <span class="text-4xl opacity-30 mb-2">📦</span>
                                            <p class="font-black uppercase tracking-widest text-xs">Inventário Zerado</p>
                                            <p class="text-xs font-medium">Nenhum material encontrado no estoque.</p>
                                        </div>
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