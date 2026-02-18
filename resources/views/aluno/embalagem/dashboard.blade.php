<x-app-layout>
    <style>
        /* ESTILO ORIGINAL DA ESTEIRA */
        .esteira-container {
            background: #2d3748;
            background-image: 
                linear-gradient(90deg, #1a202c 2px, transparent 2px),
                linear-gradient(0deg, #1a202c 2px, transparent 2px);
            background-size: 40px 40px;
            position: relative;
            overflow: hidden;
            border: 8px solid #000;
            box-shadow: inset 0 0 50px rgba(0,0,0,0.5);
        }

        .esteira-rolante {
            height: 60px;
            background: repeating-linear-gradient(
                45deg,
                #333,
                #333 20px,
                #444 20px,
                #444 40px
            );
            width: 200%;
            animation: moveEsteira 4s linear infinite;
            border-top: 4px solid #000;
            border-bottom: 4px solid #000;
        }

        @keyframes moveEsteira {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        .card-completo {
            border-color: #22c55e !important;
            box-shadow: 10px 10px 0px 0px rgba(34,197,94,1) !important;
        }
    </style>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
    @if(Auth::user()->acessibilidade_visual)
        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="2"
             style="stroke:#FFFF00!important; fill:none!important; filter:none!important;">
            <path d="M21 16V8a2 2 0 0 0-1-1.73L12 2 4 6.27A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73L12 22l8-4.27A2 2 0 0 0 21 16z"/>
            <polyline points="3.27 6.96 12 12 20.73 6.96"/>
            <line x1="12" y1="22" x2="12" y2="12"/>
        </svg>
    @else
        <span class="text-3xl">📦</span>
    @endif

    {{ __('Estação de Embalagem') }}
</h2>

            
            @if(Auth::user()->tipo == 'professor')
                <a href="{{ route('professor.monitoramento.index', $aluno->turma_id) }}" 
                   class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                    ⬅ Sair do Setor
                </a>
            @else
                <button onclick="history.back()" class="bg-white text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_black] font-black uppercase text-xs">@if(Auth::user()->acessibilidade_visual)
    <!-- Seta Amarela Travada para PCD -->
    <svg class="w-4 h-4 inline" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="4" 
         style="filter: none !important; stroke: #ffff00 !important; background-color: transparent !important;">
        <path d="M19 12H5M12 19l-7-7 7-7"></path>
    </svg>
@else
    ⬅
@endif Voltar</button>
            @endif
        </div>
    </x-slot>

    <div class="py-6 bg-yellow-50 min-h-screen">
        {{-- ESTEIRA CENTRAL --}}
        <div class="esteira-container w-full mb-10 h-24 flex items-center">
            <div class="esteira-rolante"></div>
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <span class="bg-black text-yellow-400 px-6 py-1 border-2 border-yellow-400 font-black italic tracking-widest uppercase text-xl shadow-xl">
                    Linha de Empacotamento Ativa
                </span>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if($ordensParaEmbalar->isEmpty())
                <div class="text-center py-20 bg-white border-4 border-dashed border-gray-300 rounded-3xl">
                    @if(Auth::user()->acessibilidade_visual)
    <div class="flex justify-center">
        <svg class="w-16 h-16" viewBox="0 0 24 24" fill="none" 
             stroke="#FFFF00" stroke-width="2"
             style="stroke:#FFFF00!important; fill:none!important; filter:none!important;">
            
            <!-- Rosto redondo -->
            <circle cx="12" cy="12" r="9" stroke="#FFFF00" stroke-width="2" />

            <!-- Olhos fechados (Traços Retos para evitar efeito de coração) -->
<line x1="8" y1="11" x2="10" y2="11" stroke="#FFFF00" stroke-width="2" />
<line x1="14" y1="11" x2="16" y2="11" stroke="#FFFF00" stroke-width="2" />


            <!-- Boca relaxada -->
            <path d="M9 15 q3 2 6 0" stroke="#FFFF00" stroke-width="2" stroke-linecap="round"/>
        </svg>
    </div>
@else
    <span class="text-7xl">😴</span>
@endif

                    <h3 class="text-2xl font-black text-gray-400 uppercase mt-4">Nenhum lote na esteira</h3>
                    <p class="font-bold text-gray-400">Aguardando a conclusão de ordens na Produção.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    @foreach($ordensParaEmbalar as $op)
                        
                        {{-- AQUI ESTÁ A CORREÇÃO MATEMÁTICA (APENAS ISSO) --}}
                        @php 
                            // Soma bruta do banco
                            $somaBruta = $op->apontamentos->sum('quantidade_produzida');
                            // Trava no limite da OP (Ex: Se produziu 8 mas OP é 5, mostra 5)
                            $qtdExibida = min($somaBruta, $op->quantidade);
                            
                            // Lógica para liberar o botão
                            $loteCompletoNaMesa = $somaBruta >= $op->quantidade;
                            
                            $totalItensBOM = $op->produto->materiasPrimas->count();
                        @endphp

                        <div x-data="{ 
                                totalItens: {{ $totalItensBOM }},
                                selecionados: 0,
                                toggleItem(el) {
                                    if(el.checked) this.selecionados++;
                                    else this.selecionados--;
                                }
                             }" 
                             :class="selecionados === totalItens && {{ $loteCompletoNaMesa ? 'true' : 'false' }} ? 'card-completo' : ''"
                             class="bg-white border-4 border-black rounded-2xl shadow-[10px_10px_0px_0px_black] transition-all duration-300">
                            
                            {{-- CABEÇALHO ORIGINAL --}}
                            <div class="p-4 border-b-4 border-black flex justify-between items-center bg-gray-100">
                                <div>
                                    <span class="bg-black text-white text-[9px] font-black px-2 py-0.5 rounded uppercase">Lote: #{{ $op->id }}</span>
                                    <h4 class="text-2xl font-black text-gray-800 uppercase leading-none mt-1">{{ $op->produto->nome }}</h4>
                                </div>
                                <div class="text-right">
                                    <p class="text-[9px] font-black text-gray-500 uppercase">Status Produção</p>
                                    {{-- AQUI: Usando a variável corrigida $qtdExibida --}}
                                    <p class="text-lg font-black leading-none {{ $loteCompletoNaMesa ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $qtdExibida }} <span class="text-xs text-gray-400">/ {{ $op->quantidade }} un</span>
                                    </p>
                                </div>
                            </div>

                            {{-- LISTA ORIGINAL (CHECKLIST) --}}
                            <div class="p-6 space-y-4">
                                <h5 class="text-xs font-black text-gray-800 uppercase tracking-widest mb-2">Checklist de Materiais (Status WMS):</h5>
                                
                                <div class="grid grid-cols-1 gap-2">
                                    @foreach($op->produto->materiasPrimas as $mp)
                                        @php 
                                            $sol = $op->solicitacoesMaterial->where('materia_prima_id', $mp->id)->first();
                                            $materialEntreguePeloWms = ($sol && $sol->status == 'Entregue');
                                        @endphp
                                        
                                        <label class="flex items-center justify-between p-3 border-2 rounded-lg transition-all 
                                            {{ $materialEntreguePeloWms 
                                                ? 'border-black cursor-pointer hover:bg-yellow-50 group' 
                                                : 'border-red-200 bg-red-50 opacity-80 cursor-not-allowed' }}">
                                            
                                            <div class="flex items-center gap-3">
                                                <input type="checkbox" 
                                                    @change="toggleItem($el)" 
                                                    {{ $materialEntreguePeloWms ? '' : 'disabled' }}
                                                    class="w-6 h-6 border-2 border-black rounded text-green-600 focus:ring-0 {{ $materialEntreguePeloWms ? 'cursor-pointer' : 'cursor-not-allowed' }}">
                                                
                                                <div>
                                                    <p class="font-black {{ $materialEntreguePeloWms ? 'text-gray-800' : 'text-red-800' }} uppercase text-sm leading-none">
                                                        {{ $mp->nome }}
                                                    </p>
                                                    <p class="text-[9px] font-bold uppercase mt-1 text-gray-500">
    @if($materialEntreguePeloWms)
        
        {{-- MATERIAL DISPONÍVEL --}}
        @if(Auth::user()->acessibilidade_visual)
            <svg class="w-3 h-3 inline" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="3"
                style="stroke:#FFFF00!important;">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        @else
            ✓
        @endif
        Material Disponível

    @else

        {{-- AGUARDANDO – ALERTA AMARELO --}}
        @if(Auth::user()->acessibilidade_visual)
            <svg class="w-3 h-3 inline" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="2"
                 style="stroke:#FFFF00!important;">
                <path d="M12 2 L22 20 H2 Z"></path>
                <line x1="12" y1="8" x2="12" y2="13"></line>
                <circle cx="12" cy="17" r="1"></circle>
            </svg>
        @else
            ⚠️
        @endif
        Aguardando Almoxarifado

    @endif
</p>

                                                </div>
                                            </div>

                                            @if($materialEntreguePeloWms)
                                                <span class="text-[9px] font-black text-green-600 uppercase opacity-0 group-hover:opacity-100">Conferir</span>
                                            @endif
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- RODAPÉ ORIGINAL --}}
                            <div class="p-4 bg-gray-50 border-t-4 border-black flex items-center justify-between">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-gray-800 uppercase">Conferência Física:</span>
                                    <span class="font-black text-sm" :class="selecionados === totalItens ? 'text-green-600' : 'text-orange-500'">
                                        <span x-text="selecionados"></span> de {{ $totalItensBOM }} COMPONENTES
                                    </span>
                                </div>

                                <form action="{{ route('aluno.embalagem.confirmar', $op->id) }}" method="POST">
                                    @csrf
                                    
                                    @if($loteCompletoNaMesa)
                                        <button type="submit" 
                                                x-show="selecionados === totalItens"
                                                x-transition
                                                class="bg-green-500 hover:bg-green-600 text-white border-2 border-black shadow-[4px_4px_0px_0px_black] active:shadow-none active:translate-y-[1px] px-6 py-2 rounded font-black uppercase text-xs transition-all flex items-center gap-2">
                                            @if(Auth::user()->acessibilidade_visual)
    <svg class="w-4 h-4 inline" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="2"
         style="stroke:#FFFF00!important; fill:none!important;">
        <rect x="3" y="7" width="18" height="14" rx="2" ry="2"></rect>
        <polyline points="3,7 12,2 21,7"></polyline>
    </svg>
@else
    📦
@endif
Lacrar Lote / Embalar

                                        </button>
                                        
                                        <div x-show="selecionados !== totalItens" class="text-[12px] font-black text-gray-400 uppercase italic">
                                            Aguardando Checklist...
                                        </div>
                                    @else
                                        <div class="bg-red-600 text-white text-[9px] font-black px-3 py-2 rounded border-2 border-black animate-pulse uppercase">
                                            Lote Incompleto (Refugo)
                                        </div>
                                    @endif
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>