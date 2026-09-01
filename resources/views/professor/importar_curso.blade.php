<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span>✨</span> {{ __('Importador de Planos de Curso por IA') }}
            </h2>
            <a href="{{ route('professor.avaliacoes.index') }}" 
               class="bg-gray-200 text-gray-800 px-4 py-2 rounded border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition text-xs font-black uppercase">
                ⬅ Cancelar
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-yellow-50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- 1. EXPLICATIVO PEDAGÓGICO --}}
            <div class="bg-indigo-100 border-4 border-black p-6 rounded-xl shadow-[6px_6px_0px_0px_black]">
                <h4 class="font-black text-lg text-indigo-950 uppercase mb-2 flex items-center gap-2">
                    <span>🧙‍♂️</span> Como funciona o Importador Inteligente?
                </h4>
                <p class="text-xs text-indigo-900 font-bold leading-relaxed">
                    Você pode subir um novo plano de curso para o simulador. A inteligência artificial do **Google Gemini** lerá as ementas, as UCs e extrairá todas as capacidades técnicas e socioemocionais. Em seguida, ela relacionará automaticamente cada uma delas com as métricas internas do ERP da Fábrica Digital v1.6 para realizar a avaliação automatizada dos seus alunos.
                </p>
            </div>

            {{-- FORMULÁRIO DE IMPORTAÇÃO --}}
            <div class="bg-white border-4 border-black rounded-xl shadow-[8px_8px_0px_0px_black] overflow-hidden">
                <div class="bg-purple-600 text-white p-4 border-b-4 border-black flex items-center gap-3">
                    <span class="text-2xl">✨</span>
                    <div>
                        <h3 class="font-black text-lg uppercase tracking-tighter">Novo Plano de Competências</h3>
                        <p class="text-[10px] text-purple-200 font-bold uppercase">Mapeamento Automatizado por IA</p>
                    </div>
                </div>

                <div class="p-8">
                    <form method="POST" action="{{ route('professor.avaliacoes.processar_importar') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        {{-- 1. NOME EXATO DO CURSO --}}
                        <div>
                            <label class="block text-xs font-black text-gray-700 uppercase mb-1">Nome do Curso no Sistema (Exato)</label>
                            <input type="text" name="curso" required placeholder="Ex: Técnico em Logística"
                                   class="w-full border-4 border-black rounded p-3 font-bold text-lg focus:ring-0 focus:border-indigo-600 shadow-[4px_4px_0px_0px_rgba(0,0,0,0.1)] bg-white">
                            <p class="text-[10px] font-bold text-gray-400 mt-2 uppercase">
                                * Este nome deve ser idêntico ao que você digita no campo "Curso" ao cadastrar ou editar o aluno.
                            </p>
                        </div>

                        {{-- 2. OPÇÃO A: COPIAR E COLAR TEXTO --}}
                        <div>
                            <label class="block text-xs font-black text-gray-700 uppercase mb-1">Opção A: Copiar e Colar o Texto do Plano de Curso (Recomendado)</label>
                            <textarea name="texto_plano" rows="10" placeholder="Abra o PDF do plano de curso, copie as tabelas de capacidades, conhecimentos ou ementas e cole o texto completo diretamente aqui..."
                                      class="w-full border-4 border-black rounded-lg p-3 text-xs font-bold focus:ring-0 focus:border-indigo-600 bg-white"></textarea>
                        </div>

                        {{-- DIVISOR VISUAL --}}
                        <div class="relative flex py-2 items-center">
                            <div class="flex-grow border-t-2 border-black border-dashed"></div>
                            <span class="flex-shrink mx-4 text-xs font-black text-gray-400 uppercase">OU</span>
                            <div class="flex-grow border-t-2 border-black border-dashed"></div>
                        </div>

                        {{-- 3. OPÇÃO B: CARREGAR ARQUIVO PDF (MÉTODO OFICIAL POR IA) --}}
                        <div>
                            <label class="block text-xs font-black text-gray-700 uppercase mb-1">Opção B: Carregar arquivo PDF Oficial (.pdf)</label>
                            <input type="file" name="arquivo_pdf" accept=".pdf"
                                   class="w-full border-4 border-black rounded p-2 font-bold text-sm bg-white focus:ring-0">
                        </div>

                        {{-- BOTÃO ENVIAR --}}
                        <div class="flex justify-end gap-4 border-t-2 border-dashed border-gray-300 pt-6 mt-6">
                            <a href="{{ route('professor.avaliacoes.index') }}" 
                               class="px-6 py-3 bg-white text-gray-700 border-2 border-black rounded font-black text-xs uppercase hover:bg-gray-200 transition">
                                Cancelar
                            </a>
                            
                            <button type="submit" 
                                    class="px-8 py-3 bg-purple-600 text-white rounded border-2 border-black font-black text-sm uppercase hover:bg-purple-700 shadow-[6px_6px_0px_0px_black] transition transform hover:-translate-y-1 active:translate-y-[1px] active:shadow-none flex items-center gap-2">
                                <span>✨</span> Iniciar Mapeamento IA
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>