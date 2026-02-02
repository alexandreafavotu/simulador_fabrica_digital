<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span class="text-3xl">📦</span> {{ __('WMS - Recebimento e Armazenagem') }}
            </h2>
            
            {{-- BOTÃO VOLTAR EM ESCADA --}}
            @if(Auth::user()->tipo == 'professor')
                @if($modo == 'separacao')
                    <a href="{{ route('professor.monitoramento.almoxarifado', $aluno->turma_id) }}" 
                       class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                        ⬅ Voltar ao WMS
                    </a>
                @else
                    <a href="{{ route('professor.monitoramento.index', $aluno->turma_id) }}" 
                       class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                        ⬅ Sair do Setor
                    </a>
                @endif
            @elseif(isset($modo) && $modo != 'menu')
                <a href="{{ route('aluno.almoxarifado.dashboard') }}" 
                   class="bg-white text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                    ⬅ Voltar ao Pátio
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12 bg-yellow-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- ALERTA DE CAOS (PRESERVADO) --}}
            @if($aluno->turma->mensagem_plantao_caos)
                <div class="bg-red-500 text-white p-5 border-4 border-black shadow-[8px_8px_0px_0px_black] flex items-start gap-4 animate-pulse">
                    <span class="text-3xl">📢</span>
                    <div>
                        <h3 class="font-black text-xl uppercase tracking-tighter">Plantão do Almoxarifado:</h3>
                        <p class="font-bold text-lg leading-tight">{{ $aluno->turma->mensagem_plantao_caos }}</p>
                    </div>
                </div>
            @endif

            {{-- FAIXA DE BLOQUEIO (PRESERVADO) --}}
            @php
                $bloqueado = false;
                if(isset($aluno->turma->bloqueio_almoxarifado_ate)) {
                    $dataJogo = \Carbon\Carbon::parse($aluno->turma->data_jogo);
                    $dataFimBloqueio = \Carbon\Carbon::parse($aluno->turma->bloqueio_almoxarifado_ate);
                    if($dataJogo->lt($dataFimBloqueio)) $bloqueado = true;
                }
            @endphp
            @if($bloqueado)
                <div class="bg-black text-white p-6 border-4 border-red-600 shadow-[8px_8px_0px_0px_rgba(220,38,38,0.5)] flex items-center gap-6">
                    <div class="bg-red-600 p-4 rounded border-2 border-white">
                        <span class="text-4xl">⛔</span>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black uppercase tracking-widest text-red-500">ALMOXARIFADO BLOQUEADO</h3>
                        <p class="font-bold text-gray-300">Motivo: Inventário Geral / Auditoria. Todas as operações estão suspensas.</p>
                        <p class="text-sm mt-1 text-yellow-400 font-mono">Previsão de Liberação: {{ $dataFimBloqueio->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            @endif

            {{-- LINKS INTELIGENTES (PRESERVADO) --}}
            @php
                if(Auth::user()->tipo == 'professor') {
                    $rotaEstoque = route('professor.monitoramento.estoque', $aluno->turma_id);
                    $rotaMapa = route('professor.monitoramento.mapa', $aluno->turma_id);
                    $rotaSeparacao = route('professor.monitoramento.almoxarifado', $aluno->turma_id) . '?tela=separacao';
                } else {
                    $rotaEstoque = route('aluno.almoxarifado.estoque');
                    $rotaMapa = route('aluno.almoxarifado.mapa');
                    $rotaSeparacao = route('aluno.almoxarifado.dashboard', ['tela' => 'separacao']);
                }
            @endphp

            {{-- ======================================================= --}}
            {{-- MODO 1: MENU INICIAL (VISÃO GERAL DO PÁTIO)             --}}
            {{-- ======================================================= --}}
            @if($modo == 'menu')
                
                {{-- BOTÕES DE AÇÃO RÁPIDA (TOPO) --}}
                <div class="flex justify-end gap-4 mb-6">
                    <a href="{{ $rotaEstoque }}" class="bg-white text-gray-800 px-6 py-2 rounded border-2 border-black shadow-[4px_4px_0px_0px_black] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                        📋 Ver Inventário
                    </a>
                    <a href="{{ $rotaMapa }}" class="bg-indigo-600 text-white px-6 py-2 rounded border-2 border-black shadow-[4px_4px_0px_0px_black] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                        🗺️ Abrir Mapa WMS
                    </a>
                </div>

                {{-- ÁREA DE RECEBIMENTO --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                  {{-- COLUNA 1: CHECKLIST DE ENTRADA (COM TRAVA DE INCONFORMIDADE) --}}
                    <div class="md:col-span-2 bg-white rounded-xl border-4 border-black shadow-[8px_8px_0px_0px_black] overflow-hidden">
                        <div class="p-4 bg-yellow-400 border-b-4 border-black flex items-center gap-2">
                            <span class="text-2xl">📥</span>
                            <h3 class="font-black text-lg uppercase tracking-tighter">Checklist de Entrada (Conferência)</h3>
                        </div>
                        <div class="p-6">
                            @if($entregas->isEmpty())
                                <div class="text-center p-8 bg-gray-50 border-2 border-dashed border-gray-300 rounded">
                                    <p class="text-lg font-black text-gray-400 uppercase tracking-widest">Pátio Limpo</p>
                                    <p class="text-sm font-bold text-gray-400 mt-1">Nenhum caminhão aguardando descarga no momento.</p>
                                </div>
                            @else
                                <div class="space-y-4">
                                 @foreach($entregas as $item)
                                        <div class="flex flex-col bg-gray-50 p-4 rounded-lg border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,0.1)] {{ $item->tem_inconformidade ? 'border-red-500 bg-red-50' : '' }}">
                                            <div class="flex flex-col md:flex-row justify-between items-center">
                                                <div>
                                                    <div class="text-xs font-bold text-gray-500">OC #{{ $item->id }} - {{ $item->fornecedor->nome_razao_social ?? 'Fornecedor' }}</div>
                                                    <div class="text-xl font-black text-gray-800 uppercase">{{ $item->materiaPrima->nome }}</div>
                                                    <div class="text-sm font-mono bg-white px-2 rounded border-2 border-black inline-block mt-1 font-bold">
                                                        Qtd: {{ number_format($item->quantidade, 2, ',', '.') }} {{ $item->materiaPrima->unidade_medida }}
                                                    </div>
                                                </div>

                                                {{-- LÓGICA DE BOTÕES BINÁRIA (GUIADA) --}}
                                                <div class="flex gap-2 w-full md:w-auto mt-4 md:mt-0">
                                                    @if(Auth::user()->tipo == 'aluno')
                                                        
                                                        {{-- 1. BOTÃO RECUSAR --}}
                                                        @if($item->tem_inconformidade)
                                                            {{-- Se tem defeito: HABILITA RECUSA --}}
                                                            <a href="{{ route('aluno.almoxarifado.recusar', $item->id) }}" class="flex-1 text-center bg-red-500 hover:bg-red-600 text-white border-2 border-black shadow-[3px_3px_0px_0px_black] active:shadow-none active:translate-y-[1px] text-xs font-black py-2 px-4 rounded transition uppercase animate-bounce">
                                                                ❌ Recusar
                                                            </a>
                                                        @else
                                                            {{-- Se normal: BLOQUEIA RECUSA --}}
                                                            <button disabled class="flex-1 bg-gray-200 text-gray-400 border-2 border-gray-300 text-xs font-black py-2 px-4 rounded cursor-not-allowed uppercase">
                                                                ❌ Recusar
                                                            </button>
                                                        @endif

                                                        {{-- 2. BOTÃO RECEBER --}}
                                                        @if($item->tem_inconformidade)
                                                            {{-- Se tem defeito: BLOQUEIA RECEBIMENTO --}}
                                                            <button disabled title="Recebimento suspenso por avaria técnica" class="flex-1 bg-gray-300 text-gray-500 border-2 border-gray-400 text-xs font-black py-2 px-4 rounded cursor-not-allowed uppercase">
                                                                🚫 Bloqueado
                                                            </button>
                                                        @else
                                                            {{-- Se normal: HABILITA RECEBIMENTO --}}
                                                            <form action="{{ route('aluno.almoxarifado.receber', $item->id) }}" method="POST" class="flex-1">
                                                                @csrf
                                                                <button class="w-full bg-green-500 hover:bg-green-600 text-white border-2 border-black shadow-[3px_3px_0px_0px_black] active:shadow-none active:translate-y-[1px] text-xs font-black py-2 px-4 rounded transition uppercase">
                                                                    ✅ Receber
                                                                </button>
                                                            </form>
                                                        @endif

                                                    @else
                                                        <span class="text-xs text-gray-400 italic text-center w-full">Visão Mestre</span>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- MENSAGEM DE ERRO DO PROFESSOR (SÓ APARECE SE SABOTADO) --}}
                                            @if($item->tem_inconformidade)
                                                <div class="mt-4 p-3 bg-white border-2 border-red-600 rounded flex items-center gap-3">
                                                    <span class="text-xl animate-pulse">⚠️</span>
                                                    <div>
                                                        <p class="text-[10px] font-black text-red-600 uppercase leading-none">Inconformidade no Recebimento:</p>
                                                        <p class="text-sm font-bold text-gray-800">{{ $item->descricao_inconformidade }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach   
                                </div>
                            @endif
                        </div>
                    </div>  

                    {{-- COLUNA 2: STATUS DA DOCA E INSTRUÇÕES --}}
                    <div class="bg-white rounded-xl border-4 border-black shadow-[8px_8px_0px_0px_black] overflow-hidden">
                        <div class="p-6 text-center">
                            <h4 class="font-black text-lg text-gray-800 uppercase mb-4">Status da Doca</h4>
                            
                            @if($entregas->isNotEmpty())
                                <div class="bg-green-100 p-6 rounded-xl border-4 border-green-500 mb-4 shadow-inner">
                                    {{-- CAMINHÃO PULANDO (ANIMATE-BOUNCE) --}}
                                    <div class="animate-bounce">
                                        <span class="text-6xl drop-shadow-md">🚚</span>
                                    </div>
                                    
                                    <span class="block font-black text-green-800 mt-2 uppercase tracking-widest">Caminhão na Doca</span>
                                    <span class="text-xs text-green-700 font-bold">Aguardando conferência</span>
                                </div>
                            @else
                                <div class="bg-gray-50 p-6 rounded-xl border-4 border-dashed border-gray-300 mb-4 opacity-70">
                                    <span class="text-6xl grayscale opacity-50">🚫</span>
                                    <span class="block font-bold text-gray-500 mt-2 uppercase">Doca Livre</span>
                                    <span class="text-xs text-gray-400">Aguardando próximas compras</span>
                                </div>
                            @endif

                            <div class="text-left text-xs text-gray-500 mt-6 border-t-2 border-black pt-4">
                                <p class="font-black mb-2 uppercase text-gray-700">Procedimento Padrão:</p>
                                <ul class="list-disc pl-5 space-y-2 font-bold">
                                    <li>Confira a quantidade física contra a OC.</li>
                                    <li>Se houver avaria, clique em "Recusar".</li>
                                    <li>Material recebido fica no "chão" para endereçamento.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD DE PICKING (SEPARAÇÃO) --}}
                <a href="{{ $rotaSeparacao }}" 
                   class="group bg-blue-600 border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_black] hover:bg-blue-700 active:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all duration-150 relative overflow-hidden flex items-center justify-between p-6">
                    <div class="flex items-center gap-5">
                        <div class="bg-white p-4 rounded-full border-2 border-black group-hover:scale-110 transition">
                            <span class="text-3xl">📤</span>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-white tracking-tighter uppercase">
                                Pedidos da Produção (Picking)
                            </h3>
                            <p class="text-sm font-bold text-blue-200 uppercase tracking-wide">
                                Atender Solicitações do Chão de Fábrica
                            </p>
                        </div>
                    </div>
                    
                    <div>
                        @if($stats['qtd_separacao'] > 0)
                            <span class="bg-yellow-400 text-black text-sm font-black px-4 py-2 rounded-full shadow-[4px_4px_0px_0px_black] animate-pulse inline-block border-2 border-black">
                                {{ $stats['qtd_separacao'] }} PENDENTES
                            </span>
                        @else
                            <span class="bg-blue-800 text-white text-xs font-bold px-4 py-2 rounded-full uppercase tracking-wider">
                                Tudo em Dia
                            </span>
                        @endif
                    </div>
                </a>
            @endif

            {{-- ======================================================= --}}
            {{-- MODO 2: TELA DE SEPARAÇÃO (PICKING)                     --}}
            {{-- ======================================================= --}}
            @if($modo == 'separacao')
                <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_black] overflow-hidden">
                    <div class="bg-blue-600 text-white p-4 border-b-4 border-black flex items-center gap-2">
                        <span class="text-2xl">📤</span>
                        <h3 class="font-black text-xl uppercase tracking-tighter">Fila de Separação (Picking List)</h3>
                    </div>

                    <div class="p-6">
                        @if($solicitacoes->isEmpty())
                            <div class="text-center py-16 bg-gray-50 border-4 border-dashed border-gray-200 rounded-xl">
                                <p class="text-lg font-black text-gray-400 uppercase tracking-widest">Nenhuma Solicitação Pendente</p>
                                <p class="text-sm text-gray-400 font-bold mt-1">A produção está abastecida no momento.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto border-2 border-black rounded-lg">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-800 text-white uppercase text-[10px] tracking-widest">
                                        <tr>
                                            <th class="px-6 py-3">OP Ref.</th>
                                            <th class="px-6 py-3">Produto Final</th>
                                            <th class="px-6 py-3">Material Solicitado</th>
                                            <th class="px-6 py-3 text-center">Qtd. a Separar</th>
                                            <th class="px-6 py-3 text-center">Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y-2 divide-gray-100">
                                        @foreach($solicitacoes as $sol)
                                            <tr class="hover:bg-yellow-50 transition font-medium">
                                                <td class="px-6 py-4 font-black text-gray-400">#{{ $sol->ordemProducao->id }}</td>
                                                <td class="px-6 py-4 font-bold text-gray-600 uppercase">{{ $sol->ordemProducao->produto->nome }}</td>
                                                <td class="px-6 py-4 font-black text-gray-900 uppercase">{{ $sol->materiaPrima->nome }}</td>
                                                <td class="px-6 py-4 text-center text-xl text-blue-600 font-black bg-blue-50">
                                                    {{ number_format($sol->quantidade_solicitada, 2, ',', '.') }} 
                                                    <span class="text-xs text-gray-500 font-bold">{{ $sol->materiaPrima->unidade_medida }}</span>
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    @if(Auth::user()->tipo == 'aluno')
                                                        <a href="{{ route('aluno.almoxarifado.separar', $sol->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded border-2 border-black shadow-[3px_3px_0px_0px_black] active:shadow-none active:translate-y-[1px] text-xs font-black uppercase transition flex items-center justify-center gap-2">
                                                            <span>🗺️</span> Localizar
                                                        </a>
                                                    @else
                                                        <span class="text-gray-400 text-xs italic font-bold uppercase">Visão Mestre</span>
                                                    @endif
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

        </div>
    </div>
</x-app-layout>