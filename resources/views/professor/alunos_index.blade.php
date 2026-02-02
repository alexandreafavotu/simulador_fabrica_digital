<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gerenciar Alunos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Formulário para Criar Novo Aluno -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="font-semibold text-lg mb-4">Cadastrar Novo Aluno</h3>
                    
                    <form method="POST" action="{{ route('professor.alunos.store') }}">
                        @csrf
                        
                        <!-- Nome do Aluno -->
                        <div>
                            <label for="nome">Nome do Aluno</label>
                            <input id="nome" class="block mt-1 w-full" type="text" name="nome" required autofocus />
                        </div>

                        <!-- Seleção de Turma -->
                        <div class="mt-4">
                            <label for="turma_id">Turma</label>
                            <select name="turma_id" id="turma_id" class="block mt-1 w-full">
                                @foreach ($turmas as $turma)
                                    <option value="{{ $turma->id }}">{{ $turma->nome }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Cadastrar Aluno') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>

            <!-- Tabela para Listar Alunos Existentes -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-semibold text-lg mb-4">Alunos Cadastrados</h3>
                    


                    <table class="min-w-full">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome do Aluno</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Turma</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse ($alunos as $aluno)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">{{ $aluno->id }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $aluno->nome }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $aluno->turma->nome }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('professor.alunos.edit', $aluno) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                    
                    
                    <form method="POST" action="{{ route('professor.alunos.destroy', $aluno) }}" onsubmit="return confirm('Você tem certeza que deseja excluir este aluno?');" class="inline-block ml-4">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">
                            Excluir
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">Nenhum aluno cadastrado.</td>
            </tr>
        @endforelse
    </tbody>
</table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>