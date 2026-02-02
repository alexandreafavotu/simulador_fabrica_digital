<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span class="text-3xl">🚫</span> {{ __('Recusa de Recebimento') }}
            </h2>
            
            {{-- BOTÃO VOLTAR INTELIGENTE --}}
            <button onclick="history.back()" 
               class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                ⬅ Voltar
            </button>
        </div>
    </x-slot>

    <div class="py-12 bg-yellow-50 min-h-screen">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            
            {{-- CARD DE ALERTA (ESTILO INDUSTRIAL) --}}
            <div class="bg-white border-4 border-black rounded-xl shadow-[10px_10px_0px_0px_rgba(220,38,38,1)] overflow-hidden">
                
                {{-- Cabeçalho Vermelho --}}
                <div class="bg-red-600 text-white p-4 border-b-4 border-black flex items-center gap-3">
                    <span class="text-3xl">⚠️</span>
                    <div>
                        <h3 class="font-black text-xl uppercase tracking-tighter">Devolução de Mercadoria</h3>
                        <p class="text-xs text-red-200 font-bold uppercase">Registro de Não-Conformidade</p>
                    </div>
                </div>

                <div class="p-8">
                    
                    {{-- Aviso --}}
                    <div class="bg-red-50 border-2 border-red-200 p-4 rounded-lg mb-8 flex gap-3 items-start">
                        <span class="text-2xl">📢</span>
                        <p class="text-sm font-bold text-red-800 leading-tight mt-1">
                            Atenção Operador: Ao confirmar esta ação, o material será devolvido e o setor de Compras será notificado automaticamente para reposição.
                        </p>
                    </div>

                    <!-- Resumo do Item (Ficha Técnica) -->
                    <div class="bg-gray-100 border-2 border-black p-5 rounded-lg mb-8 relative">
                        <span class="absolute -top-3 left-4 bg-black text-white text-[10px] font-black px-2 py-1 uppercase tracking-widest border border-white">
                            Item Rejeitado
                        </span>

                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <p class="text-[10px] text-gray-500 font-black uppercase">Material</p>
                                <p class="font-black text-2xl text-gray-800 uppercase">{{ $compra->materiaPrima->nome }}</p>
                            </div>
                            <div class="border-t-2 border-dashed border-gray-300 pt-2">
                                <p class="text-[10px] text-gray-500 font-black uppercase">Fornecedor Responsável</p>
                                <p class="font-bold text-gray-700">{{ $compra->fornecedor->nome_razao_social ?? 'Não Identificado' }}</p>
                            </div>
                        </div>
                    </div>

                 <form action="{{ route('aluno.almoxarifado.salvar-recusa', $compra->id) }}" method="POST">
                        @csrf
                        
                        {{-- BLOCO DE INCONFORMIDADE PROGRAMADA (PÁRA-QUEDAS DO PROFESSOR) --}}
                        @if($compra->tem_inconformidade)
                            <div class="mb-8 bg-black text-white p-5 border-4 border-red-600 shadow-[6px_6px_0px_0px_rgba(220,38,38,1)] relative">
                                <span class="absolute -top-3 left-4 bg-red-600 text-white text-[10px] font-black px-2 py-0.5 uppercase tracking-widest border-2 border-black">
                                    Laudo do Controle de Qualidade
                                </span>
                                <div class="flex items-start gap-3 mt-1">
                                    <span class="text-3xl animate-pulse">📋</span>
                                    <div>
                                        <p class="text-xs font-black uppercase text-red-500 leading-none mb-1">Ocorrência Detectada:</p>
                                        <p class="text-sm font-bold text-gray-100 italic">"{{ $compra->descricao_inconformidade }}"</p>
                                    </div>
                                </div>
                                <p class="text-[9px] font-bold text-gray-400 mt-4 uppercase border-t border-gray-800 pt-2 italic">
                                    * Instrução: Descreva os detalhes desta ocorrência no campo abaixo para processar a devolução.
                                </p>
                            </div>
                        @endif

                        <div class="mb-8">
                            <label class="block text-xs font-black text-gray-700 uppercase mb-2">Motivo Técnico da Recusa:</label>
                            <textarea name="motivo_recusa" rows="4" 
                                      class="block w-full border-2 border-black rounded-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,0.1)] focus:border-red-600 focus:ring-0 p-3 font-bold text-gray-700" 
                                      placeholder="{{ $compra->tem_inconformidade ? 'Descreva o problema baseado no laudo acima...' : 'Ex: Embalagem violada, produto vencido...' }}" required></textarea>
                        </div>

                        <div class="flex justify-end gap-4 pt-4 border-t-2 border-black">
                            <a href="{{ route('aluno.almoxarifado.dashboard') }}" 
                               class="px-6 py-3 bg-white text-gray-800 border-2 border-black rounded font-black text-xs uppercase hover:bg-gray-100 transition shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-y-[1px] active:shadow-none flex items-center">
                                Cancelar
                            </a>
                            
                            <button type="submit" class="px-6 py-3 bg-red-600 text-white border-2 border-black rounded font-black text-xs uppercase hover:bg-red-700 shadow-[4px_4px_0px_0px_black] active:translate-y-[2px] active:shadow-none transition flex items-center gap-2">
                                <span>✖️</span> Confirmar Recusa
                            </button>
                        </div>
                    </form>   

                </div>
            </div>
        </div>
    </div>
</x-app-layout>