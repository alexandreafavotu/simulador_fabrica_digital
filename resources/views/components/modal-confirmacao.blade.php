@props(['id', 'title' => 'Tem certeza?', 'message' => 'Esta ação não pode ser desfeita.', 'action', 'method' => 'POST', 'buttonText' => 'Confirmar', 'type' => 'danger'])

<div x-data="{ open: false }" class="inline-block">
    {{-- Botão que dispara o modal (O que o usuário clica na lista) --}}
    <button type="button" @click="open = true" {{ $attributes->merge(['class' => '']) }}>
        {{ $slot }}
    </button>

    {{-- O Modal em si --}}
    <template x-teleport="body">
        <div x-show="open" 
             class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <div @click.away="open = false" 
                 class="bg-white border-4 border-black rounded-xl shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] w-full max-w-md overflow-hidden">
                
                {{-- Cabeçalho --}}
                <div class="{{ $type === 'danger' ? 'bg-red-500' : 'bg-yellow-400' }} p-4 border-b-4 border-black flex items-center gap-3">
                    <span class="text-2xl">{{ $type === 'danger' ? '⚠️' : '❓' }}</span>
                    <h3 class="font-black text-xl text-black uppercase">{{ $title }}</h3>
                </div>

                {{-- Corpo --}}
                <div class="p-6">
                    <p class="text-gray-700 font-bold text-lg leading-tight">{{ $message }}</p>
                    
                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" @click="open = false" 
                                class="px-6 py-2 border-2 border-black rounded font-black text-xs uppercase hover:bg-gray-100 transition">
                            Cancelar
                        </button>

                        <form :action="'{{ $action }}'" method="POST">
                            @csrf
                            @method($method)
                            <button type="submit" 
                                    class="{{ $type === 'danger' ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }} text-white border-2 border-black shadow-[4px_4px_0px_0px_black] active:shadow-none active:translate-x-[2px] active:translate-y-[2px] px-8 py-2 rounded font-black text-xs uppercase transition">
                                {{ $buttonText }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>