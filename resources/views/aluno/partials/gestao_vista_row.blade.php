@php
    // 1. Blindagem e Carga de Dados
    $ordensProd = $p->ordensProducao ?? collect();
    $comprasPed = $p->compras ?? collect();
    $op = $ordensProd->where('produto_acabado_id', $i->produto_acabado_id)->first();
    $hasScrap = $op && ($op->quantidade_perda > 0);

    // --- DEFINIÇÃO DAS VARIÁVEIS DE CONTROLE (CORREÇÃO DO ERRO 500) ---
    
    // Status da Produção
    $prodConcluida = $op && $op->status == 'Concluída';
    $prodEmAndamento = $op && $op->status == 'Em Produção';
    
    // Variável Crítica: Refugo Ativo (A que deu erro no seu print)
    $isRefugoAtivo = $hasScrap && !$prodConcluida;

    // Status da Embalagem
    $packPendente = $prodConcluida && !$op->embalado;
    $packConcluido = $op && $op->embalado;

    // Status da Expedição
    $expedConcluido = $p->status == 'Faturado';

    // --- INTELIGÊNCIA DE SAÚDE DOS SETORES (ANDON) ---

    // --- INTELIGÊNCIA DE SAÚDE DOS SETORES (REVISADA) ---

    // COMPRAS: Só fica em Alerta (Vermelho) se houver algo 'Pendente' para comprar. 
    // Se já comprou (Aguardando Entrega), o setor cumpriu o papel.
    // COMPRAS:
    $compPendente = $comprasPed->where('status', 'Pendente')->count() > 0; // Vermelho (Urgente parado)
    
    // Nova Lógica: Se tem compra andando (não concluída) que NÃO é urgente, é Atenção (Amarelo)
    $temCompraAndando = $comprasPed->where('status', '!=', 'Concluído')->where('status', '!=', 'Recusado')->count() > 0;
    $compAtencao = !$compPendente && $temCompraAndando; 
    
    $compOk = $comprasPed->count() > 0 && !$compPendente && !$compAtencao; // Verde

    // WMS: Fica Amarelo (Ação) se houver caminhão vindo (Aguardando Entrega)
    $wmsAcao = $comprasPed->where('status', 'Aguardando Entrega')->count() > 0;
    // Fica Verde se tudo o que não foi recusado já estiver Concluído
    $wmsOk = $comprasPed->where('status', 'Concluído')->count() > 0 && !$wmsAcao;

    // PRODUÇÃO: Continua em Alerta (Vermelho) enquanto o material não estiver "No Estoque"
    $prodAlerta = $isRefugoAtivo || ($comprasPed->where('status', '!=', 'Concluído')->where('status', '!=', 'Recusado')->count() > 0 && !$prodConcluida);
    $prodVerde = ($prodEmAndamento || $prodConcluida) && !$prodAlerta;

    // MATRIZ FINAL DE ÍCONES
    $setores = [
        ['VEND', '💰', true, false, false],
        ['PCP', '⚙️', $ordensProd->count() > 0, false, false],
        // COMP: Verde se Ok, Vermelho se tem Pendência
        ['COMP', '🚚', $compOk, $compPendente, $compAtencao],
        // WMS: Verde se Ok, Vermelho se tem Recusa s/ Reposição, Amarelo se tem Caminhão vindo
        ['WMS', '📦', $wmsOk, false, $wmsAcao],
        ['PROD', '🏭', $prodVerde, $prodAlerta, false],
        ['PACK', '🎁', $packConcluido, false, $packPendente],
        ['EXPED', '🏁', $expedConcluido, false, ($packConcluido && !$expedConcluido)],
    ];

    $saldoRecusas = [];
@endphp

{{-- 1. CORREÇÃO DO TREMOR: REMOVIDOS TODOS OS EFEITOS DE HOVER/TRANSITION --}}
<tr class="{{ $p->cor_grupo }}">
    <td class="px-4 py-4 font-black border-r-2 border-black relative">
        
        {{-- BOTÃO DE CLIQUE --}}
        <button type="button" 
                class="bg-white border-2 border-black px-3 py-1 shadow-[3px_3px_0px_0px_black] hover:bg-yellow-400 hover:shadow-none cursor-pointer relative z-10"
                @click="activeOrder = (activeOrder === '{{ $p->id }}-{{ $i->id }}' ? null : '{{ $p->id }}-{{ $i->id }}')">
            #{{ $p->id }}
        </button>

        {{-- MODAL CENTRALIZADO (FIXO NO MEIO DA TELA) --}}
        <div x-show="activeOrder === '{{ $p->id }}-{{ $i->id }}'" 
             x-cloak
             class="fixed inset-0 z-[9999] flex items-center justify-center"
             style="display: none;">
            
            {{-- FUNDO ESCURO --}}
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="activeOrder = null"></div>

            {{-- A JANELA --}}
            <div class="relative bg-white border-4 border-black p-5 w-[600px] shadow-2xl flex flex-col max-h-[90vh]"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-90"
                 x-transition:enter-end="opacity-100 scale-100">
                
                <button @click="activeOrder = null" class="absolute -top-4 -right-4 bg-red-600 text-white w-8 h-8 rounded-full font-black border-2 border-black shadow-md hover:scale-110 flex items-center justify-center z-50">X</button>

                <div class="mb-4 border-b-4 border-black pb-2 flex justify-between items-start text-gray-800 shrink-0">
                    <div>
                        <h4 class="font-black text-2xl uppercase italic leading-none">{{ $p->cliente->nome_razao_social ?? 'Cliente' }}</h4>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="bg-black text-white text-xs font-black px-2 py-0.5 rounded">PED #{{ $p->id }}</span>
                            @if($op)
                                <span class="bg-indigo-600 text-white text-xs font-black px-2 py-0.5 rounded">OP #{{ $op->id }}</span>
                            @endif
                            <span class="text-[10px] font-bold text-gray-500 uppercase">{{ $p->cliente->cidade ?? 'Geral' }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        @if($packPendente)
                            <span class="bg-yellow-400 text-black border-2 border-black px-3 py-1 text-[10px] font-black uppercase tracking-widest animate-pulse">AGUARDANDO PACK</span>
                        @elseif($isRefugoAtivo)
                            <span class="bg-red-600 text-white border-2 border-black px-3 py-1 text-[10px] font-black uppercase tracking-widest animate-pulse">REFUGO / PARADO</span>
                        @else
                            <span class="{{ $isHistory ? 'bg-gray-800' : 'bg-green-600' }} text-white border-2 border-black px-3 py-1 text-[10px] font-black uppercase tracking-widest">
                                {{ $isHistory ? 'ARQUIVADO' : 'EM ANDAMENTO' }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- ÍCONES --}}
                <div class="grid grid-cols-7 gap-2 mb-4 p-3 bg-gray-50 border-2 border-dashed border-gray-300 rounded shrink-0">
                    @foreach($setores as $s)
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 border-2 border-black rounded flex items-center justify-center text-xl 
                                {{ $s[3] ? 'bg-red-500 text-white animate-pulse shadow-sm' : 
                                  ($s[4] ? 'bg-yellow-300 text-black animate-pulse shadow-sm' :
                                  ($s[2] ? 'bg-green-400 text-black shadow-sm' : 'bg-gray-100 opacity-30')) }}">
                                {{ $s[1] }}
                            </div>
                            <span class="text-[8px] font-black mt-1 text-gray-600">{{ $s[0] }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- TABELA CRONOLÓGICA (COM SCROLL FORÇADO) --}}
                <div class="flex-1 flex flex-col overflow-hidden">
                    <p class="text-[10px] font-black uppercase bg-black text-white px-3 py-1 inline-block mb-0 rounded-t self-start">Linha do Tempo de Suprimentos</p>
                    
                    {{-- 2. CORREÇÃO DA ROLAGEM: Altura fixa de 160px (max-h-40) --}}
                    <div class="max-h-40 overflow-y-auto border-2 border-black bg-white">
                        <table class="w-full text-xs relative">
                            <thead class="bg-gray-200 border-b-2 border-black font-black uppercase text-[9px] sticky top-0 z-10">
                                <tr>
                                    <th class="p-2 text-center w-12">Seq.</th>
                                    <th class="p-2 text-left">Material</th>
                                    <th class="p-2 text-left">Origem (Causa)</th>
                                    <th class="p-2 text-center">Status Atual</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($comprasPed as $index => $oc)
                                    @php
                                        $matId = $oc->materia_prima_id;
                                        if (!isset($saldoRecusas[$matId])) { $saldoRecusas[$matId] = 0; }

                                        $origem = 'PCP (Planejado)';
                                        $corOrigem = 'text-gray-500';

                                        // 1. Prioridade Máxima: Se o status é recusado, a culpa atual é do WMS
                                        if ($oc->status == 'Recusado') {
                                            $origem = 'WMS (RECUSA TÉCNICA)';
                                            $corOrigem = 'text-red-600 font-black';
                                            $saldoRecusas[$matId]++; 
                                        } 
                                        // 2. Se for urgente, verifica se é reposição de WMS ou PRODUÇÃO
                                        elseif ($oc->urgente) {
                                            if ($saldoRecusas[$matId] > 0) {
                                                $origem = 'WMS (REPOSIÇÃO)';
                                                $corOrigem = 'text-orange-600 font-black';
                                                $saldoRecusas[$matId]--; 
                                            } else {
                                                $origem = 'PRODUÇÃO (REFUGO)';
                                                $corOrigem = 'text-red-600 font-black';
                                            }
                                        }
                                        // 3. NOVA REGRA: Se tem Refugo na OP e não é a primeira compra (índice > 0)
                                        // Significa que é uma reposição de estoque (mesmo não sendo urgente)
                                        elseif ($hasScrap && $index > 0) {
                                            $origem = 'PRODUÇÃO (REPOSIÇÃO ESTOQUE)';
                                            $corOrigem = 'text-orange-500 font-bold';
                                        }

                                        

                                        
                                    @endphp
                                    <tr class="hover:bg-yellow-50 transition-colors">
                                        <td class="p-2 text-center font-mono">
                                            <span class="bg-gray-100 border border-black px-1 rounded text-[9px] font-bold">{{ $index + 1 }}º</span>
                                        </td>
                                        <td class="p-2 font-bold uppercase text-gray-800">
                                            {{ $oc->materiaPrima->nome ?? 'Insumo' }}
                                            <div class="text-[9px] text-indigo-900 font-black">OC #{{ $oc->id }}</div>
                                        </td>
                                        <td class="p-2 uppercase text-[10px] {{ $corOrigem }}">
                                            {{ $origem }}
                                        </td>
                                        <td class="p-2 text-center font-black">
                                            @if($oc->status == 'Recusado')
                                                <span class="text-red-600 border border-red-200 bg-red-50 px-2 py-0.5 rounded text-[9px]">🚫 RECUSADO</span>
                                            @elseif($oc->status == 'Concluído')
                                                <span class="text-blue-600">✓ NO ESTOQUE</span>
                                            @else
                                                <span class="text-orange-500">{{ $oc->status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="p-4 text-center text-gray-400 italic">Nenhuma movimentação.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </td>

    {{-- COLUNAS DA TABELA PRINCIPAL --}}
    <td class="px-4 py-4 border-r-2 border-black font-black text-xs uppercase text-gray-700">{{ $p->cliente->nome_razao_social ?? '---' }}</td>
    <td class="px-4 py-4 border-r-2 border-black font-black text-xs uppercase italic text-gray-600">{{ $i->produto->nome ?? '---' }}</td>
    <td class="px-4 py-4 text-center font-black text-lg border-r-2 border-black italic text-gray-800">{{ $i->quantidade }}</td>
    <td class="px-4 py-4 text-center border-r-2 border-black">
        @if(!$isHistory)
            @if($op)
                <span class="bg-black text-white text-[9px] font-black px-2 py-1 rounded shadow-sm">OP #{{ $op->id }}</span>
                <div class="text-[9px] font-black uppercase mt-1 {{ $op->status == 'Em Produção' ? 'text-green-600 animate-pulse' : ($hasScrap && $op->status != 'Concluída' ? 'text-red-500 font-black' : 'text-gray-400') }}">
                    {{ $hasScrap && ($op->status ?? '') != 'Concluída' ? 'REPOSIÇÃO' : ($op->status ?? 'Aberta') }}
                </div>
            @else
                <span class="text-[9px] font-black text-orange-500">AGUARDANDO PCP</span>
            @endif
        @else
            <span class="text-[9px] font-black text-gray-500 italic">{{ $p->updated_at ? $p->updated_at->format('d/m/Y') : '---' }}</span>
        @endif
    </td>
    <td class="px-4 py-4">
        @if(!$isHistory)
            <div class="flex flex-col gap-1">
                <div class="flex justify-between text-[9px] font-black uppercase text-gray-500">
                    <span>{{ ($p->dias_restantes ?? 0) < 0 ? 'ATRASADO '.abs($p->dias_restantes).' DIAS' : 'RESTAM '.($p->dias_restantes ?? 0).' DIAS' }}</span>
                </div>
                <div class="w-full h-2 bg-gray-200 border-2 border-black">
                    @php
                        $dr = $p->dias_restantes ?? 0;
                        $prog = 100 - ($dr * 10);
                        $prog = max(min($prog, 100), 0);
                        $c = $dr < 0 ? 'bg-red-600' : ($dr <= 2 ? 'bg-yellow-400' : 'bg-green-500');
                    @endphp
                    <div class="{{ $c }} h-full border-r border-black" style="width: {{ $prog }}%"></div>
                </div>
            </div>
        @else
            <div class="text-center font-black text-[9px] text-gray-400 uppercase tracking-widest">CONCLUÍDO ✓</div>
        @endif
    </td>
</tr>