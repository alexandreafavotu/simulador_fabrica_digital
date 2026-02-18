<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center text-gray-800">
            <h2 class="font-black text-2xl leading-tight flex items-center gap-2">
                <span class="animate-pulse flex items-center justify-center">
    @if(Auth::user()->acessibilidade_visual)
        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="2" style="filter:none!important; stroke:#ffff00!important"><rect x="2" y="7" width="20" height="15" rx="2" ry="2"></rect><polyline points="17 2 12 7 7 2"></polyline></svg>
    @else
        <span class="text-3xl">📺</span>
    @endif
</span> {{ __('Gestão à Vista') }}
            </h2>
            
            <div class="flex items-center gap-4">
                {{-- BOTÃO VOLTAR (SÓ PARA O PROFESSOR) --}}
                @if(Auth::user()->tipo == 'professor')
                    <a href="{{ route('professor.monitoramento.index', $aluno->turma_id) }}" 
                       class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2 mr-4">
                        @if(Auth::user()->acessibilidade_visual)
    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="4" style="filter:none!important; stroke:#ffff00 !important;"><path d="M19 12H5M12 19l-7-7 7-7"></path></svg>
@else
    ⬅
@endif 
Sair do Setor
                    </a>
                @endif

                {{-- RELÓGIO DO SISTEMA --}}
                <div class="bg-black text-green-400 px-4 py-2 rounded border-2 border-gray-600 font-mono text-sm shadow-[4px_4px_0px_0px_black]">
                    <span class="text-[10px] block text-gray-400 uppercase leading-none font-black">Data Simulação</span>
                    {{ $dataJogo->format('d/m/Y H:i') }}
                </div>
                
                {{-- BOTÃO RECARREGAR --}}
                <button onclick="window.location.reload()" 
        class="border-2 border-black p-2 rounded shadow-[3px_3px_0px_0px_black] hover:translate-x-[1px] hover:translate-y-[1px] transition 
        {{ Auth::user()->acessibilidade_visual ? 'bg-black border-yellow-400' : 'bg-yellow-400 active:bg-yellow-500' }}">
    @if(Auth::user()->acessibilidade_visual)
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="3" style="filter:none!important; stroke:#ffff00!important"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
    @else
        🔄
    @endif
</button>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-yellow-50 min-h-screen" x-data="{ activeOrder: null }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-16">
            
            {{-- SEÇÃO 1: PEDIDOS ATIVOS --}}
            <section class="relative z-30"> {{-- Z-INDEX ALTO PARA MODAL NÃO FICAR ATRÁS --}}
                <div class="mb-4">
                    <h3 class="font-black text-xl uppercase tracking-tighter border-b-4 border-black inline-block">Monitor de Produção em Tempo Real</h3>
                </div>

                <div class="bg-white border-4 border-black shadow-[10px_10px_0px_0px_black]">
                    <table class="w-full text-left">
                        <thead class="bg-black text-white uppercase text-[11px] tracking-widest">
                            <tr>
                                <th class="px-4 py-4 w-24">ID</th>
                                <th class="px-4 py-4">Cliente</th>
                                <th class="px-4 py-4">Produto Acabado</th>
                                <th class="px-4 py-4 text-center">Qtd</th>
                                <th class="px-4 py-4 text-center">Status</th>
                                <th class="px-4 py-4 text-center">Entrega</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-black">
                            @foreach($pedidos as $pedido)
                                @foreach($pedido->itens as $item)
                                    @include('aluno.partials.gestao_vista_row', ['p' => $pedido, 'i' => $item, 'isHistory' => false])
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                    @if($pedidos->isEmpty())
                        <div class="py-12 text-center text-gray-400 font-black uppercase italic">Nenhuma operação ativa.</div>
                    @endif
                </div>
            </section>

            {{-- SEÇÃO 2: ARQUIVO MORTO --}}
            <section class="relative z-10"> {{-- Z-INDEX MAIS BAIXO --}}
                <div class="mb-4">
                    <h3 class="font-black text-xl uppercase tracking-tighter border-b-4 border-gray-400 inline-block text-gray-500 italic">Arquivo Morto (Histórico Concluído)</h3>
                </div>

                <div class="bg-gray-200 border-4 border-gray-400 shadow-[10px_10px_0px_0px_rgba(0,0,0,0.2)] grayscale-[0.5] hover:grayscale-0 transition-all duration-700">
                    <table class="w-full text-left">
                        <thead class="bg-gray-600 text-white uppercase text-[10px] tracking-widest">
                            <tr>
                                <th class="px-4 py-3 w-24">ID</th>
                                <th class="px-4 py-3">Cliente</th>
                                <th class="px-4 py-3">Produto Acabado</th>
                                <th class="px-4 py-3 text-center">Qtd</th>
                                <th class="px-4 py-3 text-center">Finalizado em</th>
                                <th class="px-4 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-300">
                            @foreach($historico as $pedido)
                                @foreach($pedido->itens as $item)
                                    @include('aluno.partials.gestao_vista_row', ['p' => $pedido, 'i' => $item, 'isHistory' => true])
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                    @if($historico->isEmpty())
                        <div class="py-12 text-center text-gray-400 font-black uppercase italic border-t border-gray-300">Nenhum faturamento registrado.</div>
                    @endif
                </div>
            </section>

        </div>
    </div>

    <style> [x-cloak] { display: none !important; } </style>
</x-app-layout>