<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                @if(Auth::user()->acessibilidade_visual)
    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="2"
        style="stroke:#FFFF00!important; fill:none!important; filter:none!important;">
        <circle cx="12" cy="12" r="10"></circle>
        <path d="M2 12h20"></path>
        <path d="M12 2a15 15 0 0 1 0 20"></path>
        <path d="M12 2a15 15 0 0 0 0 20"></path>
    </svg>
@else
    <span class="text-3xl">🗺️</span>
@endif
 {{ __('Mapa de Armazenagem (WMS)') }}
            </h2>

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
        <div class="max-w-[98%] mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- 1. ALERTAS E INSTRUÇÕES (NO CHÃO OU PICKING) -->
            @if(isset($solicitacao))
                <!-- MODO PICKING (TAREFA DE SEPARAÇÃO) -->
                <div class="bg-indigo-600 text-white p-6 border-4 border-black shadow-[8px_8px_0px_0px_black] flex flex-col md:flex-row justify-between items-center animate-pulse rounded-xl">
                    <div class="flex items-center gap-4">
                        @if(Auth::user()->acessibilidade_visual)
    <svg class="w-12 h-12" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="2"
         style="stroke:#FFFF00!important; fill:none!important;">
        <path d="M4 14v-2a8 8 0 1 1 16 0v2"></path>
        <path d="M10 18h4"></path>
        <path d="M4 14h16l-1 6H5l-1-6z"></path>
    </svg>
@else
    <span class="text-5xl">🚨</span>
@endif

                        <div>
                            <h3 class="text-2xl font-black text-yellow-300 uppercase tracking-tighter mb-1">AÇÃO NECESSÁRIA: PICKING</h3>
                            <p class="text-lg font-bold leading-tight">
                                Localize o endereço piscando e retire: <br>
                                <span class="text-2xl font-black bg-white text-indigo-800 px-3 py-1 rounded border-2 border-black mt-2 inline-block shadow-sm">
                                    {{ number_format($solicitacao->quantidade_solicitada, 2, ',', '.') }} {{ $solicitacao->materiaPrima->unidade_medida }}
                                </span>
                                de <strong class="text-yellow-300 border-b-2 border-yellow-300">{{ $solicitacao->materiaPrima->nome }}</strong>
                            </p>
                        </div>
                    </div>
                    @if(Auth::user()->acessibilidade_visual)
<div class="hidden md:block">
    <svg class="w-14 h-14" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="2"
        style="stroke:#FFFF00!important; fill:none!important;">
        <circle cx="12" cy="10" r="3"></circle>
        <path d="M12 13v8"></path>
        <path d="M5 10a7 7 0 1 1 14 0c0 7-7 11-7 11s-7-4-7-11z"></path>
    </svg>
</div>
@else
    <div class="text-6xl hidden md:block">📍</div>
@endif

                </div>

            @elseif($materiaisSemLocal->isNotEmpty())
                <!-- MODO ARMAZENAGEM (GUARDAR COISAS DO CHÃO) -->
                <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                    <div class="p-4 bg-orange-100 border-b-4 border-black flex items-center gap-2">
                        <span class="text-2xl">📥</span>
                        <h3 class="text-lg font-black text-orange-900 uppercase">1º Passo: Selecione o Material para Guardar</h3>
                    </div>
                    
                    <div class="p-6 bg-orange-50">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="lista-materiais">
                            @foreach($materiaisSemLocal as $material)
                                <div id="card-{{ $material->id }}" 
                                     {{-- TRAVA: Só aluno clica --}}
                                     @if(Auth::user()->tipo == 'aluno') onclick="selecionarMaterial({{ $material->id }})" @endif
                                     class="material-card relative bg-white border-2 border-black rounded-lg p-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,0.2)] transition-all duration-200 @if(Auth::user()->tipo == 'aluno') cursor-pointer hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,0.3)] @endif">
                                    
                                    <input type="radio" name="material_selecionado" value="{{ $material->id }}" id="radio-{{ $material->id }}" class="sr-only">
                                    
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="overflow-hidden">
                                            <p class="font-black text-gray-900 text-lg uppercase truncate leading-tight">{{ $material->nome }}</p>
                                            <p class="text-xs font-mono font-bold text-gray-500 mt-1">SKU: {{ $material->sku }}</p>
                                        </div>
                                        <div class="text-orange-500 text-3xl">📦</div>
                                    </div>
                                    
                                    <div class="bg-orange-100 border-2 border-orange-200 rounded px-2 py-1 inline-block">
                                        <span class="text-orange-800 text-sm font-black">{{ number_format($material->quantidade_estoque, 0, ',', '.') }} {{ $material->unidade_medida }}</span>
                                    </div>

                                    {{-- Check de Seleção (Invisível por padrão) --}}
                                    <div id="check-{{ $material->id }}" class="hidden absolute top-[-10px] right-[-10px] bg-green-500 text-white border-2 border-black rounded-full p-1 shadow-md z-10">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- 2. MAPA DO ARMAZÉM (VISUAL WAREHOUSE) -->
            <div class="bg-white border-4 border-black rounded-xl shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                <div class="p-6 bg-gray-100">
                    
                    <!-- NAVEGAÇÃO DAS RUAS (ABAS INDUSTRIAIS) -->
                    <div x-data="{ ruaAtiva: '{{ $ruaAlvo ?? '1' }}' }">
                        <div class="flex overflow-x-auto gap-2 border-b-4 border-black pb-0 mb-6 bg-gray-200 p-2 rounded-t-lg">
                            @foreach($locais as $rua => $enderecos)
                                <button @click="ruaAtiva = '{{ $rua }}'" 
                                    :class="ruaAtiva == '{{ $rua }}' ? 'bg-yellow-400 text-black border-black translate-y-1 shadow-none' : 'bg-white text-gray-600 border-b-4 border-gray-400 hover:bg-gray-50'"
                                    class="px-6 py-3 rounded-t-lg font-black text-sm uppercase border-2 border-b-0 transition-all duration-100 relative">
                                    RUA {{ str_pad($rua, 2, '0', STR_PAD_LEFT) }}
                                    
                                    @if(isset($ruaAlvo) && $ruaAlvo == $rua)
                                        <span class="absolute top-1 right-1 w-3 h-3 bg-red-600 border border-white rounded-full animate-ping"></span>
                                        <span class="absolute top-1 right-1 w-3 h-3 bg-red-600 border border-white rounded-full"></span>
                                    @endif
                                </button>
                            @endforeach
                        </div>

                        @foreach($locais as $rua => $enderecos)
                            <div x-show="ruaAtiva == '{{ $rua }}'" class="animate-fade-in bg-white border-4 border-black p-8 rounded-b-xl shadow-inner min-h-[500px]">
                                
                                {{-- VISÃO DA RUA --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                                    @foreach($enderecos->groupBy('coluna') as $coluna => $locaisColuna)
                                        {{-- COLUNA (ESTANTE) --}}
                                        <div class="border-x-4 border-t-4 border-gray-400 bg-gray-50 p-4 relative shadow-[inset_0_0_20px_rgba(0,0,0,0.1)]">
                                            
                                            {{-- Topo da Estante --}}
                                            <div class="absolute -top-5 left-1/2 transform -translate-x-1/2 bg-black text-white px-4 py-1 rounded border-2 border-gray-500 font-mono text-xs font-bold uppercase z-10">
                                                COLUNA {{ str_pad($coluna, 2, '0', STR_PAD_LEFT) }}
                                            </div>

                                            <div class="flex gap-8 justify-center mt-6">
                                                @foreach($locaisColuna->groupBy('vao') as $vao => $locaisVao)
                                                    <div class="flex flex-col-reverse gap-3 w-40">
                                                        
                                                        {{-- Base do Vão --}}
                                                        <div class="text-center border-t-4 border-gray-400 pt-1">
                                                            <span class="text-[9px] font-black text-gray-500 uppercase tracking-widest">VÃO {{ $vao }}</span>
                                                        </div>

                                                        @foreach($locaisVao as $local)
                                                            
                                                            @php
                                                                $ehAlvoPicking = isset($alvoLocalId) && $local->id == $alvoLocalId;
                                                                $modoPicking = isset($solicitacao);
                                                            @endphp

                                                            @php
                                                        // 1. Verifica se é o alvo do Picking
                                                        $ehAlvoPicking = isset($alvoLocalId) && $local->id == $alvoLocalId;
                                                        $modoPicking = isset($solicitacao);

                                                        // 2. Verifica o saldo real
                                                        $qtdNoLocal = $local->materiaPrima->quantidade_estoque ?? 0;
                                                        
                                                        // 3. DEFINE A TRAVA: Só pode clicar se for o alvo E tiver saldo > 0
                                                        $podeClicar = $ehAlvoPicking && $qtdNoLocal > 0;
                                                    @endphp

                                                    @if($local->ocupado)
                                                        <!-- ITEM OCUPADO (CAIXA) -->
                                                        <div class="h-24 border-2 border-black rounded flex flex-col justify-between p-2 shadow-[4px_4px_0px_0px_rgba(0,0,0,0.2)] relative group transition-all duration-300
                                                            {{-- Mudança de cor visual se for o alvo --}}
                                                            {{ $podeClicar ? 'bg-yellow-200 ring-4 ring-red-600 scale-110 cursor-pointer animate-pulse z-30' : '' }}
                                                            {{ $ehAlvoPicking && !$podeClicar ? 'bg-gray-300 ring-4 ring-gray-500 cursor-not-allowed z-30' : '' }}
                                                            {{ $modoPicking && !$ehAlvoPicking ? 'opacity-20 grayscale pointer-events-none' : '' }}
                                                            {{ !$ehAlvoPicking && !$modoPicking ? 'bg-white hover:scale-105 z-10' : '' }}"
                                                            
                                                            {{-- AQUI ESTÁ O BLOQUEIO FÍSICO: O onclick só existe se $podeClicar for true --}}
                                                            @if($podeClicar && Auth::user()->tipo == 'aluno') 
                                                                onclick="confirmarPicking({{ $solicitacao->id }}, '{{ $local->codigo_visual }}')" 
                                                            @endif
                                                            
                                                            title="{{ $local->materiaPrima->nome ?? 'Erro' }}">
                                                            
                                                            {{-- Conteúdo da Caixa --}}
                                                            <div class="border-b border-dashed border-gray-400 pb-1">
                                                                <p class="text-[11px] font-black leading-tight uppercase truncate text-gray-900">
                                                                    {{ $local->materiaPrima->nome ?? '?' }}
                                                                </p>
                                                            </div>
                                                            
                                                            <div class="bg-gray-200 p-1 rounded text-center border border-gray-200">
                                                                {{-- Texto Vermelho se saldo for zero --}}
                                                                <p class="text-lg font-black {{ $qtdNoLocal > 0 ? 'text-indigo-700' : 'text-red-600' }}">
                                                                    {{ number_format($qtdNoLocal, 0, ',', '.') }}
                                                                    <span class="text-[12px] text-gray-800">{{ $local->materiaPrima->unidade_medida ?? '' }}</span>
                                                                </p>
                                                            </div>

                                                            <div class="text-[10px] font-mono text-center text-gray-800 font-bold mt-1">
                                                                {{ $local->codigo_visual }}
                                                            </div>
                                                            
                                                            {{-- ÍCONE DO ALVO (MUDANÇA VISUAL) --}}
                                                            @if($ehAlvoPicking)
                                                                @if($podeClicar)
                                                                    {{-- Tem Saldo: Alvo Normal --}}
                                                                    <div class="absolute -top-4 -right-4 bg-red-600 text-white border-2 border-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg animate-bounce font-bold text-xs z-40">
                                                                        @if(Auth::user()->acessibilidade_visual)
    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="2"
        style="stroke:#FFFF00!important; fill:none!important;">
        <circle cx="12" cy="12" r="8"></circle>
        <circle cx="12" cy="12" r="3"></circle>
        <line x1="12" y1="4" x2="12" y2="2"></line>
        <line x1="12" y1="22" x2="12" y2="20"></line>
        <line x1="4" y1="12" x2="2" y2="12"></line>
        <line x1="22" y1="12" x2="20" y2="12"></line>
    </svg>
@else
    🎯
@endif

                                                                    </div>
                                                                @else
                                                                    {{-- Saldo Zero: Proibido --}}
                                                                    <div class="absolute -top-4 -right-4 bg-gray-500 text-white border-2 border-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg font-bold text-xs z-40">
                                                                        🚫
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    @else
                                                                <!-- ITEM LIVRE (ESPAÇO VAZIO) -->
                                                                @if(!$modoPicking)
                                                                    @if(Auth::user()->tipo == 'aluno')
                                                                        <button onclick="armazenarMaterial({{ $local->id }}, '{{ $local->codigo_visual }}')" 
                                                                            class="h-24 bg-gray-50 border-2 border-dashed border-green-400 rounded flex flex-col items-center justify-center hover:bg-green-100 hover:border-solid hover:border-green-600 hover:scale-105 transition transform shadow-sm group">
                                                                           @if(Auth::user()->acessibilidade_visual)
    <svg class="w-6 h-6 opacity-70 group-hover:opacity-100 group-hover:scale-110 transition"
        viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="3"
        style="stroke:#FFFF00!important; fill:none!important; filter:none!important;">
        <path d="M12 5v14"></path>
        <polyline points="6 13 12 19 18 13"></polyline>
    </svg>
@else
    ⬇️
@endif

                                                                            <span class="text-[9px] font-bold text-green-700 mt-1 uppercase">Guardar Aqui</span>
                                                                            <span class="text-[9px] text-gray-700 mt-1">{{ $local->codigo_visual }}</span>
                                                                        </button>
                                                                    @else
                                                                        {{-- VISÃO PROFESSOR (BLOQUEADO) --}}
                                                                        <div class="h-24 bg-gray-100 border-2 border-dashed border-gray-300 rounded flex flex-col items-center justify-center opacity-50 cursor-not-allowed">
                                                                            <span class="text-[9px] font-bold text-gray-400 uppercase">Vazio</span>
                                                                            <span class="text-[8px] text-gray-300 mt-1">{{ $local->codigo_visual }}</span>
                                                                        </div>
                                                                    @endif
                                                                @else
                                                                    {{-- MODO PICKING (VAZIOS SOMEM/APAGAM) --}}
                                                                    <div class="h-24 border-2 border-dashed border-gray-100 rounded opacity-10"></div>
                                                                @endif
                                                            @endif

                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            </div>
                                            
                                            {{-- Pé da Estante --}}
                                            <div class="mt-4 h-4 bg-gray-700 rounded-b w-full border-t-2 border-gray-500 shadow-xl"></div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulários Ocultos -->
    <form id="form-armazenar" method="POST" style="display: none;">@csrf <input type="hidden" name="local_id" id="input_local_id"></form>
    <form id="form-picking" method="POST" style="display: none;">@csrf</form>

    {{-- MODAL DE ALERTA (ERRO/AVISO) - ESTILO INDUSTRIAL --}}
    <dialog id="custom-alert" class="p-0 rounded-xl shadow-[0_0_0_100vw_rgba(0,0,0,0.6)] bg-transparent backdrop:bg-black/50 w-[90%] max-w-md open:animate-fade-in z-50">
        <div class="bg-white border-4 border-black rounded-xl shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
            <div class="bg-yellow-400 p-4 border-b-4 border-black flex items-center gap-3">
                <span class="text-3xl">⚠️</span>
                <h3 class="font-black text-xl text-black uppercase tracking-tighter">Atenção Operador</h3>
            </div>
            <div class="p-6">
                <p id="alert-message" class="text-lg font-bold text-gray-800 leading-tight"></p>
            </div>
            <div class="p-4 bg-gray-50 border-t-4 border-black flex justify-end">
                <button onclick="document.getElementById('custom-alert').close()" class="bg-black text-white px-6 py-2 rounded border-2 border-black font-black uppercase hover:bg-gray-800 shadow-[4px_4px_0px_0px_rgba(0,0,0,0.2)] active:shadow-none active:translate-y-[2px] transition">
                    Entendido
                </button>
            </div>
        </div>
    </dialog>

    {{-- MODAL DE CONFIRMAÇÃO (AÇÃO) - ESTILO INDUSTRIAL --}}
    <dialog id="custom-confirm" class="p-0 rounded-xl shadow-[0_0_0_100vw_rgba(0,0,0,0.6)] bg-transparent backdrop:bg-black/50 w-[90%] max-w-md open:animate-fade-in z-50">
        <div class="bg-white border-4 border-black rounded-xl shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
            <div class="bg-indigo-600 text-white p-4 border-b-4 border-black flex items-center gap-3">
                <span class="text-3xl">🤖</span>
                <h3 class="font-black text-xl uppercase tracking-tighter" id="confirm-title">Confirmação</h3>
            </div>
            <div class="p-6">
                <p id="confirm-message" class="text-lg font-bold text-gray-800 leading-tight whitespace-pre-line"></p>
            </div>
            <div class="p-4 bg-gray-50 border-t-4 border-black flex justify-end gap-3">
                <button onclick="document.getElementById('custom-confirm').close()" class="px-4 py-2 text-gray-600 font-bold hover:bg-gray-200 rounded uppercase border-2 border-transparent hover:border-gray-300">
                    Cancelar
                </button>
                <button id="confirm-btn-action" class="bg-green-600 text-white px-6 py-2 rounded border-2 border-black font-black uppercase hover:bg-green-700 shadow-[4px_4px_0px_0px_black] active:shadow-none active:translate-y-[2px] transition">
                    CONFIRMAR
                </button>
            </div>
        </div>
    </dialog>

    <script>
        // Variável para guardar a ação pendente
        let acaoPendente = null;

        function mostrarAlerta(mensagem) {
            document.getElementById('alert-message').innerText = mensagem;
            document.getElementById('custom-alert').showModal();
        }

        function mostrarConfirmacao(titulo, mensagem, callback) {
            document.getElementById('confirm-title').innerText = titulo;
            document.getElementById('confirm-message').innerText = mensagem;
            document.getElementById('custom-confirm').showModal();
            
            // Configura o botão de confirmar para executar a ação
            const btn = document.getElementById('confirm-btn-action');
            // Remove ouvintes antigos para não duplicar ações
            const novoBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(novoBtn, btn);
            
            novoBtn.addEventListener('click', function() {
                callback();
                document.getElementById('custom-confirm').close();
            });
        }

        function selecionarMaterial(id) {
            // Remove seleção anterior
            document.querySelectorAll('.material-card').forEach(el => {
                el.classList.remove('border-green-500', 'bg-green-50', 'ring-4', 'ring-green-200', 'scale-105');
                el.classList.add('border-black');
            });
            document.querySelectorAll('[id^="check-"]').forEach(el => el.classList.add('hidden'));

            // Aplica seleção nova
            const card = document.getElementById('card-' + id);
            card.classList.remove('border-black');
            card.classList.add('border-green-500', 'bg-green-50', 'ring-4', 'ring-green-200', 'scale-105');
            
            document.getElementById('radio-' + id).checked = true;
            document.getElementById('check-' + id).classList.remove('hidden');
        }

        function armazenarMaterial(localId, codigoVisual) {
            const materialSelecionado = document.querySelector('input[name="material_selecionado"]:checked');

            if (!materialSelecionado) {
                // SUBSTITUI O ALERT FEIO PELO MODAL 3D
                mostrarAlerta("Selecione primeiro um material na lista do topo (Área de Recebimento) antes de clicar na prateleira.");
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return;
            }
            
            const materialId = materialSelecionado.value;
            
            // SUBSTITUI O CONFIRM FEIO PELO MODAL 3D
            mostrarConfirmacao(
                "ARMAZENAR MATERIAL?",
                `Destino: ${codigoVisual}\n\nConfirmar a entrada física neste endereço?`,
                function() {
                    const form = document.getElementById('form-armazenar');
                    const inputLocal = document.getElementById('input_local_id');
                    form.action = `/aluno/almoxarifado/armazenar/${materialId}`;
                    inputLocal.value = localId;
                    form.submit();
                }
            );
        }

        function confirmarPicking(solicitacaoId, codigoVisual) {
            // SUBSTITUI O CONFIRM FEIO PELO MODAL 3D
            mostrarConfirmacao(
                "CONFIRMAR PICKING?",
                `Endereço: ${codigoVisual}\n\nO material foi localizado e retirado corretamente para a produção?`,
                function() {
                    const form = document.getElementById('form-picking');
                    form.action = `/aluno/almoxarifado/confirmar-separacao/${solicitacaoId}`;
                    form.submit();
                }
            );
        }
    </script>
</x-app-layout>