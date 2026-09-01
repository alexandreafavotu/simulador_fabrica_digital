<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span>🎓</span> {{ __('Painel de Avaliação por Competências') }}
            </h2>
            <a href="{{ route('professor.avaliacoes.index', ['curso' => $aluno->curso, 'ano_letivo' => $aluno->ano_letivo]) }}" 
               class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase">
                ⬅ Voltar à Lista
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-yellow-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- 1. IDENTIFICAÇÃO DO ALUNO (FICHA CADASTRAL) --}}
            <div class="bg-indigo-100 border-4 border-black p-6 rounded-xl shadow-[8px_8px_0px_0px_black] flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <span class="text-[10px] font-black text-indigo-800 uppercase tracking-widest leading-none">Estudante em Avaliação</span>
                    <h3 class="text-3xl font-black text-indigo-950 uppercase mt-1">{{ $aluno->name }}</h3>
                    <p class="text-xs font-bold text-indigo-700 mt-1">{{ $aluno->email }}</p>
                </div>
                <div class="text-left md:text-right bg-white p-3 border-2 border-black rounded shadow-[3px_3px_0px_0px_black] min-w-[200px]">
                    <span class="block text-[9px] font-black text-gray-500 uppercase leading-none">Curso Regulamentar</span>
                    <span class="block text-sm font-black text-gray-800 uppercase mt-1 truncate" title="{{ $aluno->curso }}">{{ $aluno->curso }}</span>
                    <span class="inline-block bg-indigo-50 text-indigo-700 border border-indigo-300 rounded px-1.5 py-0.5 text-[9px] font-black mt-1">
                        TURMA {{ $aluno->ano_letivo }}
                    </span>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- 2. PAINEL ANALÍTICO DE INTELIGÊNCIA (KPIs DO BI DO ESTUDANTE) --}}
            {{-- ========================================================================= --}}
            <div class="bg-white border-4 border-black rounded-xl p-6 shadow-[8px_8px_0px_0px_black] space-y-4">
                <h3 class="font-black text-lg text-gray-800 uppercase flex items-center gap-2 border-b-2 border-dashed border-gray-300 pb-2">
                    <span>📈</span> Indicadores de Performance Operacional (Rastreados pelo BI)
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    
                    {{-- CARD 1: LEAD TIMES --}}
                    <div class="bg-yellow-100 border-2 border-black p-4 rounded shadow-[3px_3px_0px_0px_black] flex flex-col justify-between">
                        <div>
                            <span class="text-[9px] font-black text-yellow-900 uppercase tracking-wider block">Lead Times Médios (Ciclos)</span>
                            <div class="mt-2 space-y-1 text-xs font-bold text-gray-700">
                                <div class="flex justify-between border-b border-yellow-200 pb-1">
                                    <span>🛒 Compras:</span>
                                    <span class="text-indigo-700 font-black">{{ number_format($bi['lead_time_compras'], 1, ',', '.') }} dias</span>
                                </div>
                                <div class="flex justify-between border-b border-yellow-200 pb-1">
                                    <span>🏭 Produção:</span>
                                    <span class="text-indigo-700 font-black">{{ number_format($bi['lead_time_producao'], 1, ',', '.') }} dias</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>💰 Vendas:</span>
                                    <span class="text-indigo-700 font-black">{{ number_format($bi['lead_time_vendas'], 1, ',', '.') }} dias</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-[9px] text-gray-500 italic mt-3 font-bold uppercase">Tempo médio de resposta no software</div>
                    </div>

                    {{-- CARD 2: DIAS DE ATRASO ACUMULADO --}}
                    <div class="bg-red-100 border-2 border-black p-4 rounded shadow-[3px_3px_0px_0px_black] flex flex-col justify-between">
                        <div>
                            <span class="text-[9px] font-black text-red-900 uppercase tracking-wider block">Risco de Entrega (Atrasos)</span>
                            <div class="text-center py-2">
                                <span class="text-4xl font-black text-red-700 leading-none">
                                    {{ number_format($bi['dias_atraso_acumulado'], 0, ',', '.') }}
                                </span>
                                <span class="block text-[10px] font-black uppercase text-red-900 mt-1">Dias Acumulados sob Gestão</span>
                            </div>
                        </div>
                        <p class="text-[9px] text-red-800 font-bold uppercase tracking-tight text-center bg-white/50 py-0.5 rounded border border-red-200">
                            {{ $bi['dias_atraso_acumulado'] > 0 ? '⚠️ Pedidos com prazo estourado' : '✅ Nenhuma pendência em atraso' }}
                        </p>
                    </div>

                    {{-- CARD 3: GARGALOS ATIVOS --}}
                    <div class="bg-purple-100 border-2 border-black p-4 rounded shadow-[3px_3px_0px_0px_black] flex flex-col justify-between">
                        <div>
                            <span class="text-[9px] font-black text-purple-900 uppercase tracking-wider block">Gargalos Ativos (Fila de Espera)</span>
                            <div class="mt-2 space-y-1 text-xs font-bold text-gray-700">
                                <div class="flex justify-between border-b border-purple-200 pb-1">
                                    <span>🛒 Compras Pendentes:</span>
                                    <span class="text-purple-800 font-black">{{ $bi['gargalos']['Compras'] }}</span>
                                </div>
                                <div class="flex justify-between border-b border-purple-200 pb-1">
                                    <span>🏭 OPs em Aberto:</span>
                                    <span class="text-purple-800 font-black">{{ $bi['gargalos']['Produção'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>📦 Itens sem Local WMS:</span>
                                    <span class="text-purple-800 font-black">{{ $bi['gargalos']['WMS'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-[9px] text-purple-900 font-black uppercase mt-3 tracking-wider text-right">
                            Total: {{ array_sum($bi['gargalos']) }} gargalos
                        </div>
                    </div>

                </div>
            </div>

            {{-- FORMULÁRIO DE AVALIAÇÃO DE CAPACIDADES --}}
            <form action="{{ route('professor.avaliacoes.salvar', $aluno->id) }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="curso" value="{{ $aluno->curso }}">
                <input type="hidden" name="ano_letivo" value="{{ $aluno->ano_letivo }}">

                @if($competencias->isEmpty())
                    <div class="bg-white border-4 border-dashed border-gray-400 p-10 rounded-xl text-center text-gray-500 italic font-bold">
                        ⚠️ Nenhuma capacidade cadastrada no Seeder para o curso "{{ $aluno->curso }}".
                    </div>
                @else
                    
                    {{-- LOOP DE COMPETÊNCIAS --}}
                    @foreach($competencias as $comp)
                        @php
                            $statusAtual = $comp->status_atual;
                            $observacaoAtual = $comp->observacao_atual;
                        @endphp

                        <div class="bg-white border-4 border-black rounded-xl shadow-[6px_6px_0px_0px_black] overflow-hidden">
                            
                            {{-- Cabeçalho da Capacidade --}}
                            <div class="p-4 border-b-2 border-black flex flex-col md:flex-row justify-between items-start md:items-center gap-2 
                                {{ $comp->categoria == 'tecnica' ? 'bg-green-50' : 'bg-purple-50' }}">
                                
                                <div>
                                    <span class="text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded border border-black bg-white
                                        {{ $comp->categoria == 'tecnica' ? 'text-green-800 border-green-300' : 'text-purple-800 border-purple-300' }}">
                                        {{ $comp->unidade_curricular ?: 'Competência Transversal' }}
                                    </span>
                                    <h4 class="font-black text-md text-gray-800 mt-1 uppercase leading-tight">{{ $comp->nome }}</h4>
                                </div>

                                {{-- Indicador de Tipo de Avaliação --}}
                                <div class="flex items-center gap-2">
                                    @if($comp->sugerido)
                                        <span class="bg-indigo-100 text-indigo-800 border border-indigo-300 px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-wide flex items-center gap-1 animate-pulse">
                                            💡 Sugestão ERP
                                        </span>
                                    @endif

                                    @if($comp->tipo_avaliacao == 'automatica')
                                        <span class="bg-blue-600 text-white border-2 border-black px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wide flex items-center gap-1 shadow-[2px_2px_0px_0px_black]" title="Métrica calculada de forma automatizada pelo ERP">
                                            📟 Métrica ERP
                                        </span>
                                    @else
                                        <span class="bg-yellow-400 text-black border-2 border-black px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wide flex items-center gap-1 shadow-[2px_2px_0px_0px_black]" title="Métrica avaliada de forma observacional pelo professor">
                                            ✍️ Manual (GM)
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Corpo de Evidências --}}
                            <div class="p-6 space-y-4">
                                <p class="text-xs text-gray-500 font-bold leading-relaxed">{{ $comp->descricao }}</p>

                                {{-- SE FOR AUTOMÁTICO: CRUZA DADOS DO SOFTWARE EM REAL-TIME --}}
                                @if($comp->tipo_avaliacao == 'automatica' && isset($comp->metrica_chave))
                                    @php $valorMetrica = $metricas[$comp->metrica_chave] ?? 0; @endphp
                                    <div class="bg-blue-50 border-2 border-blue-300 p-3 rounded flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xl">📊</span>
                                            <div>
                                                <p class="text-[9px] font-black text-blue-700 uppercase leading-none">Dados rastreados no ERP:</p>
                                                <p class="text-xs font-bold text-gray-700 mt-1">Ações realizadas pelo aluno nesta métrica</p>
                                            </div>
                                        </div>
                                        <div class="bg-white border-2 border-black px-4 py-1.5 rounded font-black text-lg text-blue-700 shadow-[2px_2px_0px_0px_rgba(0,0,0,0.15)]">
                                            {{ $valorMetrica }} <span class="text-xs text-gray-500 font-bold uppercase">execuções</span>
                                        </div>
                                    </div>
                                @endif

                                {{-- CAMPO DE DECISÃO PEDAGÓGICA (ON / OFF / PENDENTE) --}}
                                <div>
                                    <label class="block text-[10px] font-black text-gray-600 uppercase mb-2">Decisão de Competência:</label>
                                    <div class="grid grid-cols-3 gap-2">
                                        
                                        <!-- Conforme -->
                                        <label class="border-2 border-black p-2 rounded-lg cursor-pointer text-center font-black text-xs uppercase flex items-center justify-center gap-2 transition select-none
                                            @if($statusAtual == 'conforme') bg-green-500 text-white shadow-[2px_2px_0px_0px_black] @else bg-white hover:bg-green-50 text-green-700 @endif">
                                            <input type="radio" name="avaliacoes[{{ $comp->id }}][status]" value="conforme" class="hidden" @checked($statusAtual == 'conforme') onclick="selecionarStatus(this)">
                                            <span>🟢 Conforme</span>
                                        </label>

                                        <!-- Não Conforme -->
                                        <label class="border-2 border-black p-2 rounded-lg cursor-pointer text-center font-black text-xs uppercase flex items-center justify-center gap-2 transition select-none
                                            @if($statusAtual == 'nao_conforme') bg-red-500 text-white shadow-[2px_2px_0px_0px_black] @else bg-white hover:bg-red-50 text-red-700 @endif">
                                            <input type="radio" name="avaliacoes[{{ $comp->id }}][status]" value="nao_conforme" class="hidden" @checked($statusAtual == 'nao_conforme') onclick="selecionarStatus(this)">
                                            <span>🔴 Não Conforme</span>
                                        </label>

                                        <!-- Pendente -->
                                        <label class="border-2 border-black p-2 rounded-lg cursor-pointer text-center font-black text-xs uppercase flex items-center justify-center gap-2 transition select-none
                                            @if($statusAtual == 'pendente' || !$statusAtual) bg-gray-400 text-white shadow-[2px_2px_0px_0px_black] @else bg-white hover:bg-gray-100 text-gray-600 @endif">
                                            <input type="radio" name="avaliacoes[{{ $comp->id }}][status]" value="pendente" class="hidden" @checked($statusAtual == 'pendente' || !$statusAtual) onclick="selecionarStatus(this)">
                                            <span>🟡 Pendente</span>
                                        </label>

                                    </div>
                                </div>

                                {{-- ANOTAÇÕES DE EVIDÊNCIA DO PROFESSOR --}}
                                <div>
                                    <label class="block text-[10px] font-black text-gray-600 uppercase mb-1">Observações do Instrutor / Evidências:</label>
                                    <textarea name="avaliacoes[{{ $comp->id }}][observacoes]" rows="2" 
                                              placeholder="Escreva notas, observações em sala ou registros de feedback..."
                                              class="w-full border-2 border-black rounded-lg p-2 text-xs font-bold focus:ring-0 focus:border-indigo-600 bg-white">{{ $observacaoAtual }}</textarea>
                                </div>

                            </div>
                        </div>
                    @endforeach

                    {{-- BOTÃO SALVAR GERAL --}}
                    <div class="flex justify-end gap-4 border-t-4 border-dashed border-gray-400 pt-6">
                        <button type="submit" 
                                class="px-8 py-3 bg-purple-600 text-white rounded-lg border-2 border-black font-black text-sm uppercase hover:bg-purple-700 shadow-[6px_6px_0px_0px_black] transition transform hover:-translate-y-1 active:translate-y-[1px] active:shadow-none flex items-center gap-2">
                            <span>🎓</span> Salvar Observações e Comentários
                        </button>
                    </div>

                @endif
            </form>

        </div>
    </div>
</x-app-layout>
<script>
function selecionarStatus(input) {
    const container = input.closest('.grid');
    const labels = container.querySelectorAll('label');
    
    labels.forEach(label => {
        label.classList.remove('bg-green-500', 'bg-red-500', 'bg-gray-400', 'text-white', 'shadow-[2px_2px_0px_0px_black]');
        label.classList.add('bg-white');
        
        const valorRadio = label.querySelector('input').value;
        if (valorRadio === 'conforme') {
            label.classList.add('text-green-700', 'hover:bg-green-50');
        } else if (valorRadio === 'nao_conforme') {
            label.classList.add('text-red-700', 'hover:bg-red-50');
        } else {
            label.classList.add('text-gray-600', 'hover:bg-gray-100');
        }
    });

    const labelClicado = input.closest('label');
    labelClicado.classList.remove('bg-white', 'hover:bg-green-50', 'hover:bg-red-50', 'hover:bg-gray-100');
    labelClicado.classList.add('shadow-[2px_2px_0px_0px_black]', 'text-white');
    
    if (input.value === 'conforme') {
        labelClicado.classList.add('bg-green-500');
    } else if (input.value === 'nao_conforme') {
        labelClicado.classList.add('bg-red-500');
    } else {
        labelClicado.classList.add('bg-gray-400');
    }
}
</script>