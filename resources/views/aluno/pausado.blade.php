<x-app-layout>
    <div class="min-h-screen flex flex-col justify-center items-center bg-gray-100">
        <div class="bg-white p-8 rounded-xl shadow-2xl text-center max-w-md border-t-8 border-red-500">
            
            <div class="mb-6">
                <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto animate-pulse">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <h1 class="text-3xl font-black text-gray-800 mb-2">SIMULAÇÃO PAUSADA</h1>
            <p class="text-gray-500 mb-6">O Professor interrompeu o tempo do jogo. Aguarde instruções para continuar.</p>

            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
                <p class="text-xs text-gray-400 uppercase font-bold">Status da Turma</p>
                <p class="text-lg font-bold text-red-600">🛑 INTERVALO</p>
            </div>

            <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-indigo-800 font-bold text-sm underline">
                Tentar reconectar / Atualizar
            </a>
        </div>
    </div>
</x-app-layout>