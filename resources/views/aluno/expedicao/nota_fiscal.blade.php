<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DANFE - Nota Fiscal #{{ $nota->numero_nota }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Estilos específicos para impressão e bordas de NF */
        .nf-box { border: 1px solid black; padding: 4px; font-size: 10px; }
        .nf-label { font-weight: bold; text-transform: uppercase; font-size: 8px; display: block; margin-bottom: 1px; }
        .nf-content { font-weight: bold; font-size: 11px; }
        .nf-title { font-weight: bold; text-transform: uppercase; font-size: 9px; background-color: #f3f4f6; border-bottom: 1px solid black; padding: 2px; }
        
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; margin: 0; padding: 0; }
            .bg-gray-200 { background-color: white !important; }
        }
    </style>
</head>
<body class="bg-gray-200 p-8 font-sans">

    <!-- Botões de Ação (Não saem na impressão) -->
    <div class="max-w-4xl mx-auto mb-6 flex justify-between items-center no-print">
        
        {{-- BOTÃO VOLTAR DINÂMICO --}}
        @if(Auth::user()->tipo == 'professor')
            <a href="{{ route('professor.monitoramento.expedicao', $nota->pedido->turma_id) }}" 
               class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                ⬅ Voltar à Expedição
            </a>
        @else
            <a href="{{ route('aluno.expedicao.dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 font-bold text-sm">
                &larr; Voltar
            </a>
        @endif

        <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 font-bold text-sm flex items-center gap-2 shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
            IMPRIMIR NOTA FISCAL
        </button>
    </div>

    <!-- O DOCUMENTO DANFE -->
    <div class="max-w-4xl mx-auto bg-white p-2 shadow-2xl border border-gray-400" id="area-impressao">
        
        <!-- TOPO: CANHOTO -->
        <div class="border border-black mb-2 flex">
            <div class="w-3/4 border-r border-black p-1">
                <span class="nf-label">RECEBEMOS DE {{ strtoupper($nota->pedido->turma->nome_empresa) }} OS PRODUTOS CONSTANTES NA NOTA FISCAL INDICADA AO LADO</span>
                <div class="mt-4 text-center text-xs">DATA DE RECEBIMENTO _____________ / _____________ / _____________</div>
            </div>
            <div class="w-1/4 p-1 text-center flex flex-col justify-center">
                <span class="nf-label">NF-e</span>
                <span class="text-lg font-bold">Nº {{ $nota->numero_nota }}</span>
                <span class="text-xs">SÉRIE {{ $nota->serie }}</span>
            </div>
        </div>

        <!-- CABEÇALHO (DADOS DO EMITENTE ATUALIZADOS) -->
        <div class="border border-black mb-2 flex">
            <!-- EMITENTE REAL DA TURMA -->
            <div class="w-1/2 border-r border-black p-2 flex flex-col items-center justify-center text-center">
                <h1 class="text-xl font-black uppercase leading-tight">{{ $nota->pedido->turma->nome_empresa }}</h1>
                
                {{-- Endereço dinâmico da Turma --}}
                <p class="text-[10px] font-bold mt-1 uppercase">
                    {{ $nota->pedido->turma->rua }}, {{ $nota->pedido->turma->numero }} - {{ $nota->pedido->turma->bairro }}
                </p>
                <p class="text-[10px] uppercase">
                    {{ $nota->pedido->turma->cidade }} - {{ $nota->pedido->turma->estado }} | CNPJ: {{ $nota->pedido->turma->cnpj ?? '00.000.000/0001-00' }}
                </p>
                <p class="text-[10px] font-bold">Fone: {{ $nota->pedido->turma->telefone ?? '---' }}</p>
                
                <p class="text-[8px] text-gray-500 mt-1 italic">{{ $nota->pedido->turma->nome }}</p>
            </div>
            
            <!-- DANFE IDENTIFICAÇÃO -->
            <div class="w-1/6 border-r border-black p-1 text-center">
                <h2 class="font-bold text-lg leading-none">DANFE</h2>
                <p class="text-[7px] leading-tight mt-1">Documento Auxiliar da Nota Fiscal Eletrônica</p>
                <div class="my-1 text-[10px]">
                    0 - Entrada<br>
                    1 - Saída <span class="border border-black px-1 font-bold">1</span>
                </div>
                <p class="font-bold text-sm">Nº {{ $nota->numero_nota }}</p>
                <p class="text-xs">SÉRIE {{ $nota->serie }}</p>
            </div>

            <!-- CHAVE DE ACESSO -->
            <div class="w-1/3 p-2 flex flex-col">
                <div class="w-full mb-1 flex justify-center overflow-hidden" style="height: 40px;">
                    {!! $barcode !!}
                </div>
                <div class="nf-label">CHAVE DE ACESSO</div>
                <div class="nf-content bg-gray-100 text-center p-1 border border-gray-300 text-[10px] font-mono">
                    {{ implode(' ', str_split($nota->chave_acesso, 4)) }}
                </div>
                <div class="mt-2 text-[8px] text-center text-gray-500 uppercase">
                    Consulta de autenticidade no portal nacional da NF-e
                </div>
            </div>
        </div>

        <!-- NATUREZA DA OPERAÇÃO E PROTOCOLO (DATA DO JOGO) -->
        <div class="border border-black mb-2 flex">
            <div class="w-3/5 border-r border-black p-1">
                <span class="nf-label">NATUREZA DA OPERAÇÃO</span>
                <span class="nf-content">VENDA DE PRODUÇÃO DO ESTABELECIMENTO</span>
            </div>
            <div class="w-2/5 p-1">
                <span class="nf-label">PROTOCOLO DE AUTORIZAÇÃO DE USO</span>
                {{-- Aqui usamos a data_jogo da turma para o protocolo --}}
                <span class="nf-content">
                    {{ rand(1000000000, 9999999999) }} - {{ \Carbon\Carbon::parse($nota->pedido->turma->data_jogo)->format('d/m/Y H:i:s') }}
                </span>
            </div>
        </div>

        <!-- DESTINATÁRIO -->
        <div class="nf-title">DESTINATÁRIO / REMETENTE</div>
        <div class="border border-black mb-2">
            <div class="flex border-b border-black">
                <div class="w-3/5 border-r border-black p-1">
                    <span class="nf-label">NOME / RAZÃO SOCIAL</span>
                    <span class="nf-content uppercase">{{ $nota->pedido->cliente->nome_razao_social }}</span>
                </div>
                <div class="w-1/4 border-r border-black p-1">
                    <span class="nf-label">CNPJ / CPF</span>
                    <span class="nf-content font-mono">{{ $nota->pedido->cliente->cnpj_cpf ?? '000.000.000-00' }}</span>
                </div>
                <div class="w-15 p-1">
                    <span class="nf-label">DATA DA EMISSÃO</span>
                    {{-- Data de emissão também segue a data_jogo --}}
                    <span class="nf-content">{{ \Carbon\Carbon::parse($nota->data_emissao)->format('d/m/Y') }}</span>
                </div>
            </div>
            <div class="flex">
                <div class="w-1/2 border-r border-black p-1">
                    <span class="nf-label">ENDEREÇO</span>
                    <span class="nf-content uppercase">{{ $nota->pedido->cliente->rua ?? 'Rua não informada' }}, {{ $nota->pedido->cliente->numero ?? 'S/N' }}</span>
                </div>
                <div class="w-1/4 border-r border-black p-1">
                    <span class="nf-label">BAIRRO / DISTRITO</span>
                    <span class="nf-content uppercase">{{ $nota->pedido->cliente->bairro ?? '-' }}</span>
                </div>
                <div class="w-1/6 border-r border-black p-1">
                    <span class="nf-label">MUNICÍPIO</span>
                    <span class="nf-content uppercase">{{ $nota->pedido->cliente->cidade ?? '-' }}</span>
                </div>
                <div class="w-20 p-1">
                    <span class="nf-label">UF</span>
                    <span class="nf-content uppercase">{{ $nota->pedido->cliente->estado ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- CÁLCULO DO IMPOSTO -->
        <div class="nf-title">CÁLCULO DO IMPOSTO</div>
        <div class="border border-black mb-2 flex text-center">
            <div class="w-1/5 border-r border-black p-1">
                <span class="nf-label">BASE DE CÁLCULO DO ICMS</span>
                <span class="nf-content">0,00</span>
            </div>
            <div class="w-1/5 border-r border-black p-1">
                <span class="nf-label">VALOR DO ICMS</span>
                <span class="nf-content">0,00</span>
            </div>
            <div class="w-1/5 border-r border-black p-1">
                <span class="nf-label">BASE DE CÁLC. ICMS S.T.</span>
                <span class="nf-content">0,00</span>
            </div>
            <div class="w-1/5 border-r border-black p-1">
                <span class="nf-label">VALOR DO ICMS S.T.</span>
                <span class="nf-content">0,00</span>
            </div>
            <div class="w-1/5 p-1 bg-gray-100">
                <span class="nf-label">VALOR TOTAL DOS PRODUTOS</span>
                <span class="nf-content">R$ {{ number_format($nota->valor_total, 2, ',', '.') }}</span>
            </div>
        </div>

        <!-- TRANSPORTADOR -->
        <div class="nf-title">TRANSPORTADOR / VOLUMES TRANSPORTADOS</div>
        <div class="border border-black mb-2 p-1">
            <span class="nf-label">RAZÃO SOCIAL</span>
            <span class="nf-content uppercase">O PRÓPRIO (FÁBRICA-ESCOLA) - FRETE POR CONTA DO EMITENTE</span>
        </div>

        <!-- DADOS DOS PRODUTOS -->
        <div class="nf-title">DADOS DOS PRODUTOS / SERVIÇOS</div>
        <div class="border border-black mb-2 min-h-[350px]">
            <table class="w-full text-[10px] border-collapse">
                <thead class="border-b border-black bg-gray-50">
                    <tr>
                        <th class="border-r border-black p-1">CÓDIGO</th>
                        <th class="border-r border-black p-1 w-1/2">DESCRIÇÃO DO PRODUTO / SERVIÇO</th>
                        <th class="border-r border-black p-1">NCM/SH</th>
                        <th class="border-r border-black p-1">UND</th>
                        <th class="border-r border-black p-1 text-center">QTD</th>
                        <th class="border-r border-black p-1 text-right">VLR. UNIT.</th>
                        <th class="p-1 text-right">VLR. TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($nota->pedido->itens as $item)
                        <tr class="border-b border-gray-300">
                            <td class="border-r border-black p-1 text-center font-mono">{{ $item->produto->sku }}</td>
                            <td class="border-r border-black p-1 font-bold uppercase">{{ $item->produto->nome }}</td>
                            <td class="border-r border-black p-1 text-center font-mono">3926.10</td>
                            <td class="border-r border-black p-1 text-center">UN</td>
                            <td class="border-r border-black p-1 text-center font-bold">{{ $item->quantidade }}</td>
                            <td class="border-r border-black p-1 text-right font-mono">R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</td>
                            <td class="p-1 text-right font-black font-mono">R$ {{ number_format($item->preco_total_item, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- DADOS ADICIONAIS -->
        <div class="nf-title">DADOS ADICIONAIS</div>
        <div class="border border-black h-28 p-1">
            <span class="nf-label">INFORMAÇÕES COMPLEMENTARES</span>
            <p class="text-[10px] mt-1 leading-tight">
                "ESTE DOCUMENTO É UMA SIMULAÇÃO DIDÁTICA PARA FINS EDUCACIONAIS. NÃO POSSUI VALOR COMERCIAL, LEGAL OU FISCAL. EMISSÃO REALIZADA EM AMBIENTE DE TREINAMENTO INDUSTRIAL.". <br>
                <strong>SIMULADOR FABRICA DIGITAL.</strong> <br>
                Unidade Fabril: {{ strtoupper($nota->pedido->turma->nome_empresa) }} <br>
                Turma Responsável: {{ $nota->pedido->turma->nome }}. <br>
                Emitido em tempo simulado: {{ \Carbon\Carbon::parse($nota->pedido->turma->data_jogo)->format('d/m/Y H:i') }}.
            </p>
        </div>

    </div>

</body>
</html>