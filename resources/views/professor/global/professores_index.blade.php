<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight">
                🎓 Sala dos Professores (Gestão de Docentes)
            </h2>
            <button onclick="document.getElementById('modal-novo-prof').showModal()" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] border-2 border-black transition hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none flex items-center gap-2">
                <span>+</span> Novo Professor
            </button>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 shadow-md rounded">
                    <p class="font-bold">✅ Sucesso!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,0.2)] overflow-hidden">
                <div class="p-6 bg-indigo-50 border-b-4 border-black flex justify-between items-center">
                    <div>
                        <h3 class="font-black text-xl text-indigo-900">Quadro de Docentes</h3>
                        <p class="text-sm text-indigo-600">Estes usuários podem criar e gerenciar suas próprias turmas.</p>
                    </div>
                    <div class="bg-white px-4 py-2 rounded border-2 border-black font-mono font-bold text-xs">
                        Total: {{ $professores->count() }}
                    </div>
                </div>

                <div class="p-0">
                    @if($professores->isEmpty())
                        <div class="text-center py-16">
                            <span class="text-6xl">🧑‍🏫</span>
                            <h3 class="text-lg font-bold text-gray-600 mt-4">Nenhum outro professor cadastrado.</h3>
                            <p class="text-gray-400">Você é o único mestre deste universo por enquanto.</p>
                        </div>
                    @else
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-100 border-b-2 border-gray-200 text-xs uppercase text-gray-500 font-bold tracking-wider">
                                    <th class="p-4">Nome</th>
                                    <th class="p-4">Email (Login)</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4 text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($professores as $prof)
                                    <tr class="hover:bg-indigo-50 transition {{ $prof->ativo ? '' : 'bg-gray-100 text-gray-400' }}">
                                        <td class="p-4 font-bold text-gray-800">{{ $prof->name }}</td>
                                        <td class="p-4 text-gray-600 font-mono text-sm">{{ $prof->email }}</td>
                                        <td class="p-4">
                                            @if($prof->ativo)
                                                <span class="bg-green-100 text-green-800 text-[10px] font-black px-2 py-1 rounded border border-green-200 uppercase">Ativo</span>
                                            @else
                                                <span class="bg-red-100 text-red-800 text-[10px] font-black px-2 py-1 rounded border border-red-200 uppercase">Bloqueado</span>
                                            @endif
                                        </td>
                                        <td class="p-4 text-right">
                                            {{-- BOTÃO BLOQUEAR/DESBLOQUEAR (Substitui Excluir) --}}
                                            <form action="{{ route('professor.usuario.toggle', $prof->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                @if($prof->ativo)
                                                    <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-xs bg-red-50 hover:bg-red-100 px-3 py-1 rounded border border-red-200 transition">
                                                        🔒 Bloquear
                                                    </button>
                                                @else
                                                    <button type="submit" class="text-green-500 hover:text-green-700 font-bold text-xs bg-green-50 hover:bg-green-100 px-3 py-1 rounded border border-green-200 transition">
                                                        🔓 Ativar Acesso
                                                    </button>
                                                @endif
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL NOVO PROFESSOR (MANTIDO IGUAL) --}}
    <dialog id="modal-novo-prof" class="p-0 rounded-xl shadow-2xl backdrop:bg-gray-900/50 w-[90%] max-w-md open:animate-fade-in">
        <div class="bg-white border-4 border-black">
            <div class="flex justify-between items-center p-4 border-b-2 border-gray-100 bg-gray-50">
                <h3 class="text-xl font-black text-gray-800 flex items-center gap-2">
                    <span>👨‍🏫</span> Cadastrar Docente
                </h3>
                <button onclick="document.getElementById('modal-novo-prof').close()" class="text-gray-400 hover:text-red-500 font-bold text-2xl leading-none">&times;</button>
            </div>
            
            <form action="{{ route('professor.global.professores.store') }}" method="POST" class="p-6">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nome Completo</label>
                    <input type="text" name="name" required class="w-full border-2 border-gray-300 p-2 rounded focus:border-indigo-500 focus:ring-0 outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">E-mail (Login)</label>
                    <input type="email" name="email" required class="w-full border-2 border-gray-300 p-2 rounded focus:border-indigo-500 focus:ring-0 outline-none">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Senha Inicial (Escolha 3 Números)</label>
                    <div class="flex items-center">
                        <span class="bg-gray-200 border-2 border-r-0 border-black px-3 py-2 text-sm font-bold text-gray-600 rounded-l">Senai</span>
                        <input type="text" name="cod_acesso" maxlength="3" required placeholder="123" class="w-24 text-center border-2 border-black p-2 rounded-r font-black tracking-widest focus:border-indigo-500 focus:ring-0 outline-none">
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" onclick="document.getElementById('modal-novo-prof').close()" class="px-4 py-2 text-gray-600 font-bold hover:bg-gray-100 rounded transition">Cancelar</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-none transition">
                        Cadastrar
                    </button>
                </div>
            </form>
        </div>
    </dialog>
</x-app-layout>