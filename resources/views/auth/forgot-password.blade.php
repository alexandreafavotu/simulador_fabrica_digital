<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Recuperar Senha | Fábrica-Digital</title>
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
            /* Botão 3D */
            .btn-3d {
                transition: all 0.1s;
                border-bottom-width: 4px;
                transform: translateY(0);
            }
            .btn-3d:active { 
                transform: translateY(2px); 
                border-bottom-width: 2px; 
            }
            /* Input Industrial */
            .input-industrial {
                border: 3px solid #000;
                box-shadow: 3px 3px 0px 0px rgba(0,0,0,0.1);
                transition: all 0.2s;
            }
            .input-industrial:focus {
                box-shadow: 4px 4px 0px 0px rgba(0,0,0,1);
                border-color: #E30613;
                outline: none;
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

        <div class="flex-grow flex items-center justify-center hero-pattern relative overflow-hidden p-4">
            
            <!-- Luzes de Fundo -->
            <div class="absolute top-0 left-0 w-72 h-72 bg-red-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute bottom-0 right-0 w-72 h-72 bg-blue-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>

            <!-- CARD DE RECUPERAÇÃO -->
            <div class="max-w-md w-full bg-white border-4 border-black shadow-[8px_8px_0px_0px_rgba(255,255,255,0.2)] rounded-2xl overflow-hidden relative z-10">
                
                <!-- Cabeçalho do Card -->
                <div class="bg-gray-100 p-6 border-b-4 border-black text-center">
                    <a href="/">
                        <img src="{{ asset('logo.png') }}" onerror="this.style.display='none'" class="h-10 w-auto mx-auto mb-2 hover:scale-105 transition">
                    </a>
                    <h2 class="text-xl font-black text-gray-800 uppercase tracking-tighter">
                        Recuperar <span class="text-red-600">Acesso</span>
                    </h2>
                </div>

                <div class="mb-4 text-sm font-bold text-black leading-tight border-2 border-black p-4 bg-yellow-300 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
    {{ __('ATENÇÃO ALUNO:') }}
</div>

<div class="mb-6 text-sm text-gray-800 font-medium">
    {{ __('Para garantir a integridade da simulação, o reset de senhas é gerenciado exclusivamente pelo Professor (Game Master).') }}
    <br><br>
    {{ __('Caso tenha perdido seu acesso, dirija-se ao monitor e solicite o reset manual da sua conta.') }}
</div>

<!-- Botão de Voltar com Estilo Neo-Brutalist -->
<div class="flex items-center justify-center mt-4">
    <a href="{{ route('login') }}" 
       class="w-full text-center py-3 bg-gray-800 text-white font-black uppercase tracking-widest border-4 border-black shadow-[4px_4px_0px_0px_black] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-none transition-all">
        ← VOLTAR PARA O LOGIN
    </a>
</div>
            </div>

        </div>
    </body>
</html>