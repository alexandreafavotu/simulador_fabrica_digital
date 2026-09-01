<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span>✨</span> {{ __('Revisão Pedagógica da IA (Game Master)') }}
            </h2>
            <a href="{{ route('professor.avaliacoes.importar') }}" 
               class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase">
                ⬅ Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-yellow-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- EXPLICATIVO DE REVISÃO --}}
            <div class="bg-indigo-100 border-4 border-black p-6 rounded-xl shadow-[6px_6px_0px_0px_black]">
                <h4 class="font-black text-lg text-indigo-950 uppercase mb-2 flex items-center gap-2">
                    <span>🕵️‍♂️</span> Controle Pedagógico: Edite e Autorize a Matriz
                </h4>
                <p class="text-xs text-indigo-900 font-bold leading-relaxed">
                    Abaixo estão as capacidades e conhecimentos estruturados pela inteligência artificial. Como Game Master, você tem a palavra final. Você pode editar qualquer descrição, alterar as métricas associadas do ERP, e desmarcar o checkbox de qualquer capacidade que não queira importar para o sistema.
                </p>
            </div>

            {{-- FORMULÁRIO DE CONFIRMAÇÃO GERAL --}}
            <form method="POST" action="{{ route('professor.avaliacoes.confirmar_importar') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="curso" value="{{ $curso }}">

                {{-- LOOP DAS COMPETÊNCIAS EXTRAÍDAS PELA IA --}}
                @foreach($competenciasExtraidas as $index => $comp)
                    @php
                        $categoria = $comp['categoria'] ?? 'tecnica';
                        $tipoAvaliacao = $comp['tipo_avaliacao'] ?? 'manual';
                        $metricaChave = $comp['metrica_chave'] ?? '';
                    @endphp

                    <div class="bg-white border-4 border-black rounded-xl shadow-[6px_6px_0px_0px_black] overflow-hidden relative">
                        
                        {{-- CABEÇALHO DO CARD --}}
                        <div class="p-4 border-b-2 border-black flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-gray-50">
                            
                            {{-- Checkbox de Importação --}}
                            <div class="flex items-center gap-3">
                                <input type="checkbox" name="competencias[{{ $index }}][importar]" value="1" checked
                                       class="w-6 h-6 border-4 border-black text-indigo-600 focus:ring-0 cursor-pointer">
                                <div>
                                    <span class="block text-xs font-black text-gray-700 uppercase leading-none">Importar esta capacidade?</span>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase">Desmarque para ignorar</span>
                                </div>
                            </div>

                            <span class="text-[9px] bg-black text-yellow-400 border border-black px-2 py-0.5 rounded font-black uppercase shadow-sm">
                                Capacidade #{{ $index + 1 }}
                            </span>
                        </div>

                        {{-- CAMPOS DE EDIÇÃO EM TELA --}}
                        <div class="p-6 space-y-4">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- 1. Unidade Curricular --}}
                                <div>
                                    <label class="block text-[10px] font-black text-gray-600 uppercase mb-1">Unidade Curricular / Elemento</label>
                                    <input type="text" name="competencias[{{ $index }}][unidade_curricular]" value="{{ $comp['unidade_curricular'] ?? '' }}" required
                                           class="w-full border-2 border-black rounded p-2 text-xs font-bold focus:ring-0 focus:border-indigo-600 bg-white">
                                </div>

                                {{-- 2. Categoria --}}
                                <div>
                                    <label class="block text-[10px] font-black text-gray-600 uppercase mb-1">Categoria de Competência</label>
                                    <select name="competencias[{{ $index }}][categoria]" required
                                            class="w-full border-2 border-black rounded p-2 text-xs font-bold focus:ring-0 focus:border-indigo-600 bg-white">
                                        <option value="tecnica" @selected($categoria == 'tecnica')>TÉCNICA</option>
                                        <option value="organizacional" @selected($categoria == 'organizacional')>ORGANIZACIONAL</option>
                                        <option value="socioemocional" @selected($categoria == 'socioemocional')>SOCIOEMOCIONAL</option>
                                        <option value="conhecimento" @selected($categoria == 'conhecimento')>CONHECIMENTO</option>
                                    </select>
                                </div>
                            </div>

                            {{-- 3. Nome Resumido --}}
                            <div>
                                <label class="block text-[10px] font-black text-gray-600 uppercase mb-1">Nome Resumido do Padrão de Desempenho</label>
                                <input type="text" name="competencias[{{ $index }}][nome]" value="{{ $comp['nome'] ?? '' }}" required
                                       class="w-full border-2 border-black rounded p-2 text-xs font-black focus:ring-0 focus:border-indigo-600 bg-white">
                            </div>

                            {{-- 4. Descrição Pedagógica --}}
                            <div>
                                <label class="block text-[10px] font-black text-gray-600 uppercase mb-1">Descrição Pedagógica Detalhada no Simulador</label>
                                <textarea name="competencias[{{ $index }}][descricao]" rows="2" required
                                          class="w-full border-2 border-black rounded p-2 text-xs font-bold focus:ring-0 focus:border-indigo-600 bg-white">{{ $comp['descricao'] ?? '' }}</textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-dashed border-gray-200 pt-4 mt-2">
                                {{-- 5. Tipo de Avaliação --}}
                                <div>
                                    <label class="block text-[10px] font-black text-gray-600 uppercase mb-1">Tipo de Avaliação</label>
                                    <select name="competencias[{{ $index }}][tipo_avaliacao]" required
                                            class="w-full border-2 border-black rounded p-2 text-xs font-bold focus:ring-0 focus:border-indigo-600 bg-white">
                                        <option value="manual" @selected($tipoAvaliacao == 'manual')>Manual (Atribuída pelo Professor)</option>
                                        <option value="automatica" @selected($tipoAvaliacao == 'automatica')>Automática (Rastreada pelo ERP)</option>
                                    </select>
                                </div>

                                {{-- 6. Métrica do ERP --}}
                                <div>
                                    <label class="block text-[10px] font-black text-gray-600 uppercase mb-1">Métrica de Chave de Negócio Associada</label>
                                    <select name="competencias[{{ $index }}][metrica_chave]"
                                            class="w-full border-2 border-black rounded p-2 text-xs font-bold focus:ring-0 focus:border-indigo-600 bg-white">
                                        <option value="null" @selected(!$metricaChave || $metricaChave == 'null')>-- Nenhuma (Manual) --</option>
                                        <option value="wms_picking_concluido" @selected($metricaChave == 'wms_picking_concluido')>WMS: Picking de Separação Concluído</option>
                                        <option value="producao_material_solicitado" @selected($metricaChave == 'producao_material_solicitado')>Produção: Solicitações de Material Efetuadas</option>
                                        <option value="compras_conferencia_concluida" @selected($metricaChave == 'compras_conferencia_concluida')>Compras: Conferências Concluídas na Doca</option>
                                        <option value="compras_recusa_justificada" @selected($metricaChave == 'compras_recusa_justificada')>Compras: Recusas de Cargas Justificadas</option>
                                        <option value="producao_refugo_apontado" @selected($metricaChave == 'producao_refugo_apontado')>Produção: Total de Refugos Registrados</option>
                                        <option value="producao_lote_concluido" @selected($metricaChave == 'producao_lote_concluido')>Produção: Lotes Concluídos / Embalados</option>
                                        <option value="expedicao_faturamento_concluido" @selected($metricaChave == 'expedicao_faturamento_concluido')>Expedição: Notas Fiscais de Faturamento Emitidas</option>
                                        <option value="wms_enderecamento_concluido" @selected($metricaChave == 'wms_enderecamento_concluido')>WMS: Endereçamentos Concluídos no Mapa</option>
                                        <option value="wms_movimentacao_total" @selected($metricaChave == 'wms_movimentacao_total')>WMS: Total Geral de Movimentações</option>
                                        <option value="pcp_prioridade_mde" @selected($metricaChave == 'pcp_prioridade_mde')>PCP: Priorização e MRP Concluídos</option>
                                        <option value="financeiro_caixa_positivo" @selected($metricaChave == 'financeiro_caixa_positivo')>Financeiro: Manutenção de Caixa Saudável</option>
                                        <option value="bi_acesso_indicadores" @selected($metricaChave == 'bi_acesso_indicadores')>BI: Auditoria de Indicadores e Relatórios</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach

                {{-- BOTÕES DE CONFIRMAÇÃO GERAL --}}
                <div class="flex justify-end gap-4 border-t-4 border-dashed border-gray-400 pt-6">
                    <a href="{{ route('professor.avaliacoes.importar') }}" 
                       class="px-6 py-3 bg-white text-gray-700 border-2 border-black rounded font-black text-xs uppercase hover:bg-gray-200 transition">
                        Cancelar
                    </a>
                    
                    <button type="submit" 
                            class="px-8 py-3 bg-green-500 text-white rounded border-2 border-black font-black text-sm uppercase hover:bg-green-600 shadow-[6px_6px_0px_0px_black] transition transform hover:-translate-y-1 active:translate-y-[1px] active:shadow-none flex items-center gap-2">
                        <span>🎓</span> Autorizar e Salvar Matriz do Curso
                    </button>
                </div>

            </form>

        </div>
    </div>
</x-app-layout>