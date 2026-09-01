<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Turma;
use App\Models\MateriaPrima;
use App\Models\ProdutoAcabado;
use App\Models\Fornecedor;
use App\Models\Cliente;
use Illuminate\Support\Facades\Http;

class GenesisController extends Controller
{
    public function gerarEstrutura(Request $request)
    {
        set_time_limit(300); 
        $turma = Turma::findOrFail($request->turma_id);
        $segmento = $turma->segmento ?? 'Indústria Geral';

        $skusExistentes = array_merge(
            MateriaPrima::where('turma_id', $turma->id)->pluck('sku')->toArray(),
            ProdutoAcabado::where('turma_id', $turma->id)->pluck('sku')->toArray()
        );

        $contexto = count($skusExistentes) > 0 
            ? "A fábrica já possui estes SKUs: " . implode(', ', $skusExistentes) . ". Gere apenas itens NOVOS."
            : "A fábrica está vazia. Comece do zero (SKUs: MP-001, PA-001).";

        $prompt = "Você é um engenheiro sênior de ERP industrial. Gere um setup industrial JSON para a empresa '{$turma->nome_empresa}' do setor '{$segmento}'.
        
        CONTEXTO DE SKUs EXISTENTES: {$contexto}

        DIRETRIZES OBRIGATÓRIAS DE LOCALIZAÇÃO E PADRÃO:
        1. BRASIL: Todos os dados (Nomes de Empresas, Pessoas, Endereços, Cidades, Estados) devem ser 100% DO BRASIL e realistas.
        2. PADRÃO SKU: Mantenha estritamente o formato 'MP-XXX' para Matérias-Primas e 'PA-XXX' para Produtos Acabados (Ex: MP-015, PA-004). Nunca desvie desse padrão.

        REGRAS DE DADOS (CADA CAMPO DEVE SER UM ITEM NO JSON):
        1. MATERIAIS (5 itens): SKU, Nome, Unidade.
        2. PRODUTOS (3 itens): SKU, Nome, Preço Venda, Tempo Produção (MÍNIMO 1 E MÁXIMO 5 DIAS - OBRIGATÓRIO) e RECEITA (BOM).
           *** IMPORTANTE: As quantidades na RECEITA (BOM) devem ser APENAS NÚMEROS INTEIROS (Sem decimais). ***
        3. FORNECEDORES (3 para cada material): Nome Razão Social, CNPJ, Telefone, Material SKU, Preço Unitário, Prazo Entrega Dias (MÍNIMO 3 E MÁXIMO 15 DIAS - OBRIGATÓRIO), CEP, Rua, Número, Bairro, Cidade, Estado UF.
        4. CLIENTES (5 itens): Razão Social, CNPJ, Telefone, CEP, Rua, Número, Bairro, Cidade, Estado UF.

        FORMATO DE RESPOSTA (JSON PURO):
        {
          \"materiais\": [{\"sku\": \"...\", \"nome\": \"...\", \"unidade\": \"...\"}],
          \"produtos\": [{\"sku\": \"...\", \"nome\": \"...\", \"preco_venda\": 0, \"tempo_producao\": 0, \"receita\": [{\"material_sku\": \"...\", \"quantidade\": 5}]}],
          \"clientes\": [{\"razao_social\": \"...\", \"cnpj\": \"...\", \"telefone\": \"...\", \"cep\": \"...\", \"rua\": \"...\", \"numero\": \"...\", \"bairro\": \"...\", \"cidade\": \"...\", \"uf\": \"...\"}],
          \"fornecedores\": [{\"nome_razao_social\": \"...\", \"cnpj\": \"...\", \"telefone\": \"...\", \"material_sku\": \"...\", \"preco_unitario\": 0, \"prazo_entrega\": 0, \"cep\": \"...\", \"rua\": \"...\", \"numero\": \"...\", \"bairro\": \"...\", \"cidade\": \"...\", \"uf\": \"...\"}]
        }";

        try {
            $apiKey = env('GEMINI_API_KEY');
            
            // Usando o mesmo modelo que você confirmou que funciona para você
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent?key=" . $apiKey;

            // A MÁGICA ESTÁ AQUI: retry(3, 2000) e response_mime_type
            $result = Http::timeout(180)
                ->retry(3, 2000)
                ->post($url, [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['response_mime_type' => 'application/json']
                ]);
            
            $res = $result->json();
            
            // Pega o texto da IA (que agora virá 100% limpo graças ao mime_type)
            $texto = $res['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            
            $sugestao = json_decode($texto, true);

            // TRAVA DE SEGURANÇA: Se a IA mandar vazio, ele te avisa em vez de mostrar caixas brancas
            if (empty($sugestao) || !isset($sugestao['materiais'])) {
                return back()->with('error', 'A IA processou, mas os dados vieram incompletos. Por favor, tente gerar novamente.');
            }

            return view('professor.genesis.validar', compact('turma', 'sugestao'));

        } catch (\Throwable $e) {
            return back()->with('error', 'Falha na IA (Gênesis): ' . $e->getMessage());
        }
    }

    public function salvarEstrutura(Request $request)
    {
        $turmaId = $request->turma_id;

        // 1. MATERIAIS
        foreach ($request->materiais ?? [] as $m) {
            MateriaPrima::firstOrCreate(
                ['turma_id' => $turmaId, 'sku' => $m['sku']],
                ['nome' => $m['nome'], 'unidade_medida' => $m['unidade']]
            );
        }

        // 2. PRODUTOS + BOM
        foreach ($request->produtos ?? [] as $p) {
            $produto = ProdutoAcabado::firstOrCreate(
                ['turma_id' => $turmaId, 'sku' => $p['sku']],
                ['nome' => $p['nome'], 'preco_venda' => $p['preco'], 'tempo_producao_dias' => $p['tempo']]
            );

            if (isset($p['receita'])) {
                foreach ($p['receita'] as $item) {
                    $mat = MateriaPrima::where('turma_id', $turmaId)->where('sku', $item['material_sku'])->first();
                    if ($mat) {
                        $produto->materiasPrimas()->syncWithoutDetaching([$mat->id => ['quantidade' => (int)$item['quantidade']]]);
                    }
                }
            }
        }

        // 3. FORNECEDORES
        foreach ($request->fornecedores ?? [] as $f) {
            $mat = MateriaPrima::where('turma_id', $turmaId)->where('sku', $f['material_sku'])->first();
            Fornecedor::firstOrCreate(
                ['turma_id' => $turmaId, 'cnpj_cpf' => $f['cnpj']],
                [
                    'nome_razao_social' => $f['nome'],
                    'telefone' => $f['telefone'],
                    'preco_unitario' => $f['preco'],
                    'tempo_entrega_dias' => $f['prazo'],
                    'materia_prima_id' => $mat ? $mat->id : null,
                    'cep' => $f['cep'],
                    'rua' => $f['rua'],
                    'numero' => $f['numero'],
                    'bairro' => $f['bairro'],
                    'cidade' => $f['cidade'],
                    'estado' => $f['uf']
                ]
            );
        }

        // 4. CLIENTES
        foreach ($request->clientes ?? [] as $c) {
            Cliente::firstOrCreate(
                ['turma_id' => $turmaId, 'cnpj_cpf' => $c['cnpj']],
                [
                    'nome_razao_social' => $c['nome'],
                    'telefone' => $c['telefone'],
                    'cep' => $c['cep'],
                    'rua' => $c['rua'],
                    'numero' => $c['numero'],
                    'bairro' => $c['bairro'],
                    'cidade' => $c['cidade'],
                    'estado' => $c['uf']
                ]
            );
        }

        return redirect()->route('professor.dashboard')->with('success', 'Fábrica Povoada!');
    }
}