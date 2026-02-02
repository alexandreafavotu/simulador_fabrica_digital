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
            
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-100">
        
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
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.198-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" /></svg>
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
            <div id="ajuda-corpo" class="flex-1 overflow-y-auto p-6 prose prose-slate" style="white-space: pre-wrap; color: #1a202c !important;">
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
    <!-- MODAL "SOBRE O SISTEMA" (CRÉDITOS E VERSÃO)            -->
    <!-- ======================================================= -->
    
    <!-- Botão Discreto (Canto Inferior Esquerdo) -->
    <div class="fixed bottom-6 left-6 z-50">
        <button onclick="document.getElementById('modal-sobre').classList.remove('hidden')" 
                class="text-gray-400 hover:text-indigo-600 transition-all duration-300 flex items-center gap-2 bg-white/50 backdrop-blur-sm px-3 py-1 rounded-full border border-gray-200 shadow-sm no-print"
                title="Sobre o Software">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-[10px] font-bold uppercase tracking-widest">v1.5</span>
        </button>
    </div>

    <!-- Estrutura do Modal -->
    <div id="modal-sobre" class="fixed inset-0 z-[100] flex items-center justify-center hidden">
        <!-- Fundo Escuro -->
        <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-md"></div>
        
        <!-- Caixa do Modal (Estética 3D) -->
        <div class="relative bg-white w-full max-w-lg rounded-3xl shadow-2xl border-2 border-indigo-100 overflow-hidden transform transition-all">
            
            {{-- Faixa de Topo --}}
            <div class="bg-indigo-600 h-2 w-full"></div>

            <div class="p-8">
                <div class="flex justify-between items-start mb-6">
                    <div class="flex items-center gap-4">
                        <div class="bg-indigo-100 p-3 rounded-2xl">
                            <span class="text-3xl">🚀</span>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-gray-800 leading-none">Fábrica-Digital</h3>
                            <p class="text-indigo-600 font-bold text-xs uppercase tracking-widest mt-1">Simulator Educacional</p>
                        </div>
                    </div>
                    <button onclick="document.getElementById('modal-sobre').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
                </div>

                <div class="space-y-6">
                    {{-- Informações de Versão --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <p class="text-[10px] text-gray-400 font-black uppercase">Versão Atual</p>
                            <p class="text-lg font-bold text-gray-700">v1.5</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <p class="text-[10px] text-gray-400 font-black uppercase">Última VERSÃO</p>
                            <p class="text-lg font-bold text-gray-700">{{ date('d/m/Y') }}</p>
                        </div>
                    </div>

                    {{-- Notas de Lançamento --}}
                    <!-- SEÇÃO DE EVOLUÇÃO DO SISTEMA -->
<div class="mt-6 space-y-4">
    <h4 class="font-black text-xs text-indigo-600 uppercase tracking-widest border-b-2 border-indigo-100 pb-1">
        🚀 Linha do Tempo de Desenvolvimento
    </h4>

    <!-- VERSÃO 1.5 (ATUAL) -->
    <div class="group relative bg-indigo-50 border-2 border-indigo-500 p-3 rounded-lg transition-all duration-300 hover:shadow-[5px_5px_0px_0px_rgba(79,70,229,1)]">
        <div class="flex justify-between items-center mb-2">
            <span class="bg-indigo-600 text-white px-2 py-0.5 rounded font-black text-[10px]">v1.5 (Stable)</span>
            <span class="text-[10px] font-bold text-indigo-400">05/01/2026</span>
        </div>
        <p class="text-xs font-black text-indigo-900 uppercase">Reforma Industrial e de Segurança</p>
        
        {{-- Detalhes que aparecem ao passar o mouse --}}
        <ul class="mt-2 space-y-1 text-[10px] font-medium text-indigo-700 hidden group-hover:block animate-fade-in">
            <li>✅ **Torre de Controle:** Monitor Andon com rastreabilidade cronológica (WMS/Prod).</li>
            <li>✅ **Engenharia do Caos:** Sabotagem de Carga (Recusa forçada) e Refugo Programado.</li>
            <li>✅ **BI Executivo:** Dashboard Dark Mode com indicadores de OEE, OTIF e Custo da Ineficiência.</li>
            <li>✅ **Regras de Mercado:** Limitador de cota de vendas diárias por aluno.</li>
            <li>✅ **UX Blindada:** Modais de confirmação com trava física de duplo clique.</li>
            <li>✅ **Manual Vivo:** Sistema de documentação técnica editável por setor.</li>
            <li>✅ **Consolidação Logística:** Validação fisíca de lotes produzidos.</li>
            <li>✅ **Consolidação Logística:** Validação fisíca de lotes produzidos.</li>
            <li>✅ **Design System:** Interface Industrial 3D (Neo-Brutalism).</li>
            <li>✅ **Multi-Professor:** Isolamento total de turmas por docente.</li>
            <li>✅ **Segurança:** Senha Mestra para ações destrutivas.</li>
            <li>✅ **Integridade:** Soft Deletes (Lixeira Segura) em todo o banco.</li>
            <li>✅ **Perfil:** Dados corporativos (CNPJ/Endereço) integrados.</li>
        </ul>
        {{-- Dica visual --}}
        <p class="text-[8px] text-indigo-300 font-bold mt-1 group-hover:hidden italic">Passe o mouse para detalhes...</p>
    </div>

    <!-- VERSÃO 1.0 (LANÇAMENTO) -->
    <div class="group relative bg-gray-50 border-2 border-gray-300 p-3 rounded-lg transition-all duration-300 hover:shadow-[5px_5px_0px_0px_rgba(0,0,0,0.1)] grayscale hover:grayscale-0">
        <div class="flex justify-between items-center mb-2">
            <span class="bg-gray-500 text-white px-2 py-0.5 rounded font-black text-[10px]">v1.0 (Legacy)</span>
            <span class="text-[10px] font-bold text-gray-400">31/12/2025</span>
        </div>
        <p class="text-xs font-black text-gray-600 uppercase">Lançamento do Ecossistema</p>

        {{-- Detalhes --}}
        <ul class="mt-2 space-y-1 text-[10px] font-medium text-gray-500 hidden group-hover:block animate-fade-in">
            <li>✅ **Sincronização:** Conexão real entre setores.</li>
            <li>✅ **Interface:** Experiência visual de comando industrial.</li>
            <li>✅ **Painel do Caos:** Simulação de quebra e atrasos.</li>
            <li>✅ **Multi-Tenant:** Isolamento total de dados entre turmas.</li>
            <li>✅ **Multi-Professor:** Gestão isolada para multiplos docentes.</li>
            <li>✅ **Segurança Master:** trava de segurança para exclusões.</li>
            <li>✅ **Simulação:** Motor de tempo simulado e Game Loop.</li>
            <li>✅ **BI Básico:** Primeiro painel de monitoramento do professor.</li>
        </ul>
    </div>
</div>

                    {{-- Créditos --}}
                    <div class="pt-6 border-t border-gray-100">
                        <p class="text-[10px] text-gray-400 font-black uppercase text-center mb-2">Desenvolvimento & Mentoria</p>
                        <p class="text-sm font-bold text-gray-800 text-center">Professor Alexandre Felix de Araujo</p>
                        <p class="text-[11px] text-gray-500 text-center mt-1">Sistema desenvolvido para capacitação técnica industrial.</p>
                    </div>
                </div>

                {{-- Rodapé do Modal --}}
                <div class="mt-8 flex justify-center">
                    <button onclick="document.getElementById('modal-sobre').classList.add('hidden')" 
                            class="bg-gray-800 text-white px-8 py-2 rounded-xl font-bold text-sm hover:bg-black transition shadow-lg">
                        Fechar
                    </button>
                </div>
            </div>
            
            <div class="bg-gray-50 p-3 text-center">
                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">AFELIX © 2026 - Todos os direitos reservados</p>
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



    </body>
</html>