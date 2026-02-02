<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span>📋</span> ESCALAÇÃO: <span class="text-indigo-600 uppercase">{{ $turma->nome }}</span>
            </h2>
            <button onclick="history.back()" 
               class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                ⬅ Voltar
            </button>
        </div>
    </x-slot>

    <div class="py-12 bg-yellow-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                {{-- COLUNA ESQUERDA: BANCO DE TALENTOS (DISPONÍVEIS) --}}
                <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] overflow-hidden flex flex-col">
                    <div class="p-4 bg-indigo-600 text-white border-b-4 border-black">
                        <h3 class="font-black text-lg uppercase tracking-widest flex items-center gap-2">
                            <span>📚</span> Alunos Disponíveis
                        </h3>
                        <p class="text-[10px] font-bold text-indigo-200 uppercase">Selecione para escalar nesta fábrica</p>
                    </div>

                    <div class="p-4 h-[500px] overflow-y-auto bg-gray-50 custom-scrollbar">
                        @if($disponiveis->isEmpty())
                            <div class="flex flex-col items-center justify-center h-full text-center p-6">
                                <span class="text-5xl mb-2 opacity-20">👥</span>
                                <p class="text-gray-400 font-bold uppercase text-xs">Todos os alunos cadastrados já estão escalados ou não há registros.</p>
                                <a href="{{ route('professor.global.alunos') }}" class="mt-4 text-indigo-600 font-black text-xs underline uppercase">Cadastrar novos alunos</a>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach($disponiveis as $user)
                                    <div class="bg-white border-2 border-black p-3 rounded-lg shadow-[3px_3px_0px_0px_black] flex justify-between items-center hover:bg-indigo-50 transition group">
                                        <div>
                                            <p class="text-sm font-black text-gray-800 uppercase leading-none">{{ $user->name }}</p>
                                            <p class="text-[10px] font-bold text-gray-400 mt-1 uppercase">{{ $user->curso }} - {{ $user->ano_letivo }}</p>
                                        </div>
                                        <form action="{{ route('professor.turmas.vincular', $turma->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-black py-1.5 px-3 rounded border-2 border-black shadow-[2px_2px_0px_0px_black] active:shadow-none active:translate-y-[1px] transition text-[10px] uppercase">
                                                Escalar ➔
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- COLUNA DIREITA: TIME ESCALADO (NA TURMA) --}}
                <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] overflow-hidden flex flex-col">
                    <div class="p-4 bg-yellow-400 text-black border-b-4 border-black">
                        <div class="flex justify-between items-center">
                            <h3 class="font-black text-lg uppercase tracking-widest flex items-center gap-2">
                                <span>🚀</span> Equipe Ativa
                            </h3>
                            <span class="bg-black text-white text-[10px] font-black px-2 py-1 rounded">
                                {{ $alunosNaTurma->count() }} OPERADORES
                            </span>
                        </div>
                    </div>

                    <div class="p-4 h-[500px] overflow-y-auto bg-white custom-scrollbar">
                        @if($alunosNaTurma->isEmpty())
                            <div class="flex flex-col items-center justify-center h-full text-center border-4 border-dashed border-gray-100 rounded-xl">
                                <span class="text-6xl mb-2 opacity-10">🏭</span>
                                <p class="text-gray-300 font-black uppercase text-sm">Fábrica sem operários.</p>
                                <p class="text-gray-300 text-[10px] uppercase">Adicione alunos da lista ao lado para iniciar.</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($alunosNaTurma as $aluno)
                                    <div class="bg-white border-2 border-black p-4 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,0.1)] flex flex-col gap-3 relative overflow-hidden">
                                        {{-- Barra lateral de setor --}}
                                        <div class="absolute left-0 top-0 bottom-0 w-2 
                                            @if($aluno->setor == 'indefinido') bg-red-500 @else bg-indigo-500 @endif border-r-2 border-black">
                                        </div>

                                        <div class="flex justify-between items-start pl-2">
                                            <div>
                                                <h4 class="font-black text-gray-900 uppercase leading-none">{{ $aluno->user->name ?? 'Usuário Removido' }}</h4>
                                                <p class="text-[10px] font-mono text-gray-400 mt-1">{{ $aluno->user->email ?? '---' }}</p>
                                            </div>
{{-- BOTÃO REMOVER ALUNO COM DESIGN INDUSTRIAL --}}
<x-modal-confirmacao 
    id="rm-aluno-{{ $aluno->id }}"
    title="REMOVER ALUNO?"
    message="Você deseja retirar {{ $aluno->user->name }} desta fábrica?"
    action="{{ route('professor.turmas.desvincular', $aluno->id) }}"
    method="DELETE"
    buttonText="Sim, Remover"
    class="text-red-500 hover:text-red-700 font-black text-xs p-1"
>
    ✕
</x-modal-confirmacao>
                                            
                                        </div>

                                        {{-- Seletor de Cargo Industrial --}}
                                        <div class="pl-2">
                                            <form action="{{ route('professor.turmas.setor', $aluno->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <label class="block text-[9px] font-black text-gray-400 uppercase mb-1">Setor Alocado</label>
                                                <select name="setor" onchange="this.form.submit()" 
                                                        class="w-full border-2 border-black rounded font-black text-xs uppercase p-1.5 focus:ring-0 
                                                        {{ $aluno->setor == 'indefinido' ? 'bg-red-50 text-red-600' : 'bg-gray-50 text-indigo-700' }}">
                                                    <option value="indefinido" {{ $aluno->setor == 'indefinido' ? 'selected' : '' }}>-- DEFINIR CARGO --</option>
                                                    <option value="pcp" {{ $aluno->setor == 'pcp' ? 'selected' : '' }}>PCP (Planejamento)</option>
                                                    <option value="compras" {{ $aluno->setor == 'compras' ? 'selected' : '' }}>COMPRAS</option>
                                                    <option value="almoxarifado" {{ $aluno->setor == 'almoxarifado' ? 'selected' : '' }}>ALMOXARIFADO / WMS</option>
                                                    <option value="producao" {{ $aluno->setor == 'producao' ? 'selected' : '' }}>CHÃO DE FÁBRICA</option>
                                                    <option value="embalagem" {{ $aluno->setor == 'embalagem' ? 'selected' : '' }}>EMBALAGEM / CONFERÊNCIA</option>
                                                    <option value="expedicao" {{ $aluno->setor == 'expedicao' ? 'selected' : '' }}>EXPEDIÇÃO</option>
                                                    <option value="vendas" {{ $aluno->setor == 'vendas' ? 'selected' : '' }}>VENDAS / CRM</option>
                                                </select>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #000; border-radius: 10px; }
    </style>
</x-app-layout>