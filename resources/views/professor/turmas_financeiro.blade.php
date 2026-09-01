<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl uppercase tracking-tighter flex items-center gap-2">
                <span>💰</span> Gestão Financeira: {{ $turma->nome_empresa }}
            </h2>
            <a href="{{ route('professor.dashboard') }}" 
               class="bg-white border-4 border-black px-4 py-2 font-black uppercase text-xs shadow-[4px_4px_0px_0px_black] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition-all flex items-center gap-2">
                ⬅ Voltar ao Painel
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-yellow-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4">

            {{-- Alertas de Feedback --}}
            @if(session('success'))
                <div class="bg-green-500 text-white p-4 border-4 border-black mb-6 font-black uppercase shadow-[4px_4px_0px_0px_black]">
                    ✅ {{ session('success') }}
                </div>
            @endif

            {{-- Card Principal: Saldo Atual --}}
            <div class="bg-white border-4 border-black p-10 shadow-[10px_10px_0px_0px_black] mb-8 text-center">
                <h3 class="text-xl font-black uppercase text-gray-400 tracking-widest">Saldo Atual em Caixa</h3>
                <p class="text-7xl font-black text-green-600 mt-2">
                    R$ {{ number_format($turma->capital_atual, 2, ',', '.') }}
                </p>
            </div>

            {{-- Card de Ações: Injeção ou Retirada --}}
            <div class="bg-indigo-600 border-4 border-black p-8 shadow-[12px_12px_0px_0px_black]">
                <div class="flex items-center gap-3 mb-6">
                    <span class="text-3xl">🗄️</span>
                    <h3 class="text-white font-black uppercase text-2xl italic tracking-tighter">Terminal de Movimentação de Capital</h3>
                </div>

                <form action="{{ route('professor.turmas.injetar_capital', $turma->id) }}" method="POST">
                    @csrf 
                    @method('PUT')
                    
                    <div class="space-y-6">
                        {{-- Campo de Valor --}}
                        <div>
                            <label class="block text-white font-black uppercase text-xs mb-2 italic">Informe o valor da transação (R$)</label>
                            <input type="number" name="valor_injeção" step="0.01" required placeholder="0,00"
                                   class="w-full border-4 border-black p-5 text-4xl font-black focus:ring-0 focus:border-yellow-400 placeholder-gray-300">
                        </div>

                        {{-- Botões de Ação --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Botão de Soma --}}
                            <button type="submit" name="acao" value="adicionar" 
                                    class="bg-yellow-400 border-4 border-black py-5 font-black uppercase text-xl shadow-[6px_6px_0px_0px_black] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
                                ➕ Confirmar Depósito
                            </button>

                            {{-- Botão de Subtração --}}
                            <button type="submit" name="acao" value="retirar" 
                                    class="bg-red-500 text-white border-4 border-black py-5 font-black uppercase text-xl shadow-[6px_6px_0px_0px_black] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
                                ➖ Retirar do Caixa
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Aviso Rodapé --}}
                <div class="mt-8 pt-4 border-t-2 border-indigo-500 border-dashed text-indigo-200 text-[10px] uppercase font-bold text-center italic">
                    * Todas as movimentações alteram instantaneamente o poder de compra e o lucro da holding no simulador.
                </div>
            </div>
        </div>
    </div>
</x-app-layout>