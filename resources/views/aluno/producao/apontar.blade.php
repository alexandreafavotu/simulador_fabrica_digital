<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                @if(Auth::user()->acessibilidade_visual)
    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="2"
         style="stroke:#FFFF00!important; filter:none!important;">
        <rect x="3" y="10" width="6" height="10"></rect>
        <rect x="10" y="6" width="6" height="14"></rect>
        <rect x="17" y="12" width="4" height="8"></rect>
    </svg>
@else
    <span class="text-3xl">🏭</span>
@endif
 {{ __('Apontamento de Produção') }}
            </h2>
            
            {{-- Botão Voltar Inteligente --}}
            <button onclick="history.back()" 
               class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                @if(Auth::user()->acessibilidade_visual)
    <!-- Seta Amarela Travada para PCD -->
    <svg class="w-4 h-4 inline" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="4" 
         style="filter: none !important; stroke: #ffff00 !important; background-color: transparent !important;">
        <path d="M19 12H5M12 19l-7-7 7-7"></path>
    </svg>
@else
    ⬅
@endif Voltar
            </button>
        </div>
    </x-slot>

    <div class="py-12 bg-yellow-50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            {{-- CÁLCULO DE SALDO REAL (A MÁGICA QUE FALTAVA) --}}
            @php
    // Lógica Original de Saldo
    $jaProduzido = $op->apontamentos->sum('quantidade_produzida');
    $saldoRestante = $op->quantidade - $jaProduzido;
    if($saldoRestante < 0) $saldoRestante = 0;

    // NOVA LÓGICA CIRÚRGICA: REFUGO FORÇADO (CAOS)
    $isForcado = $op->tem_refugo_forcado;
    $valRefugo = $isForcado ? $op->qtd_refugo_forcado : 0;
    // O que sobra para o aluno aprovar é o saldo menos o que você sabotou
    $valBoas = $isForcado ? ($saldoRestante - $valRefugo) : $saldoRestante;
    if($valBoas < 0) $valBoas = 0;
@endphp

            {{-- CARD INDUSTRIAL --}}
            <div class="bg-white border-4 border-black rounded-xl shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                
                {{-- CABEÇALHO DO CARD --}}
                <div class="bg-indigo-600 text-white p-6 border-b-4 border-black flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @if(Auth::user()->acessibilidade_visual)
    <!-- Ícone amarelo para PCD -->
    <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="2"
         style="stroke:#FFFF00!important; filter:none!important;">
        <rect x="3" y="4" width="14" height="16" rx="2" ry="2"></rect>
        <line x1="9" y1="8" x2="15" y2="8"></line>
        <line x1="9" y1="12" x2="15" y2="12"></line>
        <line x1="9" y1="16" x2="13" y2="16"></line>
    </svg>
@else
    <span class="text-4xl">📝</span>
@endif

                        <div>
                            <h3 class="text-2xl font-black uppercase tracking-tighter">Finalizar Ordem #{{ $op->id }}</h3>
                            <p class="text-xs font-bold text-indigo-200 uppercase tracking-widest">Entrada de Produto Acabado</p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                  {{-- 
                        LÓGICA DE VALORES GUIADA (SEM MARGEM PARA ERRO DO ALUNO)
                        Se tiver CAOS: Usa os valores forçados pelo professor.
                        Se for NORMAL: O sistema assume perfeição (Tudo BOM, Zero REFUGO).
                    --}}
                    @php
                        if ($isForcado) {
                            // Cenário de Sabotagem
                            $finalBoas = $valBoas;     // Calculado lá em cima (Saldo - Refugo)
                            $finalRefugo = $valRefugo; // O que o professor mandou
                        } else {
                            // Cenário Perfeito (Caminho Feliz)
                            $finalBoas = $saldoRestante; // Produziu tudo o que faltava
                            $finalRefugo = 0;            // Zero perda
                        }
                    @endphp

                    {{-- ALERTA DE SABOTAGEM --}}
                    @if($isForcado)
                        <div class="mb-8 bg-red-600 text-white p-5 border-4 border-black shadow-[6px_6px_0px_0px_black] relative animate-pulse">
                            <span class="absolute -top-3 left-4 bg-black text-yellow-400 text-[10px] font-black px-2 py-1 uppercase border border-yellow-400">
                                Laudo Técnico de Falha
                            </span>
                            <p class="text-sm font-bold italic">"{{ $op->motivo_refugo_forcado }}"</p>
                        </div>
                    @endif

                    <!-- Resumo Técnico (Visualização) -->
                    <div class="bg-gray-100 border-2 border-black p-4 rounded-lg mb-8 grid grid-cols-3 gap-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,0.1)]">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase font-black tracking-widest">Produto Alvo</p>
                            <p class="text-xl font-black text-indigo-700 uppercase leading-none mt-1">{{ $op->produto->nome }}</p>
                        </div>
                        <div class="text-center border-x-2 border-gray-300">
                            <p class="text-[10px] text-gray-500 uppercase font-black tracking-widest">Meta Total</p>
                            <p class="text-xl font-black text-gray-800 uppercase leading-none mt-1">{{ $op->quantidade }} <span class="text-xs">un</span></p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-500 uppercase font-black tracking-widest">Saldo a Fazer</p>
                            <p class="text-2xl font-black text-green-600 uppercase leading-none mt-1">{{ $saldoRestante }} <span class="text-xs">un</span></p>
                        </div>
                    </div>

                    <form action="{{ route('aluno.producao.salvar_apontamento', $op->id) }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                            <!-- Quantidade Boa -->
                            <div class="bg-green-50 p-4 rounded-xl border-2 border-green-200">
                                <label class="block text-xs font-black text-green-800 uppercase mb-2">✅ Quantidade Aprovada</label>
                                
                                {{-- INPUT TRAVADO (READONLY SEMPRE) --}}
                                <input type="number" name="quantidade_produzida" 
                                       value="{{ $finalBoas }}" 
                                       readonly
                                       class="block w-full border-4 {{ $isForcado ? 'border-gray-400 bg-gray-100' : 'border-green-600 bg-white' }} rounded-lg shadow-sm focus:ring-0 text-3xl font-black text-green-700 text-center p-2 cursor-not-allowed">
                                
                                <p class="text-[10px] font-bold text-green-600 mt-2 uppercase">
                                    {{ $isForcado ? 'Ajuste automático por falha' : 'Produção 100% Eficiente' }}
                                </p>
                            </div>

                            <!-- Refugo -->
                            <div class="bg-red-50 p-4 rounded-xl border-2 border-red-200">
                                <label class="block text-xs font-black text-red-800 uppercase mb-2">🗑️ Refugo (Perda)</label>
                                
                                {{-- INPUT TRAVADO (READONLY SEMPRE) --}}
                                <input type="number" name="quantidade_refugo" 
                                       value="{{ $finalRefugo }}" 
                                       readonly
                                       class="block w-full border-4 {{ $isForcado ? 'border-gray-400 bg-gray-100' : 'border-red-500 bg-white' }} rounded-lg shadow-sm focus:ring-0 text-3xl font-black text-red-600 text-center p-2 cursor-not-allowed">
                                
                                <p class="text-[10px] font-bold text-red-500 mt-2 uppercase">
                                    {{ $isForcado ? 'Perda detectada pela máquina' : 'Nenhuma anomalia registrada' }}
                                </p>
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="flex justify-end gap-4 border-t-2 border-dashed border-gray-300 pt-6">
                            <a href="{{ route('aluno.producao.dashboard') }}" 
                               class="px-6 py-3 bg-white text-gray-700 border-2 border-black rounded font-black text-xs uppercase hover:bg-gray-200 transition">
                                Cancelar
                            </a>
                            
                            <button type="submit" 
                                    class="px-8 py-3 bg-green-600 text-white rounded border-2 border-black font-black text-sm uppercase hover:bg-green-700 shadow-[6px_6px_0px_0px_black] transition transform hover:-translate-y-1 active:translate-y-[1px] active:shadow-none flex items-center gap-2">
                                <span>📥</span> Confirmar Entrada no Estoque
                            </button>
                        </div>
                    </form>  

                </div>
            </div>
            
            <div class="text-center mt-6">
                 <p class="text-xs font-bold text-gray-400 uppercase">Confirmar estes dados registrará o lote no sistema.</p>
            </div>

        </div>
    </div>
    
   
    {{-- SCRIPT DE VALIDAÇÃO MATEMÁTICA (COM TRAVA DO CAOS) --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputBoas = document.querySelector('input[name="quantidade_produzida"]');
        const inputRefugo = document.querySelector('input[name="quantidade_refugo"]');
        
        // --- 1. CAPTURA AS VARIÁVEIS DO PHP ---
        const limiteMaximo = {{ $saldoRestante }};
        // Aqui pegamos se está sabotado ou não (true/false)
        const isForcado = {{ $isForcado ? 'true' : 'false' }};

        // --- 2. SÓ ATIVA A LÓGICA SE NÃO ESTIVER SABOTADO ---
        if (!isForcado) {
            
            function validarTotais() {
                let boas = parseInt(inputBoas.value) || 0;
                let refugo = parseInt(inputRefugo.value) || 0;
                
                let totalTentado = boas + refugo;

                // Regra: Se a soma passar do saldo, diminui as peças boas
                if (totalTentado > limiteMaximo) {
                    let boasCorrigidas = limiteMaximo - refugo;
                    if(boasCorrigidas < 0) boasCorrigidas = 0;
                    
                    inputBoas.value = boasCorrigidas;
                    
                    // Feedback visual rápido
                    inputBoas.classList.add('bg-red-100');
                    setTimeout(() => inputBoas.classList.remove('bg-red-100'), 500);
                }
            }

            // Ativa os "ouvidos" nos campos apenas se o aluno tiver controle
            inputBoas.addEventListener('input', validarTotais);
            inputRefugo.addEventListener('input', validarTotais);
        }
    });
    </script>
</x-app-layout>