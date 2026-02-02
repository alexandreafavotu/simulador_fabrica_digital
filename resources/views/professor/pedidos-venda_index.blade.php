<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span>💰</span> {{ __('Gerenciar Pedidos de Venda') }}
            </h2>
            
            <a href="{{ route('professor.dashboard') }}" 
               class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                ⬅ Voltar ao Painel
            </a>
        </div>
    </x-slot>

    {{-- LÓGICA PHP: DETECTAR SE ESTAMOS EM MODO "TURMA ÚNICA" --}}
    @php
        $turmaSelecionada = null;
        if(request('filtro_turma')) {
            $turmaSelecionada = $turmas->firstWhere('id', request('filtro_turma'));
        }
        
        $cidadesUnicas = $clientes->pluck('cidade')->unique()->filter()->sort();
    @endphp

    <div class="py-8 bg-yellow-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- MENSAGENS DE SUCESSO --}}
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-600 text-green-800 p-4 shadow-md font-bold">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <!-- AVISO DE CONTEXTO (SE VIER DO DASHBOARD) -->
            @if($turmaSelecionada)
                <div class="bg-indigo-900 text-white p-4 rounded-lg border-4 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,0.4)] flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <span class="text-3xl">🏭</span>
                        <div>
                            <p class="text-xs text-indigo-300 font-bold uppercase tracking-widest">Modo Focado</p>
                            <h3 class="text-xl font-black uppercase leading-none">
                                Gerenciando: {{ $turmaSelecionada->nome_empresa }}
                            </h3>
                            <p class="text-xs text-white opacity-80">{{ $turmaSelecionada->nome }}</p>
                        </div>
                    </div>
                    
                    <a href="{{ route('professor.pedidos-venda.index') }}" class="bg-white text-indigo-900 px-4 py-2 rounded font-bold text-xs hover:bg-indigo-100 transition border-2 border-transparent hover:border-white">
                        Ver Todas as Turmas
                    </a>
                </div>
            @endif

            

            <!-- 2. NOVO PEDIDO (COM FILTRO INTELIGENTE E CONFIRMAÇÃO) -->
            <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] overflow-hidden"
                 x-data="{ 
                    showConfirm: false, 
                    loading: false,
                    empresaNome: '',
                    clienteNome: '',
                    totalItens: 0,

                    abrirConfirmacao() {
                        const selectTurma = document.getElementById('turma_id_novo_pedido');
                        const selectCliente = document.querySelector('select[name=cliente_id]');
                        
                        if(!selectTurma.value || !selectCliente.value) {
                            alert('⚠️ Selecione a Empresa e o Cliente primeiro!');
                            return;
                        }

                        this.empresaNome = selectTurma.options[selectTurma.selectedIndex].text;
                        this.clienteNome = selectCliente.options[selectCliente.selectedIndex].text;
                        
                        // Conta quantos itens foram adicionados
                        this.totalItens = document.querySelectorAll('.item-row').length;

                        this.showConfirm = true;
                    }
                 }">
                <div class="bg-indigo-600 text-white p-4 border-b-4 border-black flex items-center gap-2">
                    <span class="text-2xl">📝</span>
                    <h3 class="font-black text-lg uppercase tracking-wide">Novo Pedido</h3>
                </div>

                <div class="p-6">
                    <form id="formPedidoProfessor" action="{{ route('professor.pedidos-venda.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            
                            <!-- 1. Empresa (Gatilho do Filtro) -->
                            <div class="md:col-span-1">
                                <label class="block text-xs font-black text-indigo-700 uppercase mb-1">1. Empresa</label>
                                @if($turmaSelecionada)
                                    <input type="hidden" name="turma_id" id="turma_id_novo_pedido" value="{{ $turmaSelecionada->id }}">
                                    <div class="w-full bg-indigo-100 border-2 border-indigo-500 text-indigo-900 rounded p-2 text-sm font-black flex items-center justify-between shadow-sm uppercase">
                                        <span>{{ $turmaSelecionada->nome_empresa }}</span>
                                    </div>
                                @else
                                    <select name="turma_id" id="turma_id_novo_pedido" onchange="filtrarProdutosPorEmpresa(this.value)" class="w-full border-2 border-indigo-500 rounded text-sm focus:ring-0 font-bold shadow-[2px_2px_0px_0px_rgba(79,70,229,0.2)]" required>
                                        <option value="">-- Escolha a Empresa --</option>
                                        @foreach($turmas as $turma)
                                            <option value="{{ $turma->id }}">{{ $turma->nome_empresa }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <!-- 2. Cliente -->
                            <div class="md:col-span-1">
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">2. Cliente</label>
                                <select name="cliente_id" class="w-full border-2 border-black rounded text-sm font-bold" required>
                                    <option value="">Selecione...</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}">{{ $cliente->nome_razao_social }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Data do Simulador -->
                            <div class="md:col-span-1">
                                <label class="block text-xs font-black text-gray-500 uppercase mb-1">Data Atual (Simulação)</label>
                                <input type="text" id="campo_data_simulacao" value="---" disabled class="w-full bg-gray-800 border-2 border-black text-green-400 font-mono text-center font-bold rounded">
                            </div>

                            <!-- Entrega Solicitada -->
                            <div class="md:col-span-1">
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">Data de Entrega</label>
                                <input type="date" name="data_entrega_solicitada" class="w-full border-2 border-black rounded text-sm font-bold" required>
                            </div>
                        </div>

                        <!-- Área de Itens (Filtro via data-attribute) -->
                        <div class="bg-gray-50 border-2 border-dashed border-gray-400 p-4 rounded-lg mb-6">
                            <h4 class="text-sm font-black text-gray-600 uppercase mb-3 border-b border-gray-300 pb-1">Itens do Pedido</h4>
                            <div id="itens-container" class="space-y-3">
                                <div class="grid grid-cols-1 md:grid-cols-6 gap-2 item-row items-end">
                                    <div class="md:col-span-4">
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase">Produto (Filtrado por Empresa)</label>
                                        <select name="itens[0][produto_id]" class="select-produto w-full border-2 border-gray-300 rounded text-sm focus:border-black" required>
                                            <option value="">Selecione o produto...</option>
                                            @foreach($produtos as $produto)
                                                {{-- O SEGREDO ESTÁ AQUI: data-turma identifica o dono --}}
                                                <option value="{{ $produto->id }}" data-turma="{{ $produto->turma_id ?? 'global' }}">
                                                    {{ $produto->nome }} (R$ {{ number_format($produto->preco_venda, 2, ',', '.') }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="md:col-span-1">
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase">Qtd</label>
                                        <input type="number" name="itens[0][quantidade]" min="1" value="1" class="w-full border-2 border-gray-300 rounded text-sm text-center font-bold focus:border-black" required>
                                    </div>
                                    <div class="md:col-span-1"></div>
                                </div>
                            </div>
                            <button type="button" onclick="adicionarItem()" class="mt-4 text-indigo-600 text-xs font-black uppercase hover:text-indigo-800 flex items-center gap-1 border-2 border-indigo-100 bg-indigo-50 px-3 py-1 rounded hover:border-indigo-300 transition">
                                <span>+</span> Adicionar Outro Item
                            </button>
                        </div>

                        <div class="flex justify-end">
                            {{-- Botão dispara o Modal --}}
                            <button type="button" @click="abrirConfirmacao()" class="bg-green-600 text-white px-8 py-3 rounded border-2 border-black shadow-[4px_4px_0px_0px_black] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none transition text-sm font-black uppercase flex items-center gap-2">
                                <span>💾</span> Salvar Pedido
                            </button>
                        </div>
                    </form>
                </div>

                {{-- MODAL DE CONFIRMAÇÃO (ESTILO PROFESSOR MASTER) --}}
                <div x-show="showConfirm" 
                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
                     style="display: none;">
                    
                    <div class="bg-white border-4 border-black p-8 max-w-sm w-full shadow-[15px_15px_0px_0px_rgba(0,0,0,1)]">
                        <div class="text-center mb-6">
                            <span class="text-5xl">🏦</span>
                            <h4 class="text-2xl font-black uppercase mt-4 tracking-tighter">Gerar Pedido?</h4>
                            <p class="text-[10px] font-bold text-gray-400 uppercase italic">Ação Master do Professor</p>
                        </div>

                        <div class="bg-yellow-50 border-2 border-black p-4 mb-6 space-y-3">
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase leading-none">Empresa Alvo:</p>
                                <p class="text-sm font-black uppercase text-indigo-900" x-text="empresaNome"></p>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase leading-none">Cliente Selecionado:</p>
                                <p class="text-sm font-black uppercase text-black" x-text="clienteNome"></p>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase leading-none">Total de Itens:</p>
                                <p class="text-sm font-black text-black" x-text="totalItens + ' produto(s)'"></p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button @click="showConfirm = false" :disabled="loading" class="flex-1 bg-white border-2 border-black py-2 font-black uppercase text-xs hover:bg-gray-100 transition">
                                Ajustar
                            </button>
                            <button @click="loading = true; document.getElementById('formPedidoProfessor').submit();" 
                                    :disabled="loading"
                                    class="flex-1 bg-green-500 text-white border-2 border-black py-2 font-black uppercase text-xs shadow-[3px_3px_0px_0px_black] hover:shadow-none transition disabled:bg-gray-400">
                                <span x-show="!loading">Confirmar</span>
                                <span x-show="loading">Processando...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 1. FILTROS -->
            <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                <div class="bg-gray-100 border-b-4 border-black p-4 flex items-center gap-2">
                    <span class="text-xl">🔍</span>
                    <h3 class="font-black text-lg text-gray-800 uppercase tracking-wide">Filtrar Pedidos</h3>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('professor.pedidos-venda.index') }}" method="GET">
                        
                        {{-- SE VIER TRAVADO, MANTÉM O ID OCULTO PARA NÃO PERDER O FILTRO --}}
                        @if($turmaSelecionada)
                            <input type="hidden" name="filtro_turma" value="{{ $turmaSelecionada->id }}">
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                            
                            <!-- 1. NOME DA EMPRESA (Busca) -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">Nome da Empresa</label>
                                @if($turmaSelecionada)
                                    {{-- Campo Travado Visualmente --}}
                                    <input type="text" value="{{ $turmaSelecionada->nome_empresa }}" disabled class="w-full bg-gray-200 border-2 border-gray-400 rounded text-sm font-bold text-gray-500 cursor-not-allowed">
                                @else
                                    {{-- Menu Suspenso --}}
                                    <select name="busca_empresa" class="w-full border-2 border-black rounded text-sm focus:ring-0 focus:border-indigo-600 font-bold shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)]">
                                        <option value="">Todas as Empresas</option>
                                        @foreach($turmas as $turma)
                                            @if($turma->nome_empresa)
                                                <option value="{{ $turma->nome_empresa }}" {{ request('busca_empresa') == $turma->nome_empresa ? 'selected' : '' }}>
                                                    {{ $turma->nome_empresa }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <!-- 2. TURMA (ID) -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">Turma (Classe)</label>
                                @if($turmaSelecionada)
                                    <input type="text" value="{{ $turmaSelecionada->nome }}" disabled class="w-full bg-gray-200 border-2 border-gray-400 rounded text-sm font-bold text-gray-500 cursor-not-allowed">
                                @else
                                    <select name="filtro_turma" class="w-full border-2 border-black rounded text-sm focus:ring-0 focus:border-indigo-600 font-bold shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)]">
                                        <option value="">Todas</option>
                                        @foreach($turmas as $turma)
                                            <option value="{{ $turma->id }}" {{ request('filtro_turma') == $turma->id ? 'selected' : '' }}>
                                                {{ $turma->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <!-- 3. CLIENTE -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">Cliente</label>
                                <select name="filtro_cliente" class="w-full border-2 border-black rounded text-sm focus:ring-0 focus:border-indigo-600 font-bold shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)]">
                                    <option value="">Todos</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ request('filtro_cliente') == $cliente->id ? 'selected' : '' }}>
                                            {{ $cliente->nome_razao_social }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- 4. PRODUTO -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">Produto</label>
                                <select name="filtro_produto" class="w-full border-2 border-black rounded text-sm focus:ring-0 focus:border-indigo-600 font-bold shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)]">
                                    <option value="">Todos</option>
                                    @foreach($produtos as $produto)
                                        <option value="{{ $produto->id }}" {{ request('filtro_produto') == $produto->id ? 'selected' : '' }}>
                                            {{ $produto->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- 5. CIDADE -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">Cidade</label>
                                <select name="filtro_cidade" class="w-full border-2 border-black rounded text-sm focus:ring-0 focus:border-indigo-600 font-bold shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)]">
                                    <option value="">Todas</option>
                                    @foreach($cidadesUnicas as $cidade)
                                        <option value="{{ $cidade }}" {{ request('filtro_cidade') == $cidade ? 'selected' : '' }}>
                                            {{ $cidade }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- 6. UF e STATUS -->
                            <div class="md:col-span-1">
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">UF</label>
                                <input type="text" name="filtro_estado" value="{{ request('filtro_estado') }}" maxlength="2" class="w-full border-2 border-black rounded text-sm focus:ring-0 focus:border-indigo-600 font-bold text-center uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)]">
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">Status</label>
                                <select name="filtro_status" class="w-full border-2 border-black rounded text-sm focus:ring-0 focus:border-indigo-600 font-bold shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)]">
                                    <option value="">Todos</option>
                                    <option value="Novo" {{ request('filtro_status') == 'Novo' ? 'selected' : '' }}>Novo</option>
                                    <option value="Em Produção" {{ request('filtro_status') == 'Em Produção' ? 'selected' : '' }}>Em Produção</option>
                                    <option value="Concluído" {{ request('filtro_status') == 'Concluído' ? 'selected' : '' }}>Concluído</option>
                                    <option value="Faturado" {{ request('filtro_status') == 'Faturado' ? 'selected' : '' }}>Faturado</option>
                                    <option value="Cancelado" {{ request('filtro_status') == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-4 items-center border-t-2 border-dashed border-gray-300 pt-4">
                            @if(!$turmaSelecionada)
                                <a href="{{ route('professor.pedidos-venda.index') }}" class="text-gray-500 hover:text-red-600 font-bold text-xs uppercase tracking-wider underline">Limpar Filtros</a>
                            @endif
                            <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none transition text-xs font-black uppercase">
                                Filtrar Resultados
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 3. LISTAGEM (TABELA INDUSTRIAL) -->
            <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,0.2)] overflow-hidden">
                <div class="bg-yellow-400 p-4 border-b-4 border-black flex items-center justify-between">
                    <h3 class="font-black text-xl text-black uppercase tracking-tighter">Carteira de Pedidos</h3>
                    <span class="bg-black text-white text-xs font-bold px-3 py-1 rounded">{{ count($pedidos) }} Pedidos</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-black text-white uppercase text-xs">
                            <tr>
                                <th class="py-3 px-4 text-left">ID</th>
                                <th class="py-3 px-4 text-left">Empresa (Turma)</th>
                                <th class="py-3 px-4 text-left">Cliente</th>
                                <th class="py-3 px-4 text-center">Entrega</th>
                                <th class="py-3 px-4 text-right">Total</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($pedidos as $pedido)
                                <tr class="hover:bg-yellow-50 transition group {{ $pedido->status == 'Cancelado' ? 'opacity-50 bg-gray-100' : '' }}">
                                    <td class="py-3 px-4 font-mono font-bold text-gray-500">#{{ $pedido->id }}</td>
                                    
                                    <td class="py-3 px-4">
                                        <span class="bg-indigo-100 text-indigo-800 border border-indigo-200 py-0.5 px-2 rounded text-[10px] font-black uppercase">
                                            {{ $pedido->turma->nome ?? '---' }}
                                        </span>
                                    </td>
                                    
                                    <td class="py-3 px-4 font-bold text-gray-800 truncate max-w-xs">
                                        {{ $pedido->cliente->nome_razao_social ?? 'Cliente Removido' }}
                                    </td>
                                    
                                    <td class="py-3 px-4 text-center font-mono text-xs">
                                        {{ \Carbon\Carbon::parse($pedido->data_entrega_solicitada)->format('d/m/Y') }}
                                    </td>
                                    
                                    <td class="py-3 px-4 text-right font-black text-green-600">
                                        R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}
                                    </td>
                                    
                                    <td class="py-3 px-4 text-center">
                                        @php
                                            $corStatus = match($pedido->status) {
                                                'Novo' => 'bg-blue-100 text-blue-800 border-blue-300',
                                                'Em Produção' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                                'Concluído' => 'bg-green-100 text-green-800 border-green-300',
                                                'Faturado' => 'bg-gray-800 text-white border-black',
                                                'Cancelado' => 'bg-red-100 text-red-800 border-red-300',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                        @endphp
                                        <span class="{{ $corStatus }} border py-1 px-2 rounded text-[10px] font-black uppercase tracking-wide">
                                            {{ $pedido->status }}
                                        </span>
                                    </td>
                                    
                                    <td class="py-3 px-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <!-- Ver -->
                                            <a href="{{ route('professor.pedidos-venda.show', $pedido->id) }}" class="text-blue-600 hover:text-blue-800 border border-blue-200 hover:border-blue-400 bg-blue-50 p-1.5 rounded transition" title="Ver Detalhes">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                            </a>
                                            
                                            @if($pedido->status != 'Cancelado' && $pedido->status != 'Faturado')
                                                <!-- Editar -->
                                                <a href="{{ route('professor.pedidos-venda.edit', $pedido->id) }}" class="text-yellow-600 hover:text-yellow-800 border border-yellow-200 hover:border-yellow-400 bg-yellow-50 p-1.5 rounded transition" title="Editar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0015.75 21H5.25A2.25 2.25 0 003 18.75V8.25A2.25 2.25 0 005.25 6H10" /></svg>
                                                </a>

                                                <!-- BOTÃO CANCELAR SEGURO -->
                                                <x-modal-senha-mestra 
                                                    id="del-ped-{{ $pedido->id }}" 
                                                    action="{{ route('professor.pedidos-venda.destroy', $pedido->id) }}" 
                                                    method="DELETE"
                                                    class="text-red-600 hover:text-red-800 border border-red-200 hover:border-red-400 bg-red-50 p-1.5 rounded transition"
                                                    title="CANCELAR PEDIDO?"
                                                    mensagem="Você vai cancelar o pedido #{{ $pedido->id }}. Ele ficará inativo.">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                                </x-modal-senha-mestra>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-10 text-center text-gray-400 italic">
                                        Nenhum pedido encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        const datasTurmas = {
            @foreach($turmas as $t)
                "{{ $t->id }}": "{{ $t->data_jogo->format('d/m/Y H:i') }}",
            @endforeach
        };

        // FUNÇÃO PARA FILTRAR OS PRODUTOS
        function filtrarProdutosPorEmpresa(turmaId) {
            const selectsProdutos = document.querySelectorAll('.select-produto');
            
            selectsProdutos.forEach(select => {
                const options = select.options;
                
                for (let i = 0; i < options.length; i++) {
                    const opt = options[i];
                    const turmaDono = opt.getAttribute('data-turma');

                    // Se não tiver turma selecionada, esconde tudo menos o "Selecione"
                    if (!turmaId) {
                        opt.style.display = i === 0 ? "block" : "none";
                        continue;
                    }

                    // Mostra se for da turma selecionada OU se for um produto Global
                    if (turmaDono == turmaId || turmaDono === 'global' || i === 0) {
                        opt.style.display = "block";
                    } else {
                        opt.style.display = "none";
                    }
                }
                // Reseta o valor para forçar nova escolha
                select.value = "";
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const selectTurma = document.getElementById('turma_id_novo_pedido');
            const inputData = document.getElementById('campo_data_simulacao');

            if (selectTurma && selectTurma.value) {
                inputData.value = datasTurmas[selectTurma.value] || "---";
                filtrarProdutosPorEmpresa(selectTurma.value); // Inicia filtrado se já vier selecionado
            }

            if (selectTurma) {
                selectTurma.addEventListener('change', function() {
                    const idSelecionado = this.value;
                    inputData.value = datasTurmas[idSelecionado] || "---";
                });
            }
        });

        let itemIndex = 1;
        function adicionarItem() {
            const turmaIdAtiva = document.getElementById('turma_id_novo_pedido').value;
            const container = document.getElementById('itens-container');
            const novoItem = document.createElement('div');
            novoItem.className = 'grid grid-cols-1 md:grid-cols-6 gap-2 item-row items-end border-t border-dashed border-gray-300 pt-2 mt-2';
            
            novoItem.innerHTML = `
                <div class="md:col-span-4">
                    <select name="itens[${itemIndex}][produto_id]" class="select-produto w-full border-2 border-gray-300 rounded text-sm focus:border-black" required>
                        <option value="">Selecione...</option>
                        @foreach($produtos as $produto)
                            <option value="{{ $produto->id }}" data-turma="{{ $produto->turma_id ?? 'global' }}">
                                {{ $produto->nome }} (R$ {{ number_format($produto->preco_venda, 2, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-1">
                    <input type="number" name="itens[${itemIndex}][quantidade]" min="1" value="1" class="w-full border-2 border-gray-300 rounded text-sm text-center font-bold focus:border-black" required>
                </div>
                <div class="md:col-span-1">
                    <button type="button" onclick="removerItem(this)" class="w-full bg-red-100 text-red-600 border border-red-200 rounded text-xs font-bold py-2 hover:bg-red-200 uppercase">Remover</button>
                </div>
            `;
            container.appendChild(novoItem);
            itemIndex++;
            
            // Chama o filtro para a nova linha criada
            filtrarProdutosPorEmpresa(turmaIdAtiva);
        }

        function removerItem(botao) {
            botao.closest('.item-row').remove();
        }
    </script>

    
</x-app-layout>