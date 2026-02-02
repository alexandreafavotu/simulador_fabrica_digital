<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
            <span class="text-3xl">👋</span> {{ __('Portaria / Recepção') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-yellow-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            {{-- CARD DE BOAS-VINDAS INDUSTRIAL --}}
            <div class="bg-white border-4 border-black rounded-xl shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                
                <div class="bg-black text-white p-6 border-b-4 border-black text-center">
                    <h3 class="text-2xl font-black uppercase tracking-widest">Acesso Autorizado</h3>
                    <p class="text-gray-400 text-sm font-bold mt-1">Identificação: {{ Auth::user()->name }}</p>
                </div>

                <div class="p-10 text-center">
                    
                    <div class="mb-6 inline-block bg-yellow-100 p-6 rounded-full border-4 border-black shadow-sm">
                        <span class="text-6xl">⏳</span>
                    </div>

                    <h1 class="text-3xl font-black text-gray-800 mb-4 uppercase">Bem-vindo à Fábrica Digital!</h1>
                    
                    <div class="bg-blue-50 border-2 border-blue-200 p-4 rounded-lg text-blue-800 font-bold max-w-lg mx-auto mb-8">
                        <p>Seu cadastro foi realizado com sucesso, mas você ainda não possui um posto de trabalho definido.</p>
                    </div>

                    <p class="text-gray-600 font-medium mb-8">
                        Aguarde enquanto o <strong>Game Master (Professor)</strong> escala você em uma Turma e define seu Setor (Vendas, Produção, etc).
                    </p>

                    <a href="{{ route('dashboard') }}" class="inline-block bg-indigo-600 text-white px-8 py-3 rounded border-2 border-black font-black uppercase shadow-[4px_4px_0px_0px_black] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none transition">
                        🔄 Verificar Novamente
                    </a>
                    
                </div>
                
                <div class="bg-gray-100 p-4 text-center border-t-4 border-black">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Sistema de Gestão Industrial Integrada v1.5</p>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>