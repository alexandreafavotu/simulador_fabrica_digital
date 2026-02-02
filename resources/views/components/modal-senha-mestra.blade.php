@props(['action', 'id', 'titulo' => 'AÇÃO DESTRUTIVA', 'mensagem' => 'Esta ação não pode ser desfeita.'])

{{-- BOTÃO QUE ABRE O MODAL (O slot é o conteúdo do botão, ex: "Excluir") --}}
<button type="button" onclick="document.getElementById('modal-security-{{ $id }}').showModal()" {{ $attributes }}>
    {{ $slot }}
</button>

{{-- O MODAL EM SI --}}
<dialog id="modal-security-{{ $id }}" class="p-0 rounded-xl shadow-2xl backdrop:bg-red-900/80 w-[90%] max-w-md open:animate-fade-in border-4 border-red-600">
    <div class="bg-white">
        {{-- Cabeçalho de Perigo --}}
        <div class="bg-red-600 text-white p-4 flex items-center justify-between">
            <h3 class="font-black text-xl flex items-center gap-2">
                <span>☢️</span> {{ $titulo }}
            </h3>
            <button type="button" onclick="document.getElementById('modal-security-{{ $id }}').close()" class="text-white hover:text-black font-bold text-2xl">&times;</button>
        </div>

        <form action="{{ $action }}" method="POST" class="p-6">
            @csrf
            {{-- Se o método for DELETE ou PUT, injetamos aqui --}}
            @if(str_contains($attributes->get('method', 'POST'), 'DELETE')) @method('DELETE') @endif
            @if(str_contains($attributes->get('method', 'POST'), 'PUT')) @method('PUT') @endif

            <p class="text-gray-700 font-bold mb-2">{{ $mensagem }}</p>
            <p class="text-sm text-gray-500 mb-6">Para confirmar, digite a Senha Mestra do Professor.</p>

            <div class="mb-6">
                <input type="password" name="senha_mestra" required placeholder="Senha Mestra" 
                       class="w-full text-center text-xl tracking-widest border-2 border-red-200 p-3 rounded focus:border-red-600 focus:ring-0 outline-none text-red-700 font-bold placeholder-red-200">
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-100 pt-4">
                <button type="button" onclick="document.getElementById('modal-security-{{ $id }}').close()" class="px-4 py-2 text-gray-500 font-bold hover:bg-gray-100 rounded">Cancelar</button>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition">
                    CONFIRMAR
                </button>
            </div>
        </form>
    </div>
</dialog>