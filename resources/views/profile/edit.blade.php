<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                @if(Auth::user()->acessibilidade_visual)
    <svg class="w-8 h-8 inline" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="2" style="stroke: #FFFF00 !important;">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
        <circle cx="12" cy="7" r="4"></circle>
    </svg>
@else
    <span class="text-3xl">👤</span> 
@endif
{{ __('Minha Conta') }}
            </h2>
            <button onclick="history.back()" 
               class="bg-white text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase flex items-center gap-2">
                @if(Auth::user()->acessibilidade_visual)
    <svg class="w-4 h-4 inline" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="4" style="stroke: #FFFF00 !important;">
        <path d="M19 12H5M12 19l-7-7 7-7"></path>
    </svg>
@else
    ⬅ 
@endif
Voltar
            </button>
        </div>
    </x-slot>

    <div class="py-12 bg-yellow-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- CARTÃO DE IDENTIFICAÇÃO (APENAS VISUALIZAÇÃO) --}}
            <div class="p-8 bg-white border-4 border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,0.1)] rounded-xl flex items-center gap-6">
                <div class="p-4 rounded-full border-2 border-black" style="{{ Auth::user()->acessibilidade_visual ? 'background-color: #000000 !important; border-color: #FFFF00 !important;' : 'background-color: #f3f4f6;' }}">
    @if(Auth::user()->acessibilidade_visual)
        <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="2" style="stroke: #FFFF00 !important;">
            <rect x="3" y="4" width="18" height="16" rx="2"></rect>
            <path d="M7 8h10M7 12h10M7 16h6"></path>
        </svg>
    @else
        <span class="text-4xl">🆔</span>
    @endif
</div>
                <div>
                    <h3 class="font-black text-xl text-gray-800 uppercase">{{ Auth::user()->name }}</h3>
                    <p class="text-sm font-bold text-gray-500">{{ Auth::user()->email }}</p>
                    <p class="text-xs font-bold text-indigo-600 mt-1 uppercase bg-indigo-50 px-2 py-1 rounded border border-indigo-200 inline-block">
                        {{ Auth::user()->tipo == 'professor' ? 'Game Master' : 'Aluno Operador' }}
                    </p>
                </div>
            </div>

            {{-- BLOCO ÚNICO: ALTERAR SENHA --}}
            <div class="p-8 bg-white border-4 border-black shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] rounded-xl">
                <div class="max-w-xl mx-auto">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="text-center text-xs font-bold text-gray-400 uppercase">
                <p>Para alterações de e-mail ou exclusão de conta, contate o Administrador.</p>
            </div>

        </div>
    </div>
</x-app-layout>