<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span>🕵️</span> MONITORAMENTO: <span class="text-indigo-600 uppercase">{{ $turma->nome }}</span>
            </h2>
            <a href="{{ route('professor.dashboard') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                ⬅ Voltar ao Painel Geral
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-yellow-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white border-4 border-black rounded-xl shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] overflow-hidden p-8">
                
                <h3 class="font-black text-xl text-gray-800 mb-8 uppercase tracking-widest border-b-4 border-black pb-2 flex items-center gap-2">
                    <span class="text-3xl">👁️</span> Centro de Espionagem (God View)
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- 1. TORRE DE CONTROLE (BI) - ESTE É O BLOCO NOVO --}}
                    <a href="{{ route('professor.monitoramento.bi', $turma->id) }}" class="group bg-indigo-900 border-4 border-black p-6 rounded-xl hover:bg-indigo-800 transition hover:-translate-y-1 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] col-span-1 md:col-span-2 lg:col-span-3 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="text-5xl group-hover:scale-110 transition">🚀</div>
                            <div>
                                <h4 class="font-black text-xl text-white uppercase">Torre de Controle (BI)</h4>
                                <p class="text-xs font-bold text-indigo-300">Inteligência de Dados e KPIs</p>
                            </div>
                        </div>
                        <div class="bg-indigo-600 text-white px-4 py-2 rounded font-black text-xs uppercase shadow-md border border-indigo-400">
                            Acessar Dashboard
                        </div>
                    </a>

                    {{-- GESTÃO À VISTA (NOVO CARD) --}}
                    <a href="{{ route('aluno.gestao_vista', $turma->id) }}" class="group bg-yellow-400 border-4 border-black p-6 rounded-xl hover:bg-yellow-500 transition hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_black] shadow-[4px_4px_0px_0px_black]">
                        <div class="flex items-center gap-4">
                            <div class="text-4xl group-hover:scale-110 transition">📺</div>
                            <div>
                                <h4 class="font-black text-lg text-black uppercase">Monitor</h4>
                                <p class="text-xs font-bold text-gray-800">Gestão à Vista (Andon)</p>
                            </div>
                        </div>
                    </a>

                
                    


                    {{-- VENDAS --}}

                    
                    <a href="{{ route('professor.monitoramento.vendas', $turma->id) }}" class="group bg-white border-4 border-black p-6 rounded-xl hover:bg-green-50 transition hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_rgba(34,197,94,1)] shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                        <div class="flex items-center gap-4">
                            <div class="text-4xl group-hover:scale-110 transition">💰</div>
                            <div>
                                <h4 class="font-black text-lg text-gray-800 uppercase">Vendas</h4>
                                <p class="text-xs font-bold text-gray-500">Pedidos e Faturamento</p>
                            </div>
                        </div>
                    </a>
                    

                    {{-- PCP --}}
                    <a href="{{ route('professor.monitoramento.pcp', $turma->id) }}" class="group bg-white border-4 border-black p-6 rounded-xl hover:bg-blue-50 transition hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_rgba(59,130,246,1)] shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                        <div class="flex items-center gap-4">
                            <div class="text-4xl group-hover:scale-110 transition">📊</div>
                            <div>
                                <h4 class="font-black text-lg text-gray-800 uppercase">PCP</h4>
                                <p class="text-xs font-bold text-gray-500">Planejamento e Ordens</p>
                            </div>
                        </div>
                    </a>

                    {{-- COMPRAS --}}
                    <a href="{{ route('professor.monitoramento.compras', $turma->id) }}" class="group bg-white border-4 border-black p-6 rounded-xl hover:bg-purple-50 transition hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_rgba(168,85,247,1)] shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                        <div class="flex items-center gap-4">
                            <div class="text-4xl group-hover:scale-110 transition">🛒</div>
                            <div>
                                <h4 class="font-black text-lg text-gray-800 uppercase">Compras</h4>
                                <p class="text-xs font-bold text-gray-500">Cotações e Recusas</p>
                            </div>
                        </div>
                    </a>

                    {{-- ALMOXARIFADO --}}
                    <a href="{{ route('professor.monitoramento.almoxarifado', $turma->id) }}" class="group bg-white border-4 border-black p-6 rounded-xl hover:bg-orange-50 transition hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_rgba(249,115,22,1)] shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                        <div class="flex items-center gap-4">
                            <div class="text-4xl group-hover:scale-110 transition">📦</div>
                            <div>
                                <h4 class="font-black text-lg text-gray-800 uppercase">WMS</h4>
                                <p class="text-xs font-bold text-gray-500">Estoque e Doca</p>
                            </div>
                        </div>
                    </a>

                    {{-- PRODUÇÃO --}}
                    <a href="{{ route('professor.monitoramento.producao', $turma->id) }}" class="group bg-white border-4 border-black p-6 rounded-xl hover:bg-yellow-50 transition hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_rgba(234,179,8,1)] shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                        <div class="flex items-center gap-4">
                            <div class="text-4xl group-hover:scale-110 transition">🏭</div>
                            <div>
                                <h4 class="font-black text-lg text-gray-800 uppercase">Produção</h4>
                                <p class="text-xs font-bold text-gray-500">Chão de Fábrica</p>
                            </div>
                        </div>
                    </a>

                    {{-- EMBALAGEM (NOVO) --}}
                    <a href="{{ route('professor.monitoramento.embalagem', $turma->id) }}" class="group bg-white border-4 border-black p-6 rounded-xl hover:bg-pink-50 transition hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_rgba(236,72,153,1)] shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                        <div class="flex items-center gap-4">
                            <div class="text-4xl group-hover:scale-110 transition">🎁</div>
                            <div>
                                <h4 class="font-black text-lg text-gray-800 uppercase">Embalagem</h4>
                                <p class="text-xs font-bold text-gray-500">Conferência e Lacre</p>
                            </div>
                        </div>
                    </a>

                    {{-- EXPEDIÇÃO --}}
                    <a href="{{ route('professor.monitoramento.expedicao', $turma->id) }}" class="group bg-white border-4 border-black p-6 rounded-xl hover:bg-teal-50 transition hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_rgba(20,184,166,1)] shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                        <div class="flex items-center gap-4">
                            <div class="text-4xl group-hover:scale-110 transition">🚚</div>
                            <div>
                                <h4 class="font-black text-lg text-gray-800 uppercase">Expedição</h4>
                                <p class="text-xs font-bold text-gray-500">Notas Fiscais</p>
                            </div>
                        </div>
                    </a>
                    

                    
                                      

                </div>

            </div>

        </div>
    </div>
</x-app-layout>