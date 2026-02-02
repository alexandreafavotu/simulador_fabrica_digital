<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-red-700 leading-tight flex items-center gap-3 uppercase tracking-tighter">
                <span class="text-4xl animate-pulse">⚡</span> PAINEL DE CONTROLE DO CAOS: <span class="text-black ml-2">{{ $turma->nome }}</span>
            </h2>
            <a href="{{ route('professor.dashboard') }}" 
               class="bg-gray-800 text-white px-6 py-2 rounded border-2 border-black shadow-[4px_4px_0px_0px_black] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-none transition text-xs font-black uppercase flex items-center gap-2">
                ⬅ Voltar ao Painel
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-red-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-12">

            {{-- 1. PLANTÃO DE NOTÍCIAS (MENSAGEM GERAL) --}}
            <div class="bg-yellow-400 border-4 border-black shadow-[10px_10px_0px_0px_black] p-6 rounded-none relative">
                <div class="absolute -top-5 left-4 bg-black text-yellow-400 px-4 py-1 text-sm font-black uppercase tracking-widest border-2 border-yellow-400">
                    📢 Comunicação Oficial
                </div>
                
                <h3 class="font-black text-2xl text-black mb-4 uppercase mt-2">Plantão de Notícias (Pop-up para Alunos)</h3>
                
                <form action="{{ route('professor.caos.mensagem', $turma->id) }}" method="POST" class="flex gap-4">
                    @csrf
                    <input type="text" name="mensagem" 
                           class="w-full border-4 border-black p-3 text-lg font-bold placeholder-gray-600 focus:ring-0 focus:border-white bg-white shadow-inner" 
                           placeholder="Ex: Atenção! Greve de caminhoneiros iniciada. Previsão de normalização em 3 dias." 
                           value="{{ $turma->mensagem_plantao_caos }}">
                    
                    <button class="bg-black text-white border-2 border-white font-black py-3 px-8 text-sm uppercase hover:bg-gray-900 transition shadow-[4px_4px_0px_0px_white] active:shadow-none active:translate-y-[2px]">
                        PUBLICAR
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                
                {{-- 2. BLOQUEIOS GLOBAIS (INTERRUPTORES DE SETOR) --}}
                <div class="bg-white border-4 border-black shadow-[8px_8px_0px_0px_black] overflow-hidden">
                    <div class="bg-red-600 text-white p-5 border-b-4 border-black flex items-center gap-3">
                        <span class="text-4xl">🚫</span>
                        <div>
                            <h3 class="font-black text-2xl uppercase tracking-wide leading-none">Bloqueios Globais</h3>
                            <p class="text-xs text-red-200 font-bold uppercase mt-1">Paralisar setores inteiros</p>
                        </div>
                    </div>
                    
                    <div class="p-6 space-y-6 bg-red-50">
                        
                        <!-- Almoxarifado -->
                        <form action="{{ route('professor.caos.bloqueio', $turma->id) }}" method="POST" class="flex items-center justify-between bg-white p-4 border-2 border-black shadow-sm group hover:border-red-600 transition-colors">
                            @csrf <input type="hidden" name="tipo" value="almoxarifado">
                            <div>
                                <span class="font-black text-gray-900 block uppercase text-sm group-hover:text-red-600">📦 Almoxarifado</span>
                                <span class="text-[10px] font-bold {{ $turma->bloqueio_almoxarifado_ate ? 'text-red-600 animate-pulse bg-red-100 px-1' : 'text-green-600' }} uppercase">
                                    {{ $turma->bloqueio_almoxarifado_ate ? 'TRAVADO até ' . \Carbon\Carbon::parse($turma->bloqueio_almoxarifado_ate)->format('d/m') : '● Operando Normal' }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="number" name="dias" value="1" min="1" class="w-16 border-2 border-black text-center font-black text-lg h-10 focus:ring-0">
                                <button class="bg-red-600 text-white text-[10px] font-black px-4 h-10 border-2 border-black hover:bg-red-700 uppercase shadow-[2px_2px_0px_0px_black] active:shadow-none active:translate-y-[1px] transition">
                                    TRAVAR
                                </button>
                            </div>
                        </form>

                        <!-- Expedição -->
                        <form action="{{ route('professor.caos.bloqueio', $turma->id) }}" method="POST" class="flex items-center justify-between bg-white p-4 border-2 border-black shadow-sm group hover:border-red-600 transition-colors">
                            @csrf <input type="hidden" name="tipo" value="expedicao">
                            <div>
                                <span class="font-black text-gray-900 block uppercase text-sm group-hover:text-red-600">🚚 Expedição (Greve)</span>
                                <span class="text-[10px] font-bold {{ $turma->bloqueio_expedicao_ate ? 'text-red-600 animate-pulse bg-red-100 px-1' : 'text-green-600' }} uppercase">
                                    {{ $turma->bloqueio_expedicao_ate ? 'TRAVADO até ' . \Carbon\Carbon::parse($turma->bloqueio_expedicao_ate)->format('d/m') : '● Operando Normal' }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="number" name="dias" value="2" min="1" class="w-16 border-2 border-black text-center font-black text-lg h-10 focus:ring-0">
                                <button class="bg-red-600 text-white text-[10px] font-black px-4 h-10 border-2 border-black hover:bg-red-700 uppercase shadow-[2px_2px_0px_0px_black] active:shadow-none active:translate-y-[1px] transition">
                                    TRAVAR
                                </button>
                            </div>
                        </form>
                        
                        <!-- Faturamento -->
                        <form action="{{ route('professor.caos.bloqueio', $turma->id) }}" method="POST" class="flex items-center justify-between bg-white p-4 border-2 border-black shadow-sm group hover:border-red-600 transition-colors">
                            @csrf <input type="hidden" name="tipo" value="faturamento">
                            <div>
                                <span class="font-black text-gray-900 block uppercase text-sm group-hover:text-red-600">💻 Faturamento (TI)</span>
                                <span class="text-[10px] font-bold {{ $turma->bloqueio_faturamento_ate ? 'text-red-600 animate-pulse bg-red-100 px-1' : 'text-green-600' }} uppercase">
                                    {{ $turma->bloqueio_faturamento_ate ? 'TRAVADO até ' . \Carbon\Carbon::parse($turma->bloqueio_faturamento_ate)->format('d/m') : '● Operando Normal' }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="number" name="dias" value="1" min="1" class="w-16 border-2 border-black text-center font-black text-lg h-10 focus:ring-0">
                                <button class="bg-red-600 text-white text-[10px] font-black px-4 h-10 border-2 border-black hover:bg-red-700 uppercase shadow-[2px_2px_0px_0px_black] active:shadow-none active:translate-y-[1px] transition">
                                    TRAVAR
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- 3. INCIDENTES PONTUAIS (COLUNA DA DIREITA) --}}
                <div class="space-y-8">
                    
                    <!-- Quebra de Máquina -->
                    <div class="bg-white border-4 border-black shadow-[8px_8px_0px_0px_black] overflow-hidden">
                        <div class="bg-orange-500 text-white p-4 border-b-4 border-black flex items-center gap-3">
                            <span class="text-3xl">🔥</span>
                            <div>
                                <h3 class="font-black text-xl uppercase tracking-tighter">Quebrar Máquina</h3>
                                <p class="text-[10px] font-bold text-orange-100 uppercase">Parar Produção por X dias</p>
                            </div>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('professor.caos.maquina', $turma->id) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <select name="ordem_producao_id" class="w-full border-2 border-black rounded text-sm font-bold focus:ring-0 focus:border-orange-500" required>
                                        <option value="">Selecione a OP em andamento...</option>
                                        @foreach($opsEmAndamento as $op)
                                            <option value="{{ $op->id }}">OP #{{ $op->id }} - {{ $op->produto->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex items-center gap-2 justify-end bg-gray-100 p-2 border-2 border-black rounded">
                                    <span class="text-[10px] font-black uppercase text-gray-500">Tempo Parada:</span>
                                    <input type="number" name="dias_manutencao" value="1" min="1" class="w-16 border-2 border-black text-center font-black focus:ring-0">
                                    <span class="text-[10px] font-black uppercase text-gray-500 mr-2">dias</span>
                                    <button class="bg-orange-600 text-white font-black px-6 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_black] active:shadow-none active:translate-y-[1px] text-xs uppercase hover:bg-orange-700 transition">
                                        QUEBRAR
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Atraso de Fornecedor -->
                    <div class="bg-white border-4 border-black shadow-[8px_8px_0px_0px_black] overflow-hidden">
                        <div class="bg-purple-600 text-white p-4 border-b-4 border-black flex items-center gap-3">
                            <span class="text-3xl">🐢</span>
                            <div>
                                <h3 class="font-black text-xl uppercase tracking-tighter">Atrasar Entrega</h3>
                                <p class="text-[10px] font-bold text-purple-200 uppercase">Postergar chegada de material</p>
                            </div>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('professor.caos.atraso', $turma->id) }}" method="POST">
                                @csrf
                                <div class="flex gap-2 mb-4">
                                    <select name="ordem_compra_id" class="w-full border-2 border-black rounded text-sm font-bold focus:ring-0 focus:border-purple-500">
                                        <option value="">Selecione a Compra...</option>
                                        @foreach($ocsPendentes as $oc)
                                            <option value="{{ $oc->id }}">OC #{{ $oc->id }} - {{ $oc->materiaPrima->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex items-center gap-2 justify-end bg-gray-100 p-2 border-2 border-black rounded">
                                    <span class="text-[10px] font-black uppercase text-gray-500">Adicionar:</span>
                                    <input type="number" name="dias" value="5" class="w-16 border-2 border-black text-center font-black focus:ring-0">
                                    <span class="text-[10px] font-black uppercase text-gray-500 mr-2">dias</span>
                                    <button class="bg-purple-600 text-white font-black px-6 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_black] active:shadow-none active:translate-y-[1px] text-xs uppercase hover:bg-purple-700 transition">
                                        APLICAR
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- 📦 Sabotar Carga (Inconformidade WMS) -->
                    <div class="bg-white border-4 border-black shadow-[8px_8px_0px_0px_black] overflow-hidden">
                        <div class="bg-red-800 text-white p-4 border-b-4 border-black flex items-center gap-3">
                            <span class="text-3xl">📦</span>
                            <div>
                                <h3 class="font-black text-xl uppercase tracking-tighter">Sabotar Carga (WMS)</h3>
                                <p class="text-[10px] font-bold text-red-200 uppercase">Forçar Recusa de Material</p>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <form action="{{ route('professor.caos.sabotar_carga', $turma->id) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">1. Alvo (OC a caminho):</label>
                                    <select name="ordem_compra_id" class="w-full border-2 border-black rounded text-sm font-bold focus:ring-0 focus:border-red-800" required>
                                        <option value="">-- Escolha o Material --</option>
                                        @foreach($ocsPendentes as $oc)
                                            <option value="{{ $oc->id }}">OC #{{ $oc->id }} - {{ $oc->materiaPrima->nome ?? 'Material' }} ({{ $oc->status }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">2. Motivo Técnico (Pista):</label>
                                    <select name="descricao_inconformidade" class="w-full border-2 border-black rounded text-sm font-bold focus:ring-0 focus:border-red-800" required>
                                        <option value="">-- Escolha o Defeito --</option>
                                        <option value="Embalagem violada e material exposto ao ambiente">📦 Embalagem violada</option>
                                        <option value="Quantidade física menor que a informada na OC e Nota Fiscal">⚖️ Quantidade divergente</option>
                                        <option value="Material com sinais claros de umidade ou contaminação">💧 Umidade / Contaminação</option>
                                        <option value="Certificado de Qualidade ausente ou com dados inválidos">📜 Documentação irregular</option>
                                        <option value="Produto com data de validade vencida no lote recebido">📅 Validade vencida</option>
                                    </select>
                                </div>

                                <button type="submit" class="w-full bg-red-700 text-white font-black py-3 rounded border-2 border-black shadow-[4px_4px_0px_0px_black] active:shadow-none active:translate-y-[2px] transition uppercase text-xs hover:bg-red-800">
                                    ☠️ Condenar Carga
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- 🔥 Sabotar Produção (Refugo Programado) -->
            <div class="bg-white border-4 border-black shadow-[8px_8px_0px_0px_black] overflow-hidden">
                <div class="bg-orange-600 text-white p-4 border-b-4 border-black flex items-center gap-3">
                    <span class="text-3xl">⚙️</span>
                    <div>
                        <h3 class="font-black text-xl uppercase tracking-tighter">Sabotar Produção</h3>
                        <p class="text-[10px] font-bold text-orange-200 uppercase">Forçar Refugo / Perda</p>
                    </div>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('professor.caos.sabotar_producao', $turma->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">1. Alvo (OP em Andamento):</label>
                            
                            {{-- AQUI ENTRA A INTELIGÊNCIA: onchange + data-max --}}
                            <select name="ordem_producao_id" id="select_op_sabotagem" onchange="atualizarLimiteRefugo(this)" class="w-full border-2 border-black rounded text-sm font-bold focus:ring-0 focus:border-orange-600" required>
                                <option value="" data-max="0">-- Escolha a Ordem --</option>
                                @foreach($opsEmAndamento as $op)
                                    @php
                                        // Calcula o saldo real (Meta - Já Produzido) para não deixar quebrar mais do que existe
                                        $saldo = $op->quantidade - $op->apontamentos->sum('quantidade_produzida');
                                        if($saldo < 0) $saldo = 0;
                                    @endphp
                                    <option value="{{ $op->id }}" data-max="{{ $saldo }}">
                                        OP #{{ $op->id }} - {{ $op->produto->nome }} (Saldo Atual: {{ $saldo }} un)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">2. Perda (Qtd):</label>
                                
                                {{-- Input agora tem ID para o Javascript controlar --}}
                                <input type="number" name="qtd_refugo_forcado" id="input_refugo_forcado" min="1" 
                                       class="w-full border-2 border-black rounded text-sm font-bold text-center focus:ring-0" 
                                       placeholder="Selecione a OP" required>
                                
                                {{-- Aviso visual para o professor saber o limite --}}
                                <p id="aviso_limite" class="text-[10px] font-black text-orange-600 mt-1 uppercase hidden"></p>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">3. Causa:</label>
                                <select name="motivo_refugo_forcado" class="w-full border-2 border-black rounded text-sm font-bold focus:ring-0" required>
                                    <option value="">-- Motivo --</option>
                                    <option value="Falha crítica no molde de injeção">Falha Molde</option>
                                    <option value="Superaquecimento da máquina">Aquecimento</option>
                                    <option value="Queda de energia durante ciclo">Queda Energia</option>
                                    <option value="Matéria-prima contaminada">MP Ruim</option>
                                    <option value="Erro de operação manual">Erro Humano</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-orange-600 text-white font-black py-3 rounded border-2 border-black shadow-[4px_4px_0px_0px_black] active:shadow-none active:translate-y-[2px] transition uppercase text-xs hover:bg-orange-700">
                            🔥 Injetar Falha na Linha
                        </button>
                    </form>
                </div>
            </div>

            {{-- Script local para controlar o limite (Cole logo abaixo da div acima) --}}
            <script>
                function atualizarLimiteRefugo(select) {
                    // Pega a opção selecionada
                    const option = select.options[select.selectedIndex];
                    // Pega o saldo que guardamos no atributo data-max
                    const maximo = option.getAttribute('data-max');
                    
                    const input = document.getElementById('input_refugo_forcado');
                    const aviso = document.getElementById('aviso_limite');

                    if (maximo && maximo > 0) {
                        input.max = maximo;       // Trava o limite do HTML
                        input.value = '';         // Limpa o valor anterior
                        input.placeholder = 'Máx: ' + maximo;
                        input.disabled = false;
                        
                        aviso.innerText = 'Máximo permitido: ' + maximo + ' un';
                        aviso.classList.remove('hidden');
                    } else {
                        // Se não escolheu OP, ou saldo é zero, trava o campo
                        input.disabled = true;
                        input.placeholder = '---';
                        aviso.classList.add('hidden');
                    }
                }
            </script>

                </div>
            </div>

            {{-- 4. RESET GERAL (BOTÃO DE PAZ) --}}
            <div class="mt-12 text-center pb-8">
                <form action="{{ route('professor.caos.limpar', $turma->id) }}" method="POST" onsubmit="return confirm('Tem certeza? Isso vai consertar todas as máquinas e remover todos os bloqueios de setor.');">
                    @csrf
                    <button class="bg-green-600 hover:bg-green-700 text-white font-black py-5 px-12 rounded-full border-4 border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] transform hover:scale-105 active:scale-95 active:shadow-none transition text-xl flex items-center gap-4 mx-auto uppercase tracking-widest">
                        <span class="text-3xl">🕊️</span>
                        RESTAURAR A PAZ (NORMALIZAR TUDO)
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>