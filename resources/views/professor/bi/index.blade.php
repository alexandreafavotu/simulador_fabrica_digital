<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* ESTILO DARK FINTECH (O VISUAL DA SUA FOTO) */
        body, .min-h-screen {
            background-color: #0b1121 !important; /* Fundo Azul Quase Preto */
            color: #e2e8f0; /* Texto Claro */
            font-family: 'Inter', sans-serif;
        }

        /* Cards do Dashboard */
        .card-fintech {
            background: #151e32; /* Azul Petróleo Escuro */
            border: 1px solid #2d3b55;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5), 0 2px 4px -1px rgba(0, 0, 0, 0.3);
            transition: transform 0.2s;
        }
        .card-fintech:hover {
            border-color: #3b82f6; /* Brilho azul ao passar o mouse */
        }

        /* Botões de Navegação */
        .nav-btn {
            background: transparent;
            color: #94a3b8;
            border-bottom: 2px solid transparent;
            padding: 10px 20px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }
        .nav-btn.active {
            color: #38bdf8; /* Azul Neon */
            border-bottom-color: #38bdf8;
            text-shadow: 0 0 10px rgba(56, 189, 248, 0.5);
        }

        /* Tipografia de Números */
        .stat-value {
            font-family: 'Roboto Mono', monospace; /* Fonte técnica */
            letter-spacing: -1px;
        }

        /* Cores de Texto Específicas */
        .text-neon-green { color: #34d399; text-shadow: 0 0 5px rgba(52, 211, 153, 0.3); }
        .text-neon-red   { color: #f87171; text-shadow: 0 0 5px rgba(248, 113, 113, 0.3); }
        .text-neon-blue  { color: #60a5fa; text-shadow: 0 0 5px rgba(96, 165, 250, 0.3); }
        .text-neon-orange { color: #fbbf24; text-shadow: 0 0 5px rgba(251, 191, 36, 0.3); }
    </style>

    <div class="py-6 min-h-screen" x-data="{ activeTab: 'geral' }">
        <div class="max-w-[98%] mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- CABEÇALHO --}}
            {{-- CABEÇALHO COM FILTRO E RELÓGIO DA TURMA --}}
            <div class="flex flex-col md:flex-row justify-between items-center border-b border-gray-800 pb-4 gap-4">
                
                {{-- Título e Relógio --}}
                <div>
                    <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                        DASHBOARD <span class="text-xs bg-blue-900 text-blue-200 px-2 py-1 rounded border border-blue-700">{{ $turma->nome_empresa }}</span>
                    </h2>
                    <p class="text-xs text-neon-blue mt-1 font-mono">
                        DATA DA SIMULAÇÃO: {{ $turma->data_jogo->format('d/m/Y H:i') }}
                    </p>
                </div>
                
                {{-- Filtro de Data (Calendário do Jogo) --}}
                {{-- Filtro de Data (Correção Final: Estilo Inline Isolado) --}}
                <form method="GET" action="{{ route('professor.monitoramento.bi', $turma->id) }}" class="flex items-center gap-2 bg-slate-800 p-2 rounded border border-slate-700">
                    
                    <div class="flex flex-col">
                        <label class="text-[9px] text-gray-400 font-bold uppercase ml-1">Início:</label>
                        {{-- O segredo está aqui: style="color-scheme: dark;" --}}
                        <input type="date" name="data_inicio" value="{{ request('data_inicio') }}" 
                               style="color-scheme: dark;"
                               class="bg-slate-900 border border-slate-600 text-white text-xs rounded p-1.5 focus:ring-blue-500 focus:border-blue-500 w-32 cursor-pointer">
                    </div>

                    <span class="text-gray-500 mt-3">➜</span>

                    <div class="flex flex-col">
                        <label class="text-[9px] text-gray-400 font-bold uppercase ml-1">Fim:</label>
                        {{-- O segredo está aqui: style="color-scheme: dark;" --}}
                        <input type="date" name="data_fim" value="{{ request('data_fim') }}" 
                               style="color-scheme: dark;"
                               class="bg-slate-900 border border-slate-600 text-white text-xs rounded p-1.5 focus:ring-blue-500 focus:border-blue-500 w-32 cursor-pointer">
                    </div>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-3 rounded text-xs font-bold uppercase transition ml-2 h-full mt-auto shadow-lg hover:shadow-blue-500/50">
                        Filtrar
                    </button>
                    
                    @if(request('data_inicio'))
                        <a href="{{ route('professor.monitoramento.bi', $turma->id) }}" class="text-red-400 hover:text-red-300 text-xs font-bold uppercase ml-1 mt-3" title="Limpar Filtro">X</a>
                    @endif
                </form>

                {{-- Navegação --}}
                <div class="flex gap-2">
                    <button @click="activeTab = 'geral'" :class="activeTab === 'geral' ? 'active' : ''" class="nav-btn">Geral</button>
                    <button @click="activeTab = 'comercial'" :class="activeTab === 'comercial' ? 'active' : ''" class="nav-btn">Comercial</button>
                    <button @click="activeTab = 'industrial'" :class="activeTab === 'industrial' ? 'active' : ''" class="nav-btn">Indústria</button>
                    <button @click="activeTab = 'logistica'" :class="activeTab === 'logistica' ? 'active' : ''" class="nav-btn">Logística</button>
                </div>

                <a href="{{ route('professor.monitoramento.index', $turma->id) }}" class="bg-gray-800 hover:bg-gray-700 text-white text-xs font-bold py-2 px-4 rounded border border-gray-600 transition">
                    VOLTAR
                </a>
            </div>

            {{-- ======================================================= --}}
            {{-- TAB 1: VISÃO GERAL (Dark Mode Fintech)                  --}}
            {{-- ======================================================= --}}
            <div x-show="activeTab === 'geral'" class="space-y-6" x-transition.opacity>
                {{-- BLOCO EXTRA: ESTOQUE E GRÁFICOS DE OPERAÇÃO --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    
                    <!-- KPI: Valor em Estoque -->
                    <div class="card-fintech p-4 flex items-center justify-between border-l-4 border-l-purple-500">
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-bold">Capital em Estoque</p>
                            <h3 class="text-2xl font-bold text-white stat-value mt-1">R$ {{ number_format($dados['estoque']['valor_total'], 0, ',', '.') }}</h3>
                            <p class="text-[10px] text-purple-400 mt-1">Matéria-Prima + Acabados</p>
                        </div>
                        <div class="h-10 w-10 rounded-full bg-purple-900/30 flex items-center justify-center text-purple-400">📦</div>
                    </div>

                    <!-- Gráfico: Evolução Faturamento -->
                    <div class="card-fintech p-4 relative">
                        <h4 class="text-gray-400 text-[10px] font-bold uppercase mb-2">Faturamento (Realizado vs Meta)</h4>
                        <div class="h-32">
                            <canvas id="chartFatMeta"></canvas>
                        </div>
                    </div>

                    <!-- Gráfico: Gargalo (Status OPs) -->
                    <div class="card-fintech p-4 relative">
                        <h4 class="text-gray-400 text-[10px] font-bold uppercase mb-2">Status da Produção</h4>
                        <div class="h-32 flex justify-center">
                            <canvas id="chartStatusProd"></canvas>
                        </div>
                    </div>
                </div>
                
                {{-- LINHA 1: KPIS PRINCIPAIS --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Receita -->
                    <div class="card-fintech p-4 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-bold">Receita Total</p>
                            <h3 class="text-2xl font-bold text-white stat-value mt-1">R$ {{ number_format($dados['vendas']['total'], 0, ',', '.') }}</h3>
                        </div>
                        <div class="h-10 w-10 rounded-full bg-green-900/30 flex items-center justify-center text-green-400 border border-green-800">💰</div>
                    </div>

                    <!-- Despesas (Custo do Caos) -->
                    <div class="card-fintech p-4 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-bold">Custo Operacional</p>
                            <h3 class="text-2xl font-bold text-white stat-value mt-1">R$ {{ number_format($dados['caos']['total_perda'], 0, ',', '.') }}</h3>
                        </div>
                        <div class="h-10 w-10 rounded-full bg-red-900/30 flex items-center justify-center text-red-400 border border-red-800">📉</div>
                    </div>

                    <!-- Imposto Estimado (10%) -->
                    <div class="card-fintech p-4 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-bold">Imposto Estimado</p>
                            <h3 class="text-2xl font-bold text-white stat-value mt-1">R$ {{ number_format($dados['vendas']['total'] * 0.10, 0, ',', '.') }}</h3>
                        </div>
                        <div class="h-10 w-10 rounded-full bg-blue-900/30 flex items-center justify-center text-blue-400 border border-blue-800">🏛️</div>
                    </div>

                    <!-- Lucro Líquido -->
                    <div class="card-fintech p-4 flex items-center justify-between border-l-4 border-l-green-500">
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-bold">Lucro Líquido</p>
                            <h3 class="text-2xl font-bold text-neon-green stat-value mt-1">
                                R$ {{ number_format($dados['vendas']['total'] - $dados['caos']['total_perda'] - ($dados['vendas']['total'] * 0.10), 0, ',', '.') }}
                            </h3>
                        </div>
                        <div class="h-10 w-10 rounded-full bg-green-500/20 flex items-center justify-center text-green-400">🚀</div>
                    </div>
                </div>

                {{-- LINHA 2: SETORES E GRÁFICO CENTRAL --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-80">
                    <!-- ESQUERDA: SETORES -->
                    <div class="card-fintech p-6 relative">
                        <h4 class="text-gray-400 text-xs font-bold uppercase mb-6">Departamentos Ativos</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex items-center gap-3 p-3 rounded bg-slate-800/50 border border-slate-700">
                                <div class="w-8 h-8 rounded-full bg-yellow-500/20 text-yellow-500 flex items-center justify-center font-bold">V</div>
                                <span class="text-sm font-bold">Vendas</span>
                            </div>
                            <div class="flex items-center gap-3 p-3 rounded bg-slate-800/50 border border-slate-700">
                                <div class="w-8 h-8 rounded-full bg-blue-500/20 text-blue-500 flex items-center justify-center font-bold">P</div>
                                <span class="text-sm font-bold">PCP</span>
                            </div>
                            <div class="flex items-center gap-3 p-3 rounded bg-slate-800/50 border border-slate-700">
                                <div class="w-8 h-8 rounded-full bg-red-500/20 text-red-500 flex items-center justify-center font-bold">W</div>
                                <span class="text-sm font-bold">WMS</span>
                            </div>
                            <div class="flex items-center gap-3 p-3 rounded bg-slate-800/50 border border-slate-700">
                                <div class="w-8 h-8 rounded-full bg-green-500/20 text-green-500 flex items-center justify-center font-bold">F</div>
                                <span class="text-sm font-bold">Fábrica</span>
                            </div>
                        </div>
                    </div>

                    <!-- CENTRO: GRÁFICO DE ROSCA OEE -->
                    <div class="card-fintech p-6 flex flex-col items-center justify-center relative">
                        <h4 class="absolute top-4 left-6 text-gray-400 text-xs font-bold uppercase">Eficiência Global (OEE)</h4>
                        <div class="h-48 w-48 relative">
                            <canvas id="chartOEE"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <span class="text-gray-400 text-[10px] uppercase">Score</span>
                                <span class="text-3xl font-bold text-white">{{ number_format($dados['producao']['oee'], 1) }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- DIREITA: INSIGHTS (CORRIGIDO: 'Novo' no lugar de 'Novos') -->
                    <div class="card-fintech p-6 flex flex-col justify-center">
                        <h4 class="text-gray-400 text-xs font-bold uppercase mb-4">Insights da Operação</h4>
                        <div class="space-y-4">
                            <div class="bg-blue-900/20 p-4 rounded border border-blue-800">
                                <p class="text-sm text-gray-300">
                                    Transações de venda: <strong class="text-white">{{ ($dados['vendas']['funil']['Novo'] ?? 0) + ($dados['vendas']['funil']['Faturado'] ?? 0) }}</strong>. 
                                    Ticket médio: <strong class="text-neon-blue">R$ {{ number_format($dados['vendas']['ticket_medio'], 2, ',', '.') }}</strong>.
                                </p>
                            </div>
                            <div class="flex justify-between items-end">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">Qualidade Fornecedor</p>
                                    <p class="text-xl font-bold text-white">{{ $dados['compras']['qualidade_fornecedor'] }}%</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">OTIF Logístico</p>
                                    <p class="text-xl font-bold text-white">{{ $dados['logistica']['otif'] }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- LINHA 3: GRÁFICO WATERFALL (LUCRO) --}}
                <div class="card-fintech p-6">
                    <h4 class="text-gray-400 text-xs font-bold uppercase mb-4">Fluxo de Caixa Mensal (Simulado)</h4>
                    <div class="h-64 w-full">
                        <canvas id="chartLucroMensal"></canvas>
                    </div>
                </div>
            </div>

            {{-- ======================================================= --}}
            {{-- TAB 2: COMERCIAL (RESTAURADA)                           --}}
            {{-- ======================================================= --}}
            <div x-show="activeTab === 'comercial'" style="display:none" class="space-y-6" x-transition.opacity>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="card-fintech p-6 col-span-2">
                        <h4 class="text-white text-lg font-bold mb-4 border-b border-gray-700 pb-2">Curva ABC de Produtos</h4>
                        <div class="overflow-y-auto h-80">
                            <table class="w-full text-sm text-gray-300">
                                <thead class="bg-gray-800 text-white sticky top-0">
                                    <tr><th class="p-3 text-left">Classe</th><th class="p-3">Produto</th><th class="p-3 text-right">Receita Total</th></tr>
                                </thead>
                                <tbody class="divide-y divide-gray-700">
                                    @foreach($dados['vendas']['abc'] as $item)
                                    <tr class="hover:bg-gray-800">
                                        <td class="p-3"><span class="px-2 py-1 rounded text-xs font-bold text-black {{ $item->classe=='A'?'bg-green-400':($item->classe=='B'?'bg-yellow-400':'bg-gray-400') }}">{{ $item->classe }}</span></td>
                                        <td class="p-3">{{ $item->nome }}</td>
                                        <td class="p-3 text-right font-mono text-neon-blue">R$ {{ number_format($item->total, 2, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-fintech p-6">
                        <h4 class="text-white font-bold mb-4">Funil de Pedidos</h4>
                        <div class="h-64"><canvas id="chartFunil"></canvas></div>
                    </div>
                </div>
            </div>

            {{-- ======================================================= --}}
            {{-- TAB 3: INDÚSTRIA (RESTAURADA + CUSTO DO CAOS)           --}}
            {{-- ======================================================= --}}
            <div x-show="activeTab === 'industrial'" style="display:none" class="space-y-6" x-transition.opacity>
                
                {{-- CARD CUSTO DO CAOS (Visual Dark) --}}
                <div class="card-fintech p-6 border-l-4 border-l-red-600 flex items-center justify-between">
                    <div>
                        <h4 class="text-neon-red text-xl font-bold uppercase flex items-center gap-2">🔥 Custo da Ineficiência</h4>
                        <p class="text-xs text-gray-400">Impacto financeiro de recusas e refugos.</p>
                        <div class="text-4xl font-bold text-white mt-2">R$ {{ number_format($dados['caos']['total_perda'], 2, ',', '.') }}</div>
                    </div>
                    <div class="h-32 w-64">
                        <canvas id="chartCaos"></canvas>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="card-fintech p-6 text-center">
                        <h4 class="text-gray-400 text-xs font-bold uppercase mb-2">Taxa de Refugo</h4>
                        <div class="h-40 flex justify-center mb-4"><canvas id="chartRefugo"></canvas></div>
                        <p class="text-3xl font-bold {{ $dados['producao']['qualidade'] > 90 ? 'text-neon-green' : 'text-neon-red' }}">
                            {{ number_format($dados['producao']['qualidade'], 1) }}%
                        </p>
                        <p class="text-xs text-gray-500">Qualidade Final</p>
                    </div>
                    <div class="card-fintech p-6 col-span-2">
                        <h4 class="text-gray-400 text-xs font-bold uppercase mb-4">Spend Analysis (Top Gastos)</h4>
                        <div class="h-56"><canvas id="chartSpend"></canvas></div>
                    </div>
                </div>
            </div>

            {{-- ======================================================= --}}
            {{-- TAB 4: LOGÍSTICA (RESTAURADA)                           --}}
            {{-- ======================================================= --}}
            <div x-show="activeTab === 'logistica'" style="display:none" class="space-y-6" x-transition.opacity>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="card-fintech p-6">
                         <h4 class="text-white font-bold mb-4">Ocupação WMS</h4>
                         <div class="h-64 flex justify-center"><canvas id="chartWMS"></canvas></div>
                         <p class="text-center font-bold text-xl mt-4 text-neon-orange">
                             {{ number_format($dados['estoque']['ocupacao_wms'], 1) }}% Cheio
                         </p>
                    </div>
                    <div class="card-fintech p-6 flex flex-col justify-center space-y-8">
                         <div>
                            <div class="flex justify-between mb-1"><span class="text-sm font-bold text-gray-400">Giro de Estoque</span><span class="text-sm font-bold text-neon-blue">{{ $dados['logistica']['giro'] }}x</span></div>
                            <div class="w-full bg-gray-700 h-2 rounded-full overflow-hidden"><div class="bg-blue-500 h-full" style="width: 70%"></div></div>
                         </div>
                         <div class="text-center bg-black/40 p-6 rounded-lg border border-gray-700">
                             <p class="text-xs text-gray-400 uppercase font-bold tracking-widest">Pedidos na Doca de Saída</p>
                             <p class="text-5xl font-black mt-2 text-neon-orange">{{ $dados['logistica']['doca_saida'] }}</p>
                         </div>
                         


                         <!-- ACURACIDADE (Restaurada) -->
                         <div class="mt-4">
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-bold text-gray-400 uppercase">Acuracidade</span>
                                <span class="text-sm font-bold text-neon-green">98%</span>
                            </div>
                            <div class="w-full bg-gray-700 h-2 rounded-full overflow-hidden shadow-inner">
                                <div class="bg-green-500 h-full shadow-[0_0_10px_#22c55e]" style="width: 98%"></div>
                            </div>
                         </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- SCRIPTS (TODOS OS GRÁFICOS) --}}
    <script>
        
        // CONFIGURAÇÃO GLOBAL
        Chart.defaults.color = '#94a3b8';
        Chart.defaults.borderColor = '#1e293b';
        Chart.defaults.font.family = "'Inter', sans-serif";

        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        };

        // 1. OEE (VISÃO GERAL)
        new Chart(document.getElementById('chartOEE'), {
            type: 'doughnut',
            data: {
                labels: ['Eficiência', 'Perda'],
                datasets: [{
                    data: [{{ $dados['producao']['oee'] }}, {{ 100 - $dados['producao']['oee'] }}],
                    backgroundColor: ['#ffffff', '#1e293b'],
                    borderWidth: 0,
                    cutout: '85%',
                    borderRadius: 20
                }]
            },
            options: { plugins: { legend: { display: false }, tooltip: { enabled: false } } }
        });

        // 2. WATERFALL (VISÃO GERAL)
        new Chart(document.getElementById('chartLucroMensal'), {
            type: 'bar',
            data: {
                labels: ['Vendas', 'Custo MP', 'Refugo', 'Impostos', 'LUCRO'],
                datasets: [{
                    data: [
                        {{ $dados['vendas']['total'] }}, 
                        -{{ $dados['estoque']['valor_mp'] }}, 
                        -{{ $dados['caos']['total_perda'] }}, 
                        -{{ $dados['vendas']['total'] * 0.10 }}, 
                        {{ $dados['vendas']['total'] - $dados['caos']['total_perda'] - ($dados['vendas']['total'] * 0.10) }}
                    ],
                    backgroundColor: ['#3b82f6', '#64748b', '#ef4444', '#64748b', '#34d399'],
                    borderRadius: 4
                }]
            },
            options: commonOptions
        });

        // 3. FUNIL (COMERCIAL)
        const funilData = @json($dados['vendas']['funil']);
        new Chart(document.getElementById('chartFunil'), {
            type: 'bar',
            data: {
                labels: Object.keys(funilData),
                datasets: [{
                    data: Object.values(funilData),
                    backgroundColor: '#3b82f6',
                    borderRadius: 4
                }]
            },
            options: { ...commonOptions, indexAxis: 'y' }
        });

        // 4. REFUGO (INDÚSTRIA)
        new Chart(document.getElementById('chartRefugo'), {
            type: 'pie',
            data: {
                labels: ['Bom', 'Refugo'],
                datasets: [{
                    data: [{{ $dados['producao']['produzido'] }}, {{ $dados['producao']['refugo_qtd'] }}],
                    backgroundColor: ['#22c55e', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: commonOptions
        });

        // 5. CAOS (INDÚSTRIA)
        new Chart(document.getElementById('chartCaos'), {
            type: 'bar',
            data: {
                labels: ['WMS', 'PROD'],
                datasets: [{
                    data: [{{ $dados['caos']['custo_wms'] }}, {{ $dados['caos']['custo_prod'] }}],
                    backgroundColor: ['#f97316', '#ef4444'],
                    borderRadius: 4
                }]
            },
            options: { ...commonOptions, indexAxis: 'y', scales: { x: { display: false }, y: { display: false } } }
        });

        // 6. SPEND (INDÚSTRIA)
        const spendData = @json($dados['compras']['spend_analysis']);
        new Chart(document.getElementById('chartSpend'), {
            type: 'bar',
            data: {
                labels: spendData.map(i => i.nome),
                datasets: [{
                    data: spendData.map(i => i.gasto),
                    backgroundColor: '#a855f7',
                    borderRadius: 4
                }]
            },
            options: commonOptions
        });

        // 7. WMS (LOGÍSTICA)
        new Chart(document.getElementById('chartWMS'), {
            type: 'doughnut',
            data: {
                labels: ['Ocupado', 'Livre'],
                datasets: [{
                    data: [{{ $dados['estoque']['ocupacao_wms'] }}, {{ 100 - $dados['estoque']['ocupacao_wms'] }}],
                    backgroundColor: ['#fbbf24', '#374151'],
                    borderWidth: 0
                }]
            },
            options: commonOptions
        });
        // 8. GRÁFICO EVOLUÇÃO (BARRAS COMPARATIVAS)
        new Chart(document.getElementById('chartFatMeta'), {
            type: 'bar',
            data: {
                labels: ['REALIZADO', 'CARTEIRA (META)'],
                datasets: [{
                    data: [{{ $dados['vendas']['total'] }}, {{ $dados['vendas']['backlog_valor'] }}],
                    backgroundColor: ['#34d399', '#334155'], // Verde Neon e Cinza Escuro
                    borderRadius: 4,
                    barPercentage: 0.6
                }]
            },
            options: commonOptions
        });

        // 9. GRÁFICO GARGALO (ROSCA STATUS OPs)
        const statusProdData = @json($dados['producao']['status']);
        new Chart(document.getElementById('chartStatusProd'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(statusProdData),
                datasets: [{
                    data: Object.values(statusProdData),
                    backgroundColor: ['#fbbf24', '#3b82f6', '#22c55e'], // Amarelo, Azul, Verde
                    borderWidth: 0,
                    cutout: '70%'
                }]
            },
            options: {
                ...commonOptions,
                plugins: { legend: { display: false } }
            }
        });
    </script>
</x-app-layout>