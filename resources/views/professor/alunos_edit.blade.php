<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Aluno') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-semibold text-lg mb-4">Editando o aluno: {{ $aluno->nome }}</h3>
<form method="POST" action="{{ route('professor.alunos.destroy', $aluno) }}" onsubmit="return confirm('Você tem certeza que deseja excluir este aluno?');" class="inline-block ml-4">                   


                    
                        @csrf
                        @method('PUT')
                        
                        <!-- Nome do Aluno -->
                        <div>
                            <label for="nome">Novo Nome do Aluno</label>
                            <input id="nome" class="block mt-1 w-full" type="text" name="nome" value="{{ $aluno->nome }}" required autofocus />
                        </div>

                        <!-- Seleção de Turma -->
                        <div class="mt-4">
                            <label for="turma_id">Turma</label>
                            <select name="turma_id" id="turma_id" class="block mt-1 w-full">
                                @foreach ($turmas as $turma)
                                    <option value="{{ $turma->id }}" @selected($aluno->turma_id == $turma->id)>
                                        {{ $turma->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('professor.alunos.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                                Cancelar
                            </a>
                            
                            <x-primary-button>
                                {{ __('Salvar Alterações') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>