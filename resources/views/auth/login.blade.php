<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login | Fábrica-Digital</title>
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

            <!-- CARD DE LOGIN -->
            <div class="max-w-md w-full bg-white border-4 border-black shadow-[8px_8px_0px_0px_rgba(255,255,255,0.2)] rounded-2xl overflow-hidden relative z-10">
                
                <!-- Cabeçalho do Card -->
                <div class="bg-gray-100 p-6 border-b-4 border-black text-center">
                    <a href="/">
                        <img src="{{ asset('logo.png') }}" onerror="this.style.display='none'" class="h-12 w-auto mx-auto mb-2 hover:scale-105 transition">
                    </a>
                    <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tighter">
                        Acesso ao <span class="text-red-600">Sistema</span>
                    </h2>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mt-1">Identifique-se para continuar</p>
                </div>

                <!-- Formulário -->
                <div class="p-8">
                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block font-black text-xs text-gray-700 uppercase mb-1">E-mail / Login</label>
                            <input id="email" class="block w-full rounded-lg input-industrial p-3 font-bold bg-gray-50" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="mt-6">
                            <label for="password" class="block font-black text-xs text-gray-700 uppercase mb-1">Senha de Acesso</label>
                            <input id="password" class="block w-full rounded-lg input-industrial p-3 font-bold bg-gray-50"
                                            type="password"
                                            name="password"
                                            required autocomplete="current-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Remember Me -->
                        <div class="block mt-4 flex justify-between items-center">
                            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                                <input id="remember_me" type="checkbox" class="rounded border-2 border-black text-red-600 shadow-sm focus:ring-red-500 w-5 h-5" name="remember">
                                <span class="ms-2 text-sm font-bold text-gray-600">Lembrar de mim</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a class="underline text-xs text-gray-500 hover:text-red-600 font-bold uppercase tracking-wide" href="{{ route('password.request') }}">
                                    Esqueceu a senha?
                                </a>
                            @endif
                        </div>

                        <div class="mt-8">
                            <button class="w-full btn-3d bg-indigo-600 text-white border-indigo-900 text-lg py-3 rounded-xl font-black hover:bg-indigo-500 shadow-xl uppercase tracking-wider flex justify-center items-center gap-2">
                                <span>🔐</span> Entrar no Painel
                            </button>
                        </div>
                        
                        <div class="mt-6 text-center">
                            <p class="text-[10px] font-bold text-gray-400 uppercase">
                                Acesso restrito a docentes e alunos
                            </p>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </body>
</html>