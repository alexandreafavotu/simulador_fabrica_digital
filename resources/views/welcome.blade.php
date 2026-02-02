<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Fábrica-Digital | SENAI</title>
        <!-- Fonts & Scripts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600,900&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            .bg-senai-red { background-color: #E30613; }
            .hero-pattern {
                background-color: #111827;
                background-image: radial-gradient(#374151 1px, transparent 1px);
                background-size: 24px 24px;
            }
            .btn-3d {
                transition: all 0.1s;
                border-bottom-width: 6px;
                transform: translateY(0);
            }
            .btn-3d:active { 
                transform: translateY(3px); 
                border-bottom-width: 3px; 
            }
            /* Animação suave */
            @keyframes blob {
                0% { transform: translate(0px, 0px) scale(1); }
                33% { transform: translate(30px, -50px) scale(1.1); }
                66% { transform: translate(-20px, 20px) scale(0.9); }
                100% { transform: translate(0px, 0px) scale(1); }
            }
            .animate-blob { animation: blob 7s infinite; }
            .animation-delay-2000 { animation-delay: 2s; }
        </style>
    </head>
    <body class="antialiased text-gray-900 bg-gray-100 min-h-screen flex flex-col">

        <!-- Barra Superior -->
        <div class="bg-senai-red h-2 w-full shadow-lg relative z-50"></div>

        <div class="flex-grow flex items-center justify-center hero-pattern relative overflow-hidden">
            
            <!-- Luzes de Fundo -->
            <div class="absolute top-0 left-0 w-72 h-72 bg-red-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute bottom-0 right-0 w-72 h-72 bg-blue-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>

            <div class="max-w-5xl w-full px-6 text-center relative z-10">
                
                <!-- Logo e Título -->
                <div class="mb-8 flex flex-col items-center justify-center">
                    <img src="{{ asset('logo.png') }}" onerror="this.style.display='none'" class="h-28 w-auto hover:scale-105 transition duration-300 drop-shadow-2xl mb-4">
                    
                    <h1 class="text-6xl md:text-6xl font-black text-white tracking-tighter uppercase leading-none drop-shadow-lg">
                        Fábrica<span class="text-red-600">Digital</span>
                    </h1>
                    <p class="text-xl text-gray-400 mt-2 font-light tracking-wide mb-6">
                        Simulador de Gestão Industrial Integrada
                    </p>

                    <!-- HIERARQUIA DE SISTEMAS (O que você pediu) -->
                    <div class="flex flex-col items-center gap-2 w-full max-w-2xl mx-auto">
                        
                        <!-- Linha 1: O Sistema Pai (ERP) -->
                        <div class="w-full flex justify-center">
                            <span 
                                class="px-10 py-2 bg-gray-900 text-white text-lg font-black tracking-widest rounded border-2 border-gray-600 shadow-[0_0_15px_rgba(255,255,255,0.1)] transition-all duration-300 hover:shadow-[0_0_35px_rgba(255,255,255,0.9)] hover:border-white hover:scale-110 cursor-default relative z-20"
                                title="Enterprise Resource Planning - Planejamento dos Recursos da Empresa">
                                ERP
                            </span>
                        </div>

                        <!-- Conector Visual (Linha pequena vertical) -->
                        <div class="h-4 w-0.5 bg-gray-600"></div>

                        <!-- Linha 2: Os Módulos Integrados -->
                        <div class="flex flex-wrap justify-center gap-3">
                            <span class="px-4 py-1.5 bg-gray-800 text-gray-300 text-xs font-bold rounded border border-gray-600 shadow-sm hover:border-gray-400 hover:text-white transition cursor-default" title="Planejamento e Controle da Produção / Materiais">
                                PCP / MRP
                            </span>
                            <span class="px-4 py-1.5 bg-gray-800 text-gray-300 text-xs font-bold rounded border border-gray-600 shadow-sm hover:border-gray-400 hover:text-white transition cursor-default" title="Manufacturing Execution System (Chão de Fábrica)">
                                MES
                            </span>
                            <span class="px-4 py-1.5 bg-gray-800 text-gray-300 text-xs font-bold rounded border border-gray-600 shadow-sm hover:border-gray-400 hover:text-white transition cursor-default" title="Warehouse Management System (Estoque)">
                                WMS
                            </span>
                            <span class="px-4 py-1.5 bg-gray-800 text-gray-300 text-xs font-bold rounded border border-gray-600 shadow-sm hover:border-gray-400 hover:text-white transition cursor-default" title="Gestão de Clientes e Fornecedores">
                                CRM / SRM
                            </span>
                            <span class="px-4 py-1.5 bg-gray-800 text-gray-300 text-xs font-bold rounded border border-gray-600 shadow-sm hover:border-gray-400 hover:text-white transition cursor-default" title="Business Intelligence">
                                BI
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="flex flex-col sm:flex-row justify-center gap-6 mt-12">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-3d bg-yellow-400 text-black border-yellow-600 text-xl px-12 py-4 rounded-xl font-black hover:bg-yellow-300 shadow-xl uppercase tracking-wider flex items-center gap-2">
                                🚀 Acessar Painel
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-3d bg-red-600 text-white border-red-800 text-xl px-12 py-4 rounded-xl font-black hover:bg-red-500 shadow-xl uppercase tracking-wider flex items-center gap-2">
                                🔐 Entrar no Simulador
                            </a>
                        @endauth
                    @endif
                </div>

                <!-- Rodapé -->
                <div class="mt-20 text-gray-600 text-xs">
                    <p>Desenvolvido por: <strong class="text-gray-400">Professor Alexandre Felix de Araujo</strong></p>
                    <p class="mt-2 opacity-200">AFELIX © {{ date('Y') }} TODOS OS DIREITOS RESERVADOS</p>
                </div>
            </div>
        </div>
    </body>
</html>