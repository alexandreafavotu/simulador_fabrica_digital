<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span>📈</span> {{ __('Análise de Necessidades (MRP)') }}
            </h2>
            
            {{-- BOTÃO VOLTAR INTELIGENTE --}}
            @if(Auth::user()->tipo == 'professor')
                <a href="{{ route('professor.monitoramento.pcp', $pedido->turma_id) }}" 
                   class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                    ⬅ Voltar ao PCP
                </a>
            @else
                <a href="{{ route('aluno.pcp.dashboard') }}" 
                   class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                    ⬅ Voltar ao Menu
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12 bg-yellow-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- CARD PRINCIPAL DA ANÁLISE --}}
            <div class="bg-white border-4 border-black rounded-xl shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                <div class="p-6">
                    
                    <!-- Cabeçalho do Pedido (DETALHES) -->
                    <div class="border-b-4 border-black pb-4 mb-6 flex justify-between items-center bg-gray-100 p-4 rounded">
                        <div>
                            <h1 class="text-2xl font-black text-gray-800 uppercase">Pedido #{{ $pedido->id }}</h1>
                            <p class="text-gray-600 font-bold text-sm">Cliente: <span class="text-indigo-700 uppercase">{{ $pedido->cliente->nome_razao_social }}</span></p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-black text-gray-500 uppercase">Entrega Solicitada:</p>
                            <p class="text-xl font-black text-red-600 font-mono">
                                {{ \Carbon\Carbon::parse($pedido->data_entrega_solicitada)->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>

                    <!-- 1. O QUE ENTREGAR (DEMANDA) -->
                    <h3 class="font-black text-lg text-gray-800 mb-4 border-l-4 border-blue-500 pl-3">1. O que precisamos entregar? (Demanda Final)</h3>
                    <div class="overflow-x-auto mb-8 border-2 border-black rounded-lg">
                        <table class="min-w-full text-sm">
                            <thead class="bg-blue-600 text-white uppercase text-[10px] tracking-widest">
                                <tr>
                                    <th class="py-3 px-6 text-left">Produto Acabado</th>
                                    <th class="py-3 px-6 text-center">Volume Solicitado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y-2 divide-gray-100">
                                @foreach($pedido->itens as $item)
                                    <tr class="hover:bg-blue-50 transition">
                                        <td class="py-3 px-6 text-left font-black text-gray-800 uppercase">{{ $item->produto->nome }}</td>
                                        <td class="py-3 px-6 text-center font-black text-2xl text-blue-800 bg-blue-50">
                                            {{ $item->quantidade }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- 2. EXPLOSÃO DE MATERIAIS (MRP) -->
                    <h3 class="font-black text-lg text-gray-800 mb-4 border-l-4 border-orange-500 pl-3">2. O que precisamos para fabricar? (Explosão de Insumos)</h3>
                    
                    @if(empty($necessidades))
                        <div class="bg-yellow-100 border-4 border-yellow-500 p-4 mb-8 rounded-lg shadow-md">
                            <p class="font-black text-yellow-800 text-sm">⚠️ ATENÇÃO:</p>
                            <p class="text-yellow-700 text-xs mt-1">Nenhuma matéria-prima foi calculada. Verifique se os produtos possuem "Estrutura (BOM)" cadastrada pelo Professor.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto mb-8 border-2 border-black rounded-lg">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-800 text-white uppercase text-[10px] tracking-widest">
                                    <tr>
                                        <th class="py-3 px-6 text-left">Código (SKU)</th>
                                        <th class="py-3 px-6 text-left">Matéria-Prima</th>
                                        <th class="py-3 px-6 text-center">Qtd. Total Necessária</th>
                                        <th class="py-3 px-6 text-center">Unidade</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y-2 divide-gray-100">
                                    @foreach($necessidades as $material)
                                        <tr class="hover:bg-yellow-50 transition">
                                            <td class="py-3 px-6 text-left font-mono font-bold text-gray-500">{{ $material['sku'] }}</td>
                                            <td class="py-3 px-6 text-left font-black text-gray-800 uppercase">{{ $material['nome'] }}</td>
                                            <td class="py-3 px-6 text-center text-2xl text-purple-700 font-black bg-gray-50">
                                                {{ $material['quantidade'] }}
                                            </td>
                                            <td class="py-3 px-6 text-center font-bold text-gray-700 uppercase">{{ $material['unidade'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Botão de Ação: Gerar Ordens (Com Confirmação) -->
                        <div class="flex justify-end mt-6">
                            <x-modal-confirmacao 
                                id="gerar-ordens-{{ $pedido->id }}"
                                title="INICIAR PRODUÇÃO?"
                                message="Confirmar geração de Ordens de Produção e Compra baseadas nesta análise?"
                                action="{{ route('aluno.pcp.gerar_ordens', $pedido->id) }}"
                                method="POST"
                                type="success" {{-- Verde para ação de sucesso --}}
                                buttonText="SIM, GERAR AGORA"
                                class="bg-green-600 hover:bg-green-700 text-white font-black py-4 px-12 rounded border-2 border-black shadow-[6px_6px_0px_0px_black] active:shadow-none active:translate-y-[2px] transition uppercase tracking-widest text-lg flex items-center gap-3"
                            >
                                <span>⚙️</span> Confirmar e Gerar Ordens
                            </x-modal-confirmacao>
                        </div>
                    @endif

                    {{-- Link Voltar (Removido o "sem fazer nada" que é redundante) --}}
                    <div class="mt-8 text-center border-t-2 border-dashed border-gray-200 pt-6">
                        <a href="{{ Auth::user()->tipo == 'professor' ? route('professor.monitoramento.pcp', $pedido->turma_id) : route('aluno.pcp.dashboard') }}" 
                           class="text-gray-500 hover:text-red-600 font-bold text-xs uppercase tracking-wider underline">
                            ← Voltar
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>