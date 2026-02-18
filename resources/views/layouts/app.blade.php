<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Fábrica-Escola SENAI') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Cores SENAI */
            .bg-senai-red { background-color: #E30613; }
            .border-senai-red { border-color: #E30613; }
            
            /* Rodapé Fixo */
            body { display: flex; flex-direction: column; min-height: 100vh; }
            main { flex: 1; }
            @media print {
    /* 1. Forçar o navegador a mostrar as cores e sombras */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        color-adjust: exact !important;
    }

    /* 2. Esconder elementos inúteis no papel (Botão de ajuda, menus, etc) */
    .no-print, #botao-ajuda, #sidebar, nav, .btn-voltar {
        display: none !important;
    }

    /* 3. Evitar que um card seja cortado no meio entre duas páginas */
    .bg-white, .card-industrial, tr, .rounded-xl {
        break-inside: avoid !important;
        page-break-inside: avoid !important;
    }

    /* 4. Ajustar as margens do papel */
    @page {
        margin: 1cm;
    }

    /* 5. Garantir que o fundo do site não fique cinza no papel */
    body {
        background-color: white !important;
    }
}
/* Notificação de Sucesso Industrial */
            #notificacao-sucesso {
                transform: translateY(-150%);
                transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            }
            #notificacao-sucesso.mostrar {
                transform: translateY(20px);
            }
        /* ======================================================= */
/* ACESSIBILIDADE FINAL: FORÇAR AMARELO EM QUALQUER ÍCONE SVG */
/* ======================================================= */
body.high-contrast {
    background-color: #000000 !important;
}

body.high-contrast div, 
body.high-contrast section, 
body.high-contrast main,
body.high-contrast header,
body.high-contrast nav,
body.high-contrast footer {
    background-color: #000000 !important;
    color: #ffff00 !important;
    border-color: #ffff00 !important;
    box-shadow: none !important;
    background-image: none !important;
}

/* A REGRA QUE VAI RESOLVER AS SETAS E A IMPRESSORA */
/* Ela diz: "Qualquer desenho SVG dentro do modo PCD deve ser amarelo" */
body.high-contrast svg {
    fill: #ffff00 !important; /* Pinta o recheio do desenho */
    stroke: #ffff00 !important; /* Pinta o contorno do desenho */
    filter: none !important; /* Desliga qualquer filtro que possa deixar branco */
}

/* Garante texto amarelo em tudo */
body.high-contrast td, body.high-contrast th, body.high-contrast p, body.high-contrast span, body.high-contrast h1, body.high-contrast h2, body.high-contrast h3, body.high-contrast li {
    color: #ffff00 !important;

}
/* REGRA PARA FORÇAR AMARELO EM TODA A TABELA */
body.high-contrast table, 
body.high-contrast table * {
    color: #FFFF00 !important;
    background-color: #000000 !important;
    border-color: #FFFF00 !important;
}

/* Botões, Manual e Botão de Ajuda */
body.high-contrast button, body.high-contrast a { border: 2px solid #ffff00 !important; background-color: #000 !important; color: #ff0 !important; }
body.high-contrast #ajuda-corpo, body.high-contrast #ajuda-corpo * { color: #ffff00 !important; }
body.high-contrast .fixed.bottom-6.right-6 button { background-color: #ffff00 !important; color: #000 !important; }


@if(Auth::user()->acessibilidade_motora)
    /* NAVEGAÇÃO MOTOR: DESTRUIÇÃO DE VARIÁVEIS TAILWIND */
    *:focus {
        /* 1. Mata a linha branca e azul do Tailwind */
        --tw-ring-offset-width: 0px !important;
        --tw-ring-width: 0px !important;
        --tw-ring-offset-color: transparent !important;
        box-shadow: none !important;

        /* 2. Força a Moldura Amarela Neon Grossa */
        outline: 8px solid #FFFF00 !important;
        outline-offset: 4px !important;
    }

    /* 3. Garante que o fundo preto não "coma" a borda */
    body.high-contrast *:focus {
        outline: 8px solid #FFFF00 !important;
        box-shadow: 0 0 0 12px #000000 !important;
    }
@endif

@if(Auth::user()->acessibilidade_motora)
    /* NAVEGAÇÃO MOTOR: DESTRUIÇÃO DE VARIÁVEIS TAILWIND (JÁ EXISTENTE) */
    *:focus {
        /* ... suas regras de foco já existentes ... */
    }

    /* AUMENTO DA ÁREA DE CLIQUE (PADDING / TAP-TARGET) */
    button, a.btn, input[type="submit"], input[type="button"] { /* Seletores para botões e links com 'btn' */
        min-width: 48px !important;    /* Largura mínima padrão de acessibilidade */
        min-height: 48px !important;   /* Altura mínima padrão de acessibilidade */
        padding: 12px 18px !important; /* Aumenta o padding para expandir a área de clique */
        box-sizing: border-box !important; /* Garante que padding não estoure layout */
    }

    /* Aumenta a área de clique para campos de texto também */
    input[type="text"], input[type="email"], input[type="password"], textarea, select {
        min-height: 44px !important; /* Altura mínima para facilitar o toque/clique */
        padding: 8px 12px !important; /* Ajusta o padding para conforto */
        box-sizing: border-box !important;
    }

    /* Ajuste específico para checkboxes/radios se necessário (visual) */
    input[type="checkbox"], input[type="radio"] {
        transform: scale(1.5) !important; /* Aumenta visualmente o checkbox/radio */
        margin-right: 10px !important;    /* Cria espaço */
    }
@endif

/* FILTRO PARA TRANSFORMAR PICTOGRAMAS EM AMARELO NEON (MODO PCD) */
body.high-contrast .img-pcd-yellow {
    filter: invert(100%) sepia(100%) saturate(5000%) hue-rotate(5deg) brightness(100%) contrast(100%) !important;
}
            
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-100 {{ Auth::user()?->acessibilidade_visual ? 'high-contrast' : '' }}">
        
        <div class="min-h-screen flex flex-col">
            <!-- Menu Superior -->
            @include('layouts.navigation')

            <!-- Cabeçalho da Página -->
            @if (isset($header))
                <header class="bg-white shadow border-b-4 border-senai-red">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                        <div>
                            {{ $header }}
                        </div>
                        <!-- Botão Imprimir -->
                        <button onclick="window.print()" class="text-sm text-gray-500 hover:text-gray-900 flex items-center gap-1">
                            @if(Auth::user()?->acessibilidade_visual)
    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="2" style="filter: none !important; stroke: #ffff00 !important;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.198-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"></path>
    </svg>
@else
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.198-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"></path></svg>
@endif
                            Imprimir
                        </button>
                    </div>
                </header>
            @endif

            <!-- Conteúdo Principal -->
            <main>
                {{ $slot }}
            </main>

            <!-- Rodapé -->
            <footer class="bg-black text-white py-4 mt-auto border-t-4 border-senai-red no-print">
                <div class="max-w-7xl mx-auto px-4 text-center text-sm">
                    <p>Fábrica Digital 1.5</p>
                     <!-- A FAIXINHA (SEPARADOR INDUSTRIAL) -->
        <div class="h-[3px] w-20 bg-indigo-600 mx-auto my-3 shadow-[0_0_10px_rgba(79,70,229,0.6)]"></div>
                    <p class="text-gray-200 text-xs mt-1">Desenvolvido por: Professor Alexandre Felix de Araujo</p>
                </div>
            </footer>
            
        </div>
        <!-- ======================================================= -->
    <!-- SISTEMA DE MANUAL E INSTRUÇÕES DINÂMICO (PROJETO 2.0)   -->
    <!-- ======================================================= -->
    
    <!-- 1. Botão Flutuante 3D -->
    <div class="fixed bottom-6 right-6 z-50">
        <button onclick="toggleAjuda()" 
                class="bg-red-600 hover:bg-red-700 text-white w-14 h-14 rounded-full shadow-[0_6px_0_0_#991b1b] active:shadow-none active:translate-y-1 transition-all duration-150 flex items-center justify-center text-2xl font-black border-2 border-red-800 no-print"
                title="Manual de Instruções">
            ?
        </button>
    </div>

    <!-- 2. Painel Lateral (Slide-over) -->
    <div id="painel-ajuda" class="fixed inset-0 z-[60] hidden">
        <!-- Fundo escurecido -->
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="toggleAjuda()"></div>
        
        <!-- Painel Branco -->
        <div class="absolute inset-y-0 right-0 w-full max-w-md bg-white shadow-2xl flex flex-col transform transition-transform duration-300 border-l-4 border-red-600">
            
            <!-- Cabeçalho do Manual -->
            <div class="p-6 bg-gray-50 border-b flex justify-between items-center">
                <div>
                    <h3 id="ajuda-titulo" class="text-xl font-black text-gray-800 uppercase tracking-tight">Instruções</h3>
                    <p class="text-[10px] text-red-600 font-bold uppercase tracking-widest">Manual da Fábrica Digital</p>
                </div>
                <button onclick="toggleAjuda()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>

            <!-- Conteúdo do Manual -->
            <div id="ajuda-corpo" class="flex-1 overflow-y-auto p-6 prose prose-slate" style="white-space: pre-wrap;">
                <!-- O texto virá do banco de dados aqui -->
                <div class="animate-pulse flex space-y-4 flex-col">
                    <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                    <div class="h-4 bg-gray-200 rounded"></div>
                    <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                </div>
            </div>

            <!-- ÁREA DO PROFESSOR (MODO EDIÇÃO) -->
            @if(Auth::user()->tipo == 'professor')
            
                <div class="p-4 bg-indigo-50 border-t border-indigo-100">
                    <button id="btn-editar-ajuda" onclick="habilitarEdicao()" class="w-full bg-indigo-600 text-white py-2 rounded font-bold text-xs uppercase shadow-md hover:bg-indigo-700 transition">
                        ✏️ Editar Instruções desta Tela
                    </button>
                    
                    <div id="editor-ajuda" class="hidden mt-4 space-y-3">
                        <input type="text" id="edit-titulo" class="w-full text-sm rounded border-gray-300" placeholder="Título da Seção">
                        <textarea id="edit-conteudo" rows="8" class="w-full text-sm rounded border-gray-300" placeholder="Escreva as instruções aqui (aceita HTML)..."></textarea>
                        <div class="flex gap-2">
                            <button onclick="salvarAjuda()" class="flex-1 bg-green-600 text-white py-2 rounded font-bold text-xs uppercase">Salvar</button>
                            <button onclick="cancelarEdicao()" class="bg-gray-400 text-white py-2 px-4 rounded font-bold text-xs uppercase">Cancelar</button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>



    <!-- 3. Lógica Javascript -->
    <script>
        let rotaAtual = "{{ Route::currentRouteName() }}";

        function toggleAjuda() {
            const painel = document.getElementById('painel-ajuda');
            if(painel.classList.contains('hidden')) {
                carregarConteudo();
                painel.classList.remove('hidden');
            } else {
                painel.classList.add('hidden');
                cancelarEdicao();
            }
        }

        function carregarConteudo() {
            fetch("{{ route('ajuda.get') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ rota: rotaAtual })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('ajuda-titulo').innerText = data.titulo;
                document.getElementById('ajuda-corpo').innerHTML = data.conteudo;
                
                // Preenche o editor caso o professor queira editar
                if(document.getElementById('edit-titulo')) {
                    document.getElementById('edit-titulo').value = data.titulo;
                    document.getElementById('edit-conteudo').value = data.conteudo;
                }
            });
        }

        function habilitarEdicao() {
            document.getElementById('btn-editar-ajuda').classList.add('hidden');
            document.getElementById('ajuda-corpo').classList.add('hidden');
            document.getElementById('editor-ajuda').classList.remove('hidden');
        }

        function cancelarEdicao() {
            if(document.getElementById('btn-editar-ajuda')) {
                document.getElementById('btn-editar-ajuda').classList.remove('hidden');
                document.getElementById('ajuda-corpo').classList.remove('hidden');
                document.getElementById('editor-ajuda').classList.add('hidden');
            }
        }

        function salvarAjuda() {
            const titulo = document.getElementById('edit-titulo').value;
            const conteudo = document.getElementById('edit-conteudo').value;

            fetch("{{ route('ajuda.salvar') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    rota: rotaAtual,
                    titulo: titulo,
                    conteudo: conteudo
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    // --- TROCAMOS O ALERT POR ISSO ---
                    const notify = document.getElementById('notificacao-sucesso');
                    notify.classList.add('mostrar'); // Faz a caixa descer
                    
                    setTimeout(() => {
                        notify.classList.remove('mostrar'); // Faz a caixa subir após 3 segundos
                    }, 3000);

                    carregarConteudo();
                    cancelarEdicao();
                }
            });
        }
    </script>

    <!-- ======================================================= -->
    <!-- MODAL "SOBRE O SISTEMA" (AJUSTADO PARA ACESSIBILIDADE) -->
    <!-- ======================================================= -->
    
    {{-- Variável de controle para o layout mestre --}}
    @php 
        $isPcdGlobal = Auth::user()?->acessibilidade_visual; 
        
        // Ícone de Check para a lista (SVG Amarelo ou Emoji)
        // Isso garante que no modo PCD o check seja amarelo puro
        $iconCheck = $isPcdGlobal 
            ? '<svg class="w-3 h-3 inline-block mr-1 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="#ffff00" stroke-width="3" style="filter:none!important"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>' 
            : '✅';
    @endphp
    
    <!-- Botão Discreto (Canto Inferior Esquerdo) -->
    <div class="fixed bottom-6 left-6 z-50">
        <button onclick="document.getElementById('modal-sobre').classList.remove('hidden')" 
                class="transition-all duration-300 flex items-center gap-2 px-3 py-1 rounded-full border no-print
                {{ $isPcdGlobal ? 'bg-black border-yellow-400' : 'bg-white/50 backdrop-blur-sm border-gray-200 shadow-sm text-gray-400 hover:text-indigo-600' }}"
                title="Sobre o Software">
            
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" 
                 stroke="{{ $isPcdGlobal ? '#ffff00' : 'currentColor' }}" 
                 style="{{ $isPcdGlobal ? 'filter: none !important; stroke: #ffff00 !important;' : '' }}">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>

            <span class="text-[10px] font-bold uppercase tracking-widest {{ $isPcdGlobal ? 'text-yellow-400' : '' }}">v1.5</span>
        </button>
    </div>

    <!-- Estrutura do Modal -->
    <div id="modal-sobre" class="fixed inset-0 z-[100] flex items-center justify-center hidden">
        <!-- Fundo Escuro -->
        <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-md"></div>
        
        <!-- Caixa do Modal -->
        <div class="relative w-full max-w-lg rounded-3xl overflow-hidden transform transition-all {{ $isPcdGlobal ? 'bg-black border-4 border-yellow-400' : 'bg-white shadow-2xl border-2 border-indigo-100' }}">
            
            {{-- Faixa de Topo --}}
            <div class="{{ $isPcdGlobal ? 'bg-black h-2' : 'bg-indigo-600 h-2 w-full' }}"></div>

            <div class="p-8">
                {{-- Cabeçalho do Modal --}}
                <div class="flex justify-between items-start mb-6">
                    <div class="flex items-center gap-4">
                       <div class="p-3 rounded-2xl {{ $isPcdGlobal ? 'bg-black border-2 border-yellow-400' : 'bg-indigo-100' }}">
                            @if($isPcdGlobal)
                                <!-- Ícone de Fábrica (SVG Amarelo Neon) -->
                                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="2" style="filter:none!important">
                                    <path d="M2 20V9l9-2v13M11 20V5l9-2v17" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M11 13h4M11 17h4M5 13h3M5 17h3" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            @else
                                <span class="text-3xl">🏭</span>
                            @endif
                        </div> 
                        <div>
                            <h3 class="text-2xl font-black leading-none {{ $isPcdGlobal ? 'text-yellow-400' : 'text-gray-800' }}">Fábrica-Digital</h3>
                            <p class="font-bold text-xs uppercase tracking-widest mt-1 {{ $isPcdGlobal ? 'text-yellow-400' : 'text-indigo-600' }}">Simulator Educacional</p>
                        </div>
                    </div>
                    <button onclick="document.getElementById('modal-sobre').classList.add('hidden')" class="text-2xl font-bold {{ $isPcdGlobal ? 'text-yellow-400' : 'text-gray-400 hover:text-gray-600' }}">&times;</button>
                </div>

                <div class="space-y-6">
                    {{-- Informações de Versão --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-3 rounded-xl {{ $isPcdGlobal ? 'bg-black border-2 border-yellow-400' : 'bg-gray-50 border border-gray-100' }}">
                            <p class="text-[10px] font-black uppercase {{ $isPcdGlobal ? 'text-yellow-400' : 'text-gray-400' }}">Versão Atual</p>
                            <p class="text-lg font-bold {{ $isPcdGlobal ? 'text-yellow-400' : 'text-gray-700' }}">v1.5</p>
                        </div>
                        <div class="p-3 rounded-xl {{ $isPcdGlobal ? 'bg-black border-2 border-yellow-400' : 'bg-gray-50 border border-gray-100' }}">
                            <p class="text-[10px] font-black uppercase {{ $isPcdGlobal ? 'text-yellow-400' : 'text-gray-400' }}">Última Versão</p>
                            <p class="text-lg font-bold {{ $isPcdGlobal ? 'text-yellow-400' : 'text-gray-700' }}">{{ date('d/m/Y') }}</p>
                        </div>
                    </div>

                    {{-- Notas de Lançamento (Timeline) --}}
                    <div class="mt-6 space-y-4">
                        <h4 class="font-black text-xs uppercase tracking-widest pb-1 border-b-2 {{ $isPcdGlobal ? 'text-yellow-400 border-yellow-400' : 'text-indigo-600 border-indigo-100' }}">
                            Linha do Tempo de Desenvolvimento
                        </h4>

                        <!-- VERSÃO 1.5 (ATUAL) -->
                        <div class="group relative p-3 rounded-lg transition-all {{ $isPcdGlobal ? 'bg-black border-4 border-yellow-400' : 'bg-indigo-50 border-2 border-indigo-500 hover:shadow-[5px_5px_0px_0px_rgba(79,70,229,1)]' }}">
                            <div class="flex justify-between items-center mb-2">
                                <span class="px-2 py-0.5 rounded font-black text-[10px] {{ $isPcdGlobal ? 'bg-yellow-400 text-black' : 'bg-indigo-600 text-white' }}">v1.5 (Stable)</span>
                                <span class="text-[10px] font-bold {{ $isPcdGlobal ? 'text-yellow-400' : 'text-indigo-400' }}">05/01/2026</span>
                            </div>
                            <p class="text-xs font-black uppercase {{ $isPcdGlobal ? 'text-yellow-400' : 'text-indigo-900' }}">Reforma Industrial e de Segurança</p>
                            
                            {{-- LISTA V1.5 COMPLETA --}}
                            <ul class="mt-2 space-y-1 text-[10px] font-medium hidden group-hover:block {{ $isPcdGlobal ? 'text-yellow-400' : 'text-indigo-700' }}">
                            <li>{!! $iconCheck !!} **Visual:** Modo Alto Contraste (Preto e Amarelo Neon).</li>
                            <li>{!! $iconCheck !!} **Inclusão 4.0:** Motor de Voz e Tradutor de Libras.</li>
                            <li>{!! $iconCheck !!} **Acessibilidade:** Pictogramas e Navegação Assistida.</li>

                                <li>{!! $iconCheck !!} **Torre de Controle:** Monitor Andon com rastreabilidade cronológica.</li>
                                <li>{!! $iconCheck !!} **Engenharia do Caos:** Sabotagem programada de Carga e Refugo.</li>
                                <li>{!! $iconCheck !!} **BI Executivo:** Dashboard Dark Mode com indicadores (OEE, OTIF).</li>
                                <li>{!! $iconCheck !!} **Regras de Mercado:** Limitador de cota de vendas diárias por aluno.</li>
                                <li>{!! $iconCheck !!} **UX Blindada:** Modais de confirmação com trava física de duplo clique.</li>
                                <li>{!! $iconCheck !!} **Manual Vivo:** Sistema de documentação técnica editável por setor.</li>
                                <li>{!! $iconCheck !!} **Consolidação Logística:** Validação física de lotes produzidos.</li>
                                <li>{!! $iconCheck !!} **Design System:** Interface Industrial 3D Neo-Brutalism.</li>
                                <li>{!! $iconCheck !!} **Multi-Professor:** Isolamento total por docente.</li>
                                <li>{!! $iconCheck !!} **Segurança:** Senha Mestra para ações destrutivas.</li>
                                <li>{!! $iconCheck !!} **Integridade:** Soft Deletes (Lixeira Segura).</li>
                                <li>{!! $iconCheck !!} **Perfil:** Dados corporativos integrados.</li>
                            </ul>
                            <p class="text-[8px] font-bold mt-1 group-hover:hidden italic {{ $isPcdGlobal ? 'text-yellow-400' : 'text-indigo-300' }}">Passe o mouse para detalhes...</p>
                        </div>

                        <!-- VERSÃO 1.0 (LEGACY) -->
                        <div class="group relative p-3 rounded-lg transition-all {{ $isPcdGlobal ? 'bg-black border-2 border-yellow-400' : 'bg-gray-50 border-2 border-gray-300 grayscale hover:grayscale-0' }}">
                            <div class="flex justify-between items-center mb-2">
                                <span class="px-2 py-0.5 rounded font-black text-[10px] {{ $isPcdGlobal ? 'bg-yellow-400 text-black' : 'bg-gray-500 text-white' }}">v1.0 (Legacy)</span>
                                <span class="text-[10px] font-bold {{ $isPcdGlobal ? 'text-yellow-400' : 'text-gray-400' }}">31/12/2025</span>
                            </div>
                            <p class="text-xs font-black uppercase {{ $isPcdGlobal ? 'text-yellow-400' : 'text-gray-600' }}">Lançamento do Ecossistema</p>


                            {{-- LISTA V1.0 (AGORA INCLUÍDA CORRETAMENTE) --}}
                            <ul class="mt-2 space-y-1 text-[10px] font-medium hidden group-hover:block {{ $isPcdGlobal ? 'text-yellow-400' : 'text-gray-500' }}">
                                <li>{!! $iconCheck !!} **Sincronização:** Conexão real entre setores.</li>
                                <li>{!! $iconCheck !!} **Interface:** Experiência visual de comando industrial.</li>
                                <li>{!! $iconCheck !!} **Painel do Caos:** Simulação de quebra e atrasos.</li>
                                <li>{!! $iconCheck !!} **Multi-Tenant:** Isolamento total de dados entre turmas.</li>
                                <li>{!! $iconCheck !!} **Multi-Professor:** Gestão isolada para multiplos docentes.</li>
                                <li>{!! $iconCheck !!} **Segurança Master:** Trava de segurança para exclusões.</li>
                                <li>{!! $iconCheck !!} **Simulação:** Motor de tempo simulado.</li>
                                <li>{!! $iconCheck !!} **BI Básico:** Painel de monitoramento inicial.</li>
                            </ul>
                            <p class="text-[8px] font-bold mt-1 group-hover:hidden italic {{ $isPcdGlobal ? 'text-yellow-400' : 'text-gray-400' }}">Passe o mouse para detalhes...</p>
                        </div>
                    </div>

                    {{-- Créditos --}}
                    <div class="pt-6 border-t {{ $isPcdGlobal ? 'border-yellow-400' : 'border-gray-100' }}">
                        <p class="text-[10px] font-black uppercase text-center mb-2 {{ $isPcdGlobal ? 'text-yellow-400' : 'text-gray-400' }}">Desenvolvimento & Mentoria</p>
                        <p class="text-sm font-bold text-center {{ $isPcdGlobal ? 'text-yellow-400' : 'text-gray-800' }}">Professor Alexandre Felix de Araujo</p>
                        <p class="text-[11px] text-center mt-1 {{ $isPcdGlobal ? 'text-yellow-400' : 'text-gray-500' }}">Sistema desenvolvido para capacitação técnica industrial.</p>
                    </div>
                </div>

                {{-- Rodapé do Modal --}}
                <div class="mt-8 flex justify-center">
                    <button onclick="document.getElementById('modal-sobre').classList.add('hidden')" 
                            class="px-8 py-2 rounded-xl font-bold text-sm transition shadow-lg {{ $isPcdGlobal ? 'bg-black text-yellow-400 border-2 border-yellow-400' : 'bg-gray-800 text-white hover:bg-black' }}">
                        Fechar
                    </button>
                </div>
            </div>
            
            <div class="p-3 text-center {{ $isPcdGlobal ? 'bg-black border-t-2 border-yellow-400' : 'bg-gray-50' }}">
                <p class="text-[9px] font-bold uppercase tracking-widest {{ $isPcdGlobal ? 'text-yellow-400' : 'text-gray-400' }}">AFELIX © 2026 - Todos os direitos reservados</p>
            </div>
        </div>
    </div>







    <!-- Notificação Customizada (Estilo Fábrica Digital) -->
    <div id="notificacao-sucesso" class="fixed top-0 left-1/2 -translate-x-1/2 z-[200] w-full max-w-sm pointer-events-none">
        <div class="bg-green-500 border-4 border-black p-4 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] flex items-center gap-4">
            <div class="bg-white border-2 border-black w-10 h-10 flex items-center justify-center text-2xl">
                ✅
            </div>
            <div>
                <h4 class="font-black text-white uppercase text-sm leading-none">Sucesso!</h4>
                <p class="text-green-950 font-bold text-xs mt-1">Manual atualizado no banco de dados.</p>
            </div>
        </div>
    </div>

<!-- ======================================================= -->
<!-- MOTOR DE LEITURA TOTAL + CONTROLE DE SOM (PCD READY)    -->
<!-- ======================================================= -->
@if(Auth::user()?->acessibilidade_audio)
    <!-- 1. Botão Flutuante de Controle de Som (Aparece acima do botão de ajuda) -->
    <div class="fixed bottom-24 right-6 z-50 no-print">
        <button id="btn-toggle-audio" onclick="toggleMuteAudio()" 
                class="bg-white border-4 border-black w-14 h-14 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] active:shadow-none active:translate-y-1 transition-all flex items-center justify-center text-2xl hover:bg-yellow-50"
                style="{{ Auth::user()->acessibilidade_visual ? 'background-color: #000 !important; border-color: #ffff00 !important; shadow: none !important;' : '' }}"
                title="Ativar/Desativar Som da Leitura">
            
            <div id="container-icon-audio">
                @if(Auth::user()->acessibilidade_visual)
                    {{-- SVG Amarelo Neon para o Modo PCD (Som Ativo) --}}
                    <svg id="svg-audio-on" class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="2.5" style="display: block;">
                        <path d="M11 5L6 9H2v6h4l5 4V5zM19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path>
                    </svg>
                    {{-- SVG Amarelo Neon para o Modo PCD (Mudo) --}}
                    <svg id="svg-audio-off" class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="#ffff00" stroke-width="2.5" style="display: none;">
                        <path d="M11 5L6 9H2v6h4l5 4V5zM23 9l-6 6m0-6l6 6"></path>
                    </svg>
                @else
                    {{-- Emoji para o Modo Normal --}}
                    <span id="emoji-audio">🔊</span>
                @endif
            </div>
        </button>
    </div>

    <script>
        const synth = window.speechSynthesis;
        let speech = new SpeechSynthesisUtterance(); 
        speech.lang = 'pt-BR';
        speech.rate = 1.2;
        
        let audioMudo = false;
        let ultimoElementoLido = null;
        const isPcdVisual = {{ Auth::user()->acessibilidade_visual ? 'true' : 'false' }};

        // Função para Alternar Mudo
        function toggleMuteAudio() {
            audioMudo = !audioMudo;
            synth.cancel();

            if (isPcdVisual) {
                document.getElementById('svg-audio-on').style.display = audioMudo ? 'none' : 'block';
                document.getElementById('svg-audio-off').style.display = audioMudo ? 'block' : 'none';
            } else {
                document.getElementById('emoji-audio').innerText = audioMudo ? '🔇' : '🔊';
            }
        }

        // MOTOR DE VARREDURA UNIVERSAL (Lê qualquer texto onde o mouse passar)
        document.body.addEventListener('mouseover', (event) => {
            if (audioMudo) return;

            const el = event.target;
            // Filtro de tags para evitar ler códigos ou containers vazios
            const tagsPermitidas = ['H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'P', 'SPAN', 'BUTTON', 'A', 'TD', 'TH', 'LABEL', 'LI'];
            
            if (tagsPermitidas.includes(el.tagName) && el.innerText.trim() !== "" && el !== ultimoElementoLido) {
                synth.cancel();
                
                if(ultimoElementoLido) ultimoElementoLido.style.outline = "none";
                
                speech.text = el.innerText.trim();
                synth.speak(speech);

                // Feedback visual: Moldura no que está sendo lido
                el.style.outline = isPcdVisual ? "4px solid #FFFF00" : "3px solid #6366f1";
                ultimoElementoLido = el;
            }
        });

        // Limpa a moldura ao tirar o mouse
        document.body.addEventListener('mouseout', (event) => {
            if (event.target === ultimoElementoLido) {
                synth.cancel();
                event.target.style.outline = "none";
                ultimoElementoLido = null;
            }
        });

        // PARAR TUDO (CLIQUE EM ÁREA VAZIA OU TECLA ESC)
        const pararTudo = () => {
            synth.cancel();
            if(ultimoElementoLido) {
                ultimoElementoLido.style.outline = "none";
                ultimoElementoLido = null;
            }
        };

        document.addEventListener('click', pararTudo);
        document.addEventListener('keydown', (e) => {
            if (e.key === "Escape") pararTudo();
        });
    </script>
@endif



<!-- SISTEMA VLIBRAS -->
@if(Auth::user()?->acessibilidade_libras)
    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script>
        new window.VLibras.Widget('https://vlibras.gov.br/app');
    </script>
@endif

    </body>
</html>   