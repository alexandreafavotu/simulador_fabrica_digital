<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span class="text-3xl">📝</span> {{ __('Conferência de Expedição') }}
            </h2>

            {{-- BOTÃO VOLTAR --}}
            @if(Auth::user()->tipo == 'professor')
                <a href="{{ route('professor.monitoramento.expedicao', $pedido->turma_id) }}" 
                   class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                    ⬅ Voltar à Expedição
                </a>
            @else
                <button onclick="history.back()" 
                   class="bg-white text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                    ⬅ Cancelar
                </button>
            @endif
        </div>
    </x-slot>

    <div class="py-12 bg-yellow-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            {{-- CARD PRINCIPAL --}}
            <div class="bg-white border-4 border-black rounded-xl shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                
                {{-- CABEÇALHO DO PEDIDO --}}
                <div class="bg-indigo-600 text-white p-6 border-b-4 border-black flex justify-between items-center">
                    <div>
                        <h3 class="text-3xl font-black uppercase tracking-tighter">Pedido #{{ $pedido->id }}</h3>
                        <p class="text-sm font-bold text-indigo-200 mt-1 uppercase">
                            Cliente: <span class="text-white">{{ $pedido->cliente->nome_razao_social }}</span>
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="bg-white text-indigo-800 border-2 border-black text-xs font-black px-4 py-2 rounded-full uppercase tracking-widest shadow-sm">
                            Em Conferência
                        </span>
                    </div>
                </div>

                <div class="p-8 text-gray-900">
                    {{-- ADICIONEI O ID "form-faturar" AQUI --}}
                    <form id="form-faturar" action="{{ route('aluno.expedicao.faturar', $pedido->id) }}" method="POST">
                        @csrf
                        
                        <div class="mb-8">
                            <h4 class="font-black text-lg text-gray-800 uppercase mb-4 flex items-center gap-2">
                                <span>📦</span> Itens e Disponibilidade
                            </h4>
                            
                            <div class="overflow-x-auto border-2 border-black rounded-lg">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-black text-white uppercase text-[10px] tracking-widest">
                                        <tr>
                                            <th class="py-4 px-4 text-left">Produto</th>
                                            <th class="py-4 px-4 text-center">Solicitado</th>
                                            <th class="py-4 px-4 text-center">Estoque Real</th>
                                            <th class="py-4 px-4 text-center">A Faturar</th>
                                            <th class="py-4 px-4 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y-2 divide-gray-100">
                                        @foreach($pedido->itens as $item)
                                            <tr class="hover:bg-yellow-50 transition font-medium">
                                                <td class="py-4 px-4 font-black text-gray-800 uppercase">
                                                    {{ $item->produto->nome }}
                                                </td>
                                                
                                                <td class="py-4 px-4 text-center text-gray-500 font-bold">
                                                    {{ $item->quantidade }}
                                                </td>
                                                
                                                <td class="py-4 px-4 text-center">
                                                    <span class="font-black text-lg {{ $item->tem_saldo ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ $item->estoque_atual }}
                                                    </span>
                                                </td>
                                                
                                                <td class="py-4 px-4 text-center">
                                                    <input type="number" name="qtd_faturar[{{ $item->id }}]" 
                                                           value="{{ min($item->quantidade, $item->estoque_atual) }}" 
                                                           max="{{ $item->estoque_atual }}" min="0"
                                                           class="w-24 text-center border-2 border-black rounded bg-gray-100 font-black text-indigo-700 focus:ring-0 cursor-not-allowed shadow-inner" 
                                                           readonly>
                                                </td>
                                                
                                                <td class="py-4 px-4 text-center">
                                                    @if($item->estoque_atual >= $item->quantidade)
                                                        <span class="inline-block bg-green-100 text-green-700 border-2 border-green-500 px-2 py-1 rounded text-[10px] font-black uppercase">
                                                            ✔ Completo
                                                        </span>
                                                    @elseif($item->estoque_atual > 0)
                                                        <span class="inline-block bg-orange-100 text-orange-700 border-2 border-orange-500 px-2 py-1 rounded text-[10px] font-black uppercase">
                                                            ⚠ Parcial
                                                        </span>
                                                    @else
                                                        <span class="inline-block bg-red-100 text-red-700 border-2 border-red-500 px-2 py-1 rounded text-[10px] font-black uppercase">
                                                            ❌ Sem Estoque
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-2 flex items-start gap-2 bg-blue-50 p-3 rounded border-l-4 border-blue-500">
                                <span class="text-xl">ℹ️</span>
                                <p class="text-xs text-blue-800 font-bold mt-1">
                                    O sistema ajusta automaticamente a quantidade a faturar baseada no saldo físico disponível no armazém (WMS).
                                </p>
                            </div>
                        </div>

                        <div class="flex justify-end gap-4 border-t-4 border-black bg-gray-50 -m-8 p-6 mt-0">
                            <a href="{{ route('aluno.expedicao.dashboard') }}" 
                               class="px-6 py-3 bg-white text-gray-700 border-2 border-black rounded font-black text-xs uppercase hover:bg-gray-200 transition shadow-[4px_4px_0px_0px_rgba(0,0,0,0.1)] active:shadow-none active:translate-y-[1px]">
                                Cancelar
                            </a>
                            
                            {{-- BOTÃO QUE ABRE O MODAL (Mudei de submit para button e adicionei onclick) --}}
                            <button type="button" 
                                    onclick="document.getElementById('modal-confirmar-fatura').showModal()"
                                    class="px-8 py-3 bg-green-600 text-white rounded border-2 border-black font-black text-sm uppercase hover:bg-green-700 shadow-[4px_4px_0px_0px_black] active:shadow-none active:translate-y-[1px] transition flex items-center gap-2">
                                <span>📄</span> Emitir Nota Fiscal
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- MODAL INDUSTRIAL DE CONFIRMAÇÃO --}}
    <dialog id="modal-confirmar-fatura" class="p-0 rounded-xl shadow-[0_0_0_100vw_rgba(0,0,0,0.6)] bg-transparent backdrop:bg-black/50 w-[90%] max-w-md open:animate-fade-in z-50">
        <div class="bg-white border-4 border-black rounded-xl shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
            <div class="bg-green-600 text-white p-4 border-b-4 border-black flex items-center gap-3">
                <span class="text-3xl">✅</span>
                <h3 class="font-black text-xl text-white uppercase tracking-tighter">Confirmação de Saída</h3>
            </div>
            
            <div class="p-6 bg-white">
                <p class="text-lg font-bold text-gray-800 leading-tight">
                    Você está prestes a emitir a Nota Fiscal.
                </p>
                <div class="mt-4 bg-green-50 border-l-4 border-green-500 p-3">
                    <p class="text-sm font-bold text-green-800">
                        O estoque será baixado automaticamente e a operação fiscal será registrada.
                    </p>
                </div>
                <p class="mt-4 text-sm font-bold text-gray-500 uppercase tracking-wide">Deseja prosseguir?</p>
            </div>

            <div class="p-4 bg-gray-50 border-t-4 border-black flex justify-end gap-3">
                <button onclick="document.getElementById('modal-confirmar-fatura').close()" 
                        class="px-4 py-2 text-gray-600 font-bold hover:bg-gray-200 rounded uppercase border-2 border-transparent hover:border-gray-300 transition">
                    Cancelar
                </button>
                
                {{-- Botão que envia o formulário principal --}}
                <button onclick="document.getElementById('form-faturar').submit()" 
                        class="bg-green-600 text-white px-6 py-2 rounded border-2 border-black font-black uppercase hover:bg-green-700 shadow-[4px_4px_0px_0px_black] active:shadow-none active:translate-y-[2px] transition">
                    CONFIRMAR EMISSÃO
                </button>
            </div>
        </div>
    </dialog>
</x-app-layout>