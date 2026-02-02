<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span>🏢</span> PERFIL CORPORATIVO: <span class="text-indigo-600">{{ $turma->nome_empresa }}</span>
            </h2>
            
            {{-- Botão Voltar Inteligente --}}
            @if(Auth::user()->tipo == 'professor')
                <a href="{{ route('professor.dashboard') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_black] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase">
                    ⬅ Voltar ao Painel
                </a>
            @else
                <button onclick="history.back()" class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_black] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase">
                    ⬅ Voltar
                </button>
            @endif
        </div>
    </x-slot>

    <div class="py-12 bg-yellow-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            {{-- FICHA DA EMPRESA --}}
            <div class="bg-white border-4 border-black rounded-xl shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                
                {{-- Cabeçalho Estilizado --}}
                <div class="bg-indigo-600 text-white p-8 border-b-4 border-black relative">
                    <div class="absolute top-4 right-4 opacity-20 text-6xl font-black italic uppercase">EMPRESA</div>
                    <p class="text-xs font-bold uppercase tracking-[0.3em] opacity-80 mb-1">Registro de Atividade Industrial</p>
                    <h1 class="text-4xl font-black tracking-tighter uppercase">{{ $turma->nome_empresa }}</h1>
                    <div class="mt-4 flex gap-4">
                        <span class="bg-white text-indigo-900 px-3 py-1 rounded border-2 border-black font-black text-xs uppercase">{{ $turma->setor ?? 'Setor não definido' }}</span>
                        <span class="bg-indigo-400 text-white px-3 py-1 rounded border-2 border-indigo-900 font-bold text-xs uppercase">{{ $turma->segmento ?? 'Segmento não definido' }}</span>
                    </div>
                </div>

                <div class="p-8 space-y-8">
                    
                    {{-- MISSÃO E RESUMO --}}
                    <div>
                        <h4 class="font-black text-xs text-gray-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                            <span>📝</span> Resumo e Missão
                        </h4>
                        <div class="bg-gray-50 border-2 border-black p-4 rounded-lg italic text-gray-700 leading-relaxed">
                            "{{ $turma->resumo ?? 'Nenhuma descrição cadastrada para esta unidade fabril.' }}"
                        </div>
                    </div>

                    {{-- DADOS TÉCNICOS --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="border-2 border-black p-4 rounded-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] bg-white">
                            <p class="text-[10px] font-black text-gray-400 uppercase">Número do CNPJ</p>
                            <p class="text-lg font-mono font-black text-gray-800">{{ $turma->cnpj ?? '---' }}</p>
                        </div>
                        <div class="border-2 border-black p-4 rounded-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] bg-white">
                            <p class="text-[10px] font-black text-gray-400 uppercase">Contato Telefônico</p>
                            <p class="text-lg font-black text-gray-800">{{ $turma->telefone ?? '---' }}</p>
                        </div>
                    </div>

                    {{-- LOCALIZAÇÃO SEDE --}}
                    <div class="bg-gray-100 border-4 border-black p-6 rounded-xl relative overflow-hidden">
                        <div class="absolute right-[-10px] bottom-[-10px] opacity-10 text-6xl">📍</div>
                        <h4 class="font-black text-sm text-gray-800 uppercase mb-4 border-b-2 border-gray-300 pb-2">Endereço da Unidade</h4>
                        
                        <div class="space-y-2">
                            <p class="text-lg font-bold text-gray-800">
                                {{ $turma->rua ?? 'Rua não cadastrada' }}, {{ $turma->numero ?? 's/n' }}
                            </p>
                            <p class="text-sm font-medium text-gray-600 uppercase">
                                Bairro: {{ $turma->bairro ?? '---' }}
                            </p>
                            <div class="flex gap-4">
                                <div class="flex-1 bg-white border-2 border-black p-2 rounded">
                                    <p class="text-[9px] font-black text-gray-400 uppercase leading-none">Cidade</p>
                                    <p class="text-sm font-black text-gray-800">{{ $turma->cidade ?? '---' }}</p>
                                </div>
                                <div class="w-20 bg-white border-2 border-black p-2 rounded text-center">
                                    <p class="text-[9px] font-black text-gray-400 uppercase leading-none">Estado</p>
                                    <p class="text-sm font-black text-gray-800 uppercase">{{ $turma->estado ?? '--' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RODAPÉ DA FICHA --}}
                    <div class="pt-6 border-t-2 border-dashed border-gray-200 flex justify-between items-center opacity-50">
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Sistema Fábrica-Digital v1.0 • Registro Eletrônico</p>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">ID: #{{ $turma->id }}</p>
                    </div>
                </div>
            </div>

            {{-- Aviso de Segurança para o Aluno --}}
            @if(Auth::user()->tipo == 'aluno')
                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-400 font-bold italic">
                        ⚠️ Visualização restrita. Alterações apenas via Gerência (Professor).
                    </p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>