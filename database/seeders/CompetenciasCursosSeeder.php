<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CompetenciasCursosSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('aluno_avaliacoes')->delete();
        DB::table('competencias_cursos')->delete();
        Schema::enableForeignKeyConstraints();

        $cursos = ['Auxiliar de Linha de Produção', 'Almoxarife', 'Técnico em Móveis'];
        $dados = [];

        // =========================================================================
        // 1. MATRIZ: AUXILIAR DE LINHA DE PRODUÇÃO (ALP)
        // =========================================================================
        $dados = array_merge($dados, [
            [
                'curso' => 'Auxiliar de Linha de Produção',
                'unidade_curricular' => 'A. Letramento Digital (Págs. 41 a 43)',
                'categoria' => 'tecnica',
                'nome' => 'Elaborar gráficos e dashboards em nuvem',
                'descricao' => 'Capacidade de operar em ambiente Cloud (SaaS) e utilizar o Módulo de BI Operacional para interpretar gráficos gerados por suas próprias ações de produção.',
                'tipo_avaliacao' => 'automatica',
                'metrica_chave' => 'bi_acesso_indicadores',
            ],
            [
                'curso' => 'Auxiliar de Linha de Produção',
                'unidade_curricular' => 'B. Fundamentos dos Processos Logísticos (Págs. 45 a 47)',
                'categoria' => 'tecnica',
                'nome' => 'Identificar atividades de recebimento e inconformidades',
                'descricao' => 'Capacidade de confrontar dados de cargas recebidas no Módulo de Compras e identificar avarias/inconformidades simuladas pelo Caos, decidindo pela recusa técnica.',
                'tipo_avaliacao' => 'automatica',
                'metrica_chave' => 'compras_recusa_justificada',
            ],
            [
                'curso' => 'Auxiliar de Linha de Produção',
                'unidade_curricular' => 'C. Abastecimento da Produção (Págs. 61 a 64)',
                'categoria' => 'tecnica',
                'nome' => 'Movimentar materiais e registrar abastecimento',
                'descricao' => 'Capacidade de operar o Mapa WMS para realizar o endereçamento físico de matérias-primas e registrar digitalmente o abastecimento das máquinas ("Solicitar Material").',
                'tipo_avaliacao' => 'automatica',
                'metrica_chave' => 'wms_picking_concluido',
            ],
            [
                'curso' => 'Auxiliar de Linha de Produção',
                'unidade_curricular' => 'D. Programação do Abastecimento da Produção (Págs. 67 a 70)',
                'categoria' => 'tecnica',
                'nome' => 'Análise de MRP, BOM e sequenciamento de ordens',
                'descricao' => 'Capacidade de utilizar o PCP para realizar a análise de MRP, cruzando a necessidade da lista de materiais (BOM) com o estoque e priorizando ordens por MDE.',
                'tipo_avaliacao' => 'automatica',
                'metrica_chave' => 'pcp_prioridade_mde',
            ],
            [
                'curso' => 'Auxiliar de Linha de Produção',
                'unidade_curricular' => 'E. Sustentabilidade nos Processos Produtivos (Págs. 57 a 59)',
                'categoria' => 'tecnica',
                'nome' => 'Acompanhar custos e perdas de refugo (Scrap)',
                'descricao' => 'Capacidade de realizar o apontamento de "Peças Boas" e "Refugos", monitorando como as perdas impactam diretamente o capital de giro da empresa no BI.',
                'tipo_avaliacao' => 'automatica',
                'metrica_chave' => 'producao_refugo_apontado',
            ],
            [
                'curso' => 'Auxiliar de Linha de Produção',
                'unidade_curricular' => 'F. Medidas e Representações Gráficas (Págs. 76 a 78)',
                'categoria' => 'tecnica',
                'nome' => 'Interpretar especificações técnicas de materiais',
                'descricao' => 'Capacidade de ler e interpretar as especificações técnicas geradas pelo Gênesis IA para diferenciar e classificar insumos no sistema (Ex: Chapa MDF vs Parafuso).',
                'tipo_avaliacao' => 'automatica',
                'metrica_chave' => 'wms_enderecamento_concluido',
            ],
        ]);

        // =========================================================================
        // 2. MATRIZ: TÉCNICO EM MÓVEIS
        // =========================================================================
        $dados = array_merge($dados, [
            [
                'curso' => 'Técnico em Móveis',
                'unidade_curricular' => 'A. Gestão de Processos (Págs. 45 a 47)',
                'categoria' => 'tecnica',
                'nome' => 'Priorizar ordens de produção e gerenciar custos',
                'descricao' => 'Capacidade de gerenciar a demanda variável da IA, priorizando ordens curtas (MDE) e controlando custos de produção (MDF, ferragens) no Banco Central.',
                'tipo_avaliacao' => 'automatica',
                'metrica_chave' => 'financeiro_caixa_positivo',
            ],
            [
                'curso' => 'Técnico em Móveis',
                'unidade_curricular' => 'B. Processos Automatizados na Produção de Móveis (Págs. 42 a 44)',
                'categoria' => 'tecnica',
                'nome' => 'Integrar sistemas industriais e simular processos',
                'descricao' => 'Capacidade de vivenciar a Indústria 4.0 ao disparar ordens em nuvem geradas por IA, garantindo a integridade dos dados integrados (Validação de Ferro).',
                'tipo_avaliacao' => 'automatica',
                'metrica_chave' => 'producao_lote_concluido',
            ],
            [
                'curso' => 'Técnico em Móveis',
                'unidade_curricular' => 'C. Fundamentos da Movelaria (Págs. 39 a 41)',
                'categoria' => 'tecnica',
                'nome' => 'Embalar e consolidar conjuntos de peças acabadas',
                'descricao' => 'Capacidade de realizar a conferência e consolidação final do móvel no Módulo de Embalagem (Pack), separando perdas e organizando para a expedição.',
                'tipo_avaliacao' => 'automatica',
                'metrica_chave' => 'producao_lote_concluido',
            ],
            [
                'curso' => 'Técnico em Móveis',
                'unidade_curricular' => 'D. Gestão de Pessoas (Págs. 48 a 49)',
                'categoria' => 'tecnica',
                'nome' => 'Liderança, trabalho colaborativo e Job Rotation',
                'descricao' => 'Capacidade de atuar colaborativamente em fluxo multi-tenant interdependente e adaptar-se a novos setores na rotação de postos do professor.',
                'tipo_avaliacao' => 'manual',
                'metrica_chave' => null,
            ],
        ]);

        // =========================================================================
        // 3. MATRIZ: APRENDIZAGEM INDUSTRIAL - ALMOXARIFE
        // =========================================================================
        $dados = array_merge($dados, [
            [
                'curso' => 'Almoxarife',
                'unidade_curricular' => 'A. Letramento Digital (Págs. 41 a 44)',
                'categoria' => 'tecnica',
                'nome' => 'Uso operacional de computadores e acessibilidade',
                'descricao' => 'Capacidade do aluno (incluindo PCD) de navegar de forma fluida no sistema operacional do simulador utilizando recursos de acessibilidade ativa.',
                'tipo_avaliacao' => 'manual',
                'metrica_chave' => null,
            ],
            [
                'curso' => 'Almoxarife',
                'unidade_curricular' => 'B. Técnicas de Recebimento de Materiais (Págs. 63 a 66)',
                'categoria' => 'tecnica',
                'nome' => 'Conferência fiscal, quantitativa e qualitativa',
                'descricao' => 'Capacidade de receber cargas, conferir SKUs, quantidades fiscais e tratar inconformidades/avarias da Doca recusando cargas pelo Caos.',
                'tipo_avaliacao' => 'automatica',
                'metrica_chave' => 'compras_conferencia_concluida',
            ],
            [
                'curso' => 'Almoxarife',
                'unidade_curricular' => 'C. Técnicas de Armazenagem (Págs. 67 a 70)',
                'categoria' => 'tecnica',
                'nome' => 'Endereçar e gerir estruturas físicas no WMS',
                'descricao' => 'Capacidade de endereçar materiais no Mapa de Estoque de forma organizada (Rua, Coluna, Nível, Vão) e auditar quantidades no Inventário.',
                'tipo_avaliacao' => 'automatica',
                'metrica_chave' => 'wms_enderecamento_concluido',
            ],
            [
                'curso' => 'Almoxarife',
                'unidade_curricular' => 'D. Atividades de Expedição de Materiais (Págs. 71 a 76)',
                'categoria' => 'tecnica',
                'nome' => 'Operar listas de picking e expedir lotes',
                'descricao' => 'Capacidade de conferir requisições de material da produção, realizar a separação física do picking e registrar faturamentos na expedição.',
                'tipo_avaliacao' => 'automatica',
                'metrica_chave' => 'expedicao_faturamento_concluido',
            ],
        ]);

        // =========================================================================
        // 4. MATRIZ UNIVERSAL: CAPACIDADES SOCIOEMOCIONAIS (SEMENTE EM TODOS OS CURSOS)
        // =========================================================================
        foreach ($cursos as $c) {
            $dados = array_merge($dados, [
                [
                    'curso' => $c,
                    'unidade_curricular' => 'Competências Socioemocionais',
                    'categoria' => 'socioemocional',
                    'nome' => 'Demonstrar Visão Sistêmica',
                    'descricao' => 'Compreende que um atraso no Almoxarifado não é um problema isolado, mas sim um gargalo que afeta a Produção e compromete a lucratividade da empresa.',
                    'tipo_avaliacao' => 'manual',
                    'metrica_chave' => null,
                ],
                [
                    'curso' => $c,
                    'unidade_curricular' => 'Competências Socioemocionais',
                    'categoria' => 'socioemocional',
                    'nome' => 'Trabalhar em Equipe',
                    'descricao' => 'Atua de forma colaborativa e integrada na cadeia multi-tenant. Depende e apoia os colegas para que o fluxo do ERP funcione sem gargalos.',
                    'tipo_avaliacao' => 'manual',
                    'metrica_chave' => null,
                ],
                [
                    'curso' => $c,
                    'unidade_curricular' => 'Competências Socioemocionais',
                    'categoria' => 'socioemocional',
                    'nome' => 'Demonstrar Resiliência Emocional e Autogestão',
                    'descricao' => 'Mantém o equilíbrio operacional e organiza sua fila de trabalho mesmo sob a pressão do tempo do relógio ativo e das crises da Engine do Caos.',
                    'tipo_avaliacao' => 'manual',
                    'metrica_chave' => null,
                ],
                [
                    'curso' => $c,
                    'unidade_curricular' => 'Competências Socioemocionais',
                    'categoria' => 'socioemocional',
                    'nome' => 'Demonstrar Pensamento Analítico e Tomada de Decisão',
                    'descricao' => 'Analisa variáveis de estoque e caixa para tomar decisões financeiras e logísticas embasadas em dados, evitando prejuízos à holding.',
                    'tipo_avaliacao' => 'manual',
                    'metrica_chave' => null,
                ],
                [
                    'curso' => $c,
                    'unidade_curricular' => 'Competências Socioemocionais',
                    'categoria' => 'socioemocional',
                    'nome' => 'Demonstrar Atenção a Detalhes',
                    'descricao' => 'Mostra rigor na transcrição e preenchimento de dados operacionais, operando em conformidade com as exigências da Validação de Ferro.',
                    'tipo_avaliacao' => 'manual',
                    'metrica_chave' => null,
                ],
                [
                    'curso' => $c,
                    'unidade_curricular' => 'Competências Socioemocionais',
                    'categoria' => 'socioemocional',
                    'nome' => 'Demonstrar Capacidade de Planejamento e Organização',
                    'descricao' => 'Organiza o seu fluxo de trabalho, respeitando limites de cota de vendas e estruturando o WMS para otimizar o tempo das operações diárias.',
                    'tipo_avaliacao' => 'manual',
                    'metrica_chave' => null,
                ],
            ]);
        }

        DB::table('competencias_cursos')->insert($dados);
    }
}