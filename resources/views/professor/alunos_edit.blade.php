<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
            <span>✏️</span> {{ __('Ajustar Perfil e Acessibilidade') }}
        </h2>
    </x-slot>

    <div class="py-8 bg-yellow-50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <!-- VOLTAR -->
            <div class="mb-4">
                <a href="{{ route('professor.global.alunos') }}" class="text-xs font-black uppercase text-gray-500 hover:text-black transition flex items-center gap-1">
                    ⬅️ Voltar para a lista
                </a>
            </div>

            <!-- CARD PRINCIPAL NEO-BRUTALISTA -->
            <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                
                <!-- CABEÇALHO DO CARD -->
                <div class="bg-indigo-600 text-white p-4 border-b-4 border-black flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">👤</span>
                        <h3 class="font-black text-lg uppercase tracking-wide">Dados do Aluno</h3>
                    </div>
                    <span class="text-[10px] bg-black/20 px-2 py-1 rounded font-bold uppercase">ID: #{{ $aluno->id }}</span>
                </div>

                <div class="p-8">
                    <form method="POST" action="{{ route('professor.alunos.atualizar', $aluno->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <!-- NOME -->
                            <div>
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">Nome Completo do Aluno</label>
                                <input type="text" name="nome" value="{{ $aluno->name }}" required 
                                       class="w-full border-4 border-black rounded p-3 font-bold text-lg focus:ring-0 focus:border-indigo-600 shadow-[4px_4px_0px_0px_rgba(0,0,0,0.1)]">
                            </div>

                            <!-- CURSO -->
                            <div>
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">Curso</label>
                                <input type="text" name="curso" value="{{ $aluno->curso }}" required placeholder="Ex: Almoxarife"
                                       class="w-full border-4 border-black rounded p-3 font-bold text-lg focus:ring-0 focus:border-indigo-600 shadow-[4px_4px_0px_0px_rgba(0,0,0,0.1)]">
                            </div>

                            <!-- ANO / TURMA (SALVA JUNTO NA COLUNA 'ano_letivo' DO BANCO DE DADOS) -->
                            <div>
                                <label class="block text-xs font-black text-gray-700 uppercase mb-1">Ano / Turma</label>
                                <input type="text" name="ano_letivo" value="{{ $aluno->ano_letivo }}" required placeholder="Ex: 2026 - Manhã"
                                       class="w-full border-4 border-black rounded p-3 font-bold text-lg focus:ring-0 focus:border-indigo-600 shadow-[4px_4px_0px_0px_rgba(0,0,0,0.1)]">
                            </div>

                            <!-- SEÇÃO DE ACESSIBILIDADE -->
                            <div class="mt-10 p-6 bg-yellow-200 border-4 border-black shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                                <h3 class="text-lg font-black uppercase mb-4 flex items-center gap-2">
                                    <svg class="w-6 h-6 inline-block" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" stroke="black" stroke-width="1.5"/><path d="M5 11.5c3-1.5 11-1.5 14 0M12 10.5l-3.5 8M12 10.5l3.5 8" stroke="black" stroke-width="1.5" stroke-linecap="round"/><circle cx="12" cy="6.5" r="2" fill="#3ABEF9" stroke="black" stroke-width="1"/><circle cx="5" cy="11.5" r="1.5" fill="#3ABEF9" stroke="black" stroke-width="1"/><circle cx="19" cy="11.5" r="1.5" fill="#3ABEF9" stroke="black" stroke-width="1"/><circle cx="8.5" cy="18.5" r="1.5" fill="#3ABEF9" stroke="black" stroke-width="1"/><circle cx="15.5" cy="18.5" r="1.5" fill="#3ABEF9" stroke="black" stroke-width="1"/></svg>Recursos de Inclusão
                                </h3>
                                
                                <div class="grid grid-cols-1 gap-4">
                                    <!-- Visual -->
                                    <label class="flex items-center p-4 border-2 border-black bg-white hover:bg-yellow-50 cursor-pointer transition">
                                        <input type="hidden" name="acessibilidade_visual" value="0">
                                        <input type="checkbox" name="acessibilidade_visual" value="1" {{ $aluno->acessibilidade_visual ? 'checked' : '' }} 
                                               class="w-8 h-8 border-4 border-black text-indigo-600 focus:ring-0">
                                        <div class="ml-4">
                                            <span class="block font-black uppercase text-sm">🌗 Modo Alto Contraste</span>
                                            <span class="text-[10px] font-bold text-gray-500 uppercase">Fundo preto e texto amarelo para baixa visão.</span>
                                        </div>
                                    </label>

                                    <!-- Motor -->
                                    <label class="flex items-center p-4 border-2 border-black bg-white hover:bg-yellow-50 cursor-pointer transition">
                                        <input type="hidden" name="acessibilidade_motora" value="0">
                                        <input type="checkbox" name="acessibilidade_motora" value="1" {{ $aluno->acessibilidade_motora ? 'checked' : '' }} 
                                               class="w-8 h-8 border-4 border-black text-indigo-600 focus:ring-0">
                                        <div class="ml-4">
                                            <span class="block font-black uppercase text-sm">⌨️ Auxílio de Navegação</span>
                                            <span class="text-[10px] font-bold text-gray-500 uppercase">Destaque visual em campos e navegação por teclado.</span>
                                        </div>
                                    </label>

                                    <!-- Cognitivo (DESATIVADO NO MOMENTO) -->
                                    {{--<label class="flex items-center p-4 border-2 border-black bg-white hover:bg-yellow-50 cursor-pointer transition">
                                       <input type="hidden" name="acessibilidade_cognitiva" value="0">
                                        <input type="checkbox" name="acessibilidade_cognitiva" value="1" {{ $aluno->acessibilidade_cognitiva ? 'checked' : '' }} 
                                               class="w-8 h-8 border-4 border-black text-indigo-600 focus:ring-0">
                                        <div class="ml-4">
                                            <span class="block font-black uppercase text-sm">Linguagem Simplificada</span>
                                            <span class="text-[10px] font-bold text-gray-500 uppercase">Termos técnicos traduzidos para facilitar a compreensão.</span>
                                        </div>
                                    </label>--}}

                                    <!-- 4. ÁUDIO (LEITURA DE TELA) -->
<label class="flex items-center p-4 border-2 border-black bg-white hover:bg-yellow-50 cursor-pointer transition">
    <input type="hidden" name="acessibilidade_audio" value="0">
    <input type="checkbox" name="acessibilidade_audio" value="1" {{ $aluno->acessibilidade_audio ? 'checked' : '' }} 
           class="w-8 h-8 border-4 border-black text-indigo-600 focus:ring-0">
    <div class="ml-4">
        <span class="block font-black uppercase text-sm">🔊 Leitura de Tela (Áudio)</span>
        <span class="text-[10px] font-bold text-gray-500 uppercase">Lê o conteúdo em voz alta ao passar o mouse ou focar.</span>
    </div>
</label>

<!-- 5. PICTOGRAMAS -->
<label class="flex items-center p-4 border-2 border-black bg-white hover:bg-yellow-50 cursor-pointer transition">
    <input type="hidden" name="acessibilidade_pictogramas" value="0">
    <input type="checkbox" name="acessibilidade_pictogramas" value="1" {{ $aluno->acessibilidade_pictogramas ? 'checked' : '' }} 
           class="w-8 h-8 border-4 border-black text-indigo-600 focus:ring-0">
    <div class="ml-4">
        <span class="block font-black uppercase text-sm">🖼️ Pictogramas de Apoio</span>
        <span class="text-[10px] font-bold text-gray-500 uppercase">Exibe ícones simplificados junto aos textos técnicos do MENU.<br>(OBS.: Não deve ser utilizado junto ao modo Alto Contraste).</span>
    </div>
</label>

<!-- 6. VLIBRAS -->
<label class="flex items-center p-4 border-2 border-black bg-white hover:bg-yellow-50 cursor-pointer transition">
    <input type="hidden" name="acessibilidade_libras" value="0">
    <input type="checkbox" name="acessibilidade_libras" value="1" {{ $aluno->acessibilidade_libras ? 'checked' : '' }} 
           class="w-8 h-8 border-4 border-black text-indigo-600 focus:ring-0">
    <div class="ml-4">
        <span class="block font-black uppercase text-sm">🧏 Tradutor de Libras (VLibras)</span>
        <span class="text-[10px] font-bold text-gray-500 uppercase">Ativa o assistente virtual de língua de sinais brasileira.</span>
    </div>
</label>
                                </div>
                            </div>

                            <!-- BOTÕES -->
                            <div class="flex flex-col md:flex-row items-center gap-4 pt-6">
                                <button type="submit" class="w-full md:w-2/3 bg-green-500 hover:bg-green-600 text-white font-black py-4 px-6 rounded border-4 border-black shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] active:shadow-none active:translate-x-[2px] active:translate-y-[2px] transition-all uppercase tracking-widest">
                                    💾 Salvar Preferências
                                </button>
                                <a href="{{ route('professor.global.alunos') }}" class="w-full md:w-1/3 text-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-black py-4 px-6 rounded border-4 border-black shadow-[6px_6px_0px_0px_rgba(0,0,0,0.1)] transition-all uppercase text-xs">
                                    Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>