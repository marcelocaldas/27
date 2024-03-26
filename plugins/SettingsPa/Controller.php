<?php

namespace SettingsPa;

use MapasCulturais\App;

class Controller extends \MapasCulturais\Controllers\EntityController
{
    use \MapasCulturais\Traits\ControllerAPI;

    function __construct()
    {
    }

    public function GET_querys()
    {
        $this->requireAuthentication();
        
        $app = App::i();

        if(!$app->user->is('admin')) {
            return;
        }

        $em = $app->em;
        $conn = $em->getConnection();

        $total_oportunidades = $conn->fetchColumn("select count(*) as total from opportunity");
        $total_oportunidades_publicados = $conn->fetchColumn("select count(*) as total from opportunity where status > 0");
        $total_oportunidades_rascunho = $conn->fetchColumn("select count(*) as total from opportunity where status = 0");
        $total_oportunidades_lixeira = $conn->fetchColumn("select count(*) as total from opportunity where status = -10");
        $total_oportunidades_arquivado = $conn->fetchColumn("select count(*) as total from opportunity where status = -2");
        // Oportunidades por area de atuação
        $results = $conn->fetchAll("
            select 
                t.term as area_atuacao,
                count(*) as total
            from 
                term_relation tr 
            join term t on t.id = tr.term_id 
            where 
                tr.object_type = 'MapasCulturais\Entities\Opportunity' and 
                t.taxonomy = 'area'
            group by area_atuacao
        ");
        foreach ($results as $_key => $_value) {
            $total_oportunidade_por_area_atuacao[$_value['area_atuacao']] = $_value['total'];
        }

        $total_espacos = $conn->fetchColumn("select count(*) as total from space");
        $total_espacos_publicados = $conn->fetchColumn("select count(*) as total from space where status > 0");
        $total_espacos_rascunho = $conn->fetchColumn("select count(*) as total from space where status = 0");
        $total_espacos_lixeira = $conn->fetchColumn("select count(*) as total from space where status = -10");
        $total_espacos_arquivado = $conn->fetchColumn("select count(*) as total from space where status = -2");
        // Espaços por area de atuação
        $results = $conn->fetchAll("
            select 
                t.term as area_atuacao,
                count(*) as total
            from 
                term_relation tr 
            join term t on t.id = tr.term_id 
            where 
                tr.object_type = 'MapasCulturais\Entities\Space' and 
                t.taxonomy = 'area'
            group by area_atuacao
        ");
        foreach ($results as $_key => $_value) {
            $total_espaco_por_area_atuacao[$_value['area_atuacao']] = $_value['total'];
        }

        $total_projetos = $conn->fetchColumn("select count(*) as total from project");
        $total_projetos_publicados = $conn->fetchColumn("select count(*) as total from project where status > 0");
        $total_projetos_rascunho = $conn->fetchColumn("select count(*) as total from project where status = 0");
        $total_projetos_lixeira = $conn->fetchColumn("select count(*) as total from project where status = -10");
        $total_projetos_arquivado = $conn->fetchColumn("select count(*) as total from project where status = -2");

        $total_inscricoes = $conn->fetchColumn("select count(*) as total from registration");
        $total_inscricoes_enviadas = $conn->fetchColumn("select count(*) as total from registration where status > 0");
        $total_inscricoes_rascunho = $conn->fetchColumn("select count(*) as total from registration where status = 0");
        $total_inscricoes_selecionadas = $conn->fetchColumn("select count(*) as total from registration where status = 10");
        $total_inscricoes_nao_selecionadas = $conn->fetchColumn("select count(*) as total from registration where status = 3");
        $total_inscricoes_nao_invalidas = $conn->fetchColumn("select count(*) as total from registration where status = 2");
        $total_inscricoes_nao_suplente = $conn->fetchColumn("select count(*) as total from registration where status = 8");

        $total_agentes = $conn->fetchColumn("SELECT count(*) as Total from agent");
        $total_agentes_publicado = $conn->fetchColumn("SELECT count(*) as Total from agent where status = 1");
        $total_agentes_rascunhos = $conn->fetchColumn("SELECT count(*) as Total from agent where status = 0");
        $total_agentes_lixeira = $conn->fetchColumn("SELECT count(*) as Total from agent where status = -10");
        $total_agentes_arquivados = $conn->fetchColumn("SELECT count(*) as Total from agent where status = -2");
        $total_agentes_individual = $conn->fetchColumn("select count(*) from agent a where type = 1");
        $total_agentes_coletivo = $conn->fetchColumn("select count(*) from agent a where type = 2");
        $total_agentes_idoso = $conn->fetchColumn("select count(*) from agent_meta am where am.key = 'idoso' and value is not null");
        $total_agente_pessoa_fisica = $conn->fetchColumn("select count(*) as total from agent_meta am where am.key = 'cpf' and am.object_id not in (select am.object_id  from agent_meta am where am.key = 'cnpj' and am.value is null)");
        $total_agente_MEI = $conn->fetchColumn("select count(*) as total from agent_meta am where am.key = 'cpf' and am.object_id in (select am.object_id  from agent_meta am where am.key = 'cnpj' and am.value is not null)");
        $total_agente_pessoa_juridica = $conn->fetchColumn("select count(*) as total from agent_meta am where am.key = 'cnpj' and am.object_id not in (select am.object_id  from agent_meta am where am.key = 'cpf' and am.value is null)");
        $outras_comunidades_tradicionais = $conn->fetchColumn("select count(*) as total from agent_meta am where am.key = 'comunidadesTradicionalOutros'");
        $total_agentes_inscritos_em_editais = $conn->fetchColumn("select count(*) as total from agent a where a.id in (select r.agent_id from registration r where status > 0)");
        $total_agentes_nunca_inscritos_em_editais = $conn->fetchColumn("select count(*) as total from agent a where a.id not in (select r.agent_id from registration r)");
        $total_agentes_contemplados_em_editais = $conn->fetchColumn("select distinct count(r.agent_id) from registration r where status = 10");
        $total_agentes_nao_contemplados_em_editais = $conn->fetchColumn("select distinct count(r.agent_id) from registration r where status in ('2','3','8')");

        // Agentes por area de atuação
        $results = $conn->fetchAll("
            select 
                t.term as area_atuacao,
                count(*) as total
            from 
                term_relation tr 
            join term t on t.id = tr.term_id 
            where 
                tr.object_type = 'MapasCulturais\Entities\Agent' and 
                t.taxonomy = 'area'
            group by area_atuacao
        ");
        foreach ($results as $_key => $_value) {
            $total_agentes_por_area_atuacao[$_value['area_atuacao']] = $_value['total'];
        }

        // Individuais sem CNPJ
        $total_agentes_individuais_com_cpf_sem_cnpj = $conn->fetchColumn("
            select 
                count(*) as total 
            from 
                agent a 
            where 
                a.type = 1 and 
                a.id not in (select am.object_id from agent_meta am where am.key = 'cnpj' and (am.value is null or am.value = ''))
        ");

        // Individuais com CNPJ
        $total_agentes_individuais_com_cpf_com_cnpj = $conn->fetchColumn("
            select 
                count(*) as total 
            from 
                agent a 
            where 
                a.type = 1 and 
                a.id not in (select am.object_id from agent_meta am where am.key = 'cnpj' and am.value is not null)
        ");

        // Coletivos sem CNPJ
        $total_agentes_coletivos_com_cpf_sem_cnpj = $conn->fetchColumn("
          select 
              count(*) as total 
          from 
              agent a 
          where 
              a.type = 2 and 
              a.id not in (select am.object_id from agent_meta am where am.key = 'cnpj' and (am.value is null or am.value = ''))
      ");

        // Coletivos com CNPJ
        $total_agentes_coletivos_com_cpf_com_cnpj = $conn->fetchColumn("
          select 
              count(*) as total 
          from 
              agent a 
          where 
              a.type = 2 and 
              a.id not in (select am.object_id from agent_meta am where am.key = 'cnpj' and am.value is not null)
      ");

        // Comunidade tadicional
        $results = $conn->fetchAll("select 
                                    CASE 
                                        WHEN am.value IS NULL OR am.value = '' THEN 'Não Informado'
                                        else am.value
                                    END AS comunidade,
                                    count(*) as total
                                from 
                                    agent_meta am 
                                where 
                                    am.key = 'comunidadesTradicional'
                                group by 
                                    comunidade");
        foreach ($results as $_key => $_value) {
            $comunidadeTradicional[$_value['comunidade']] = $_value['total'];
        }

        // Pessoa com deficiencia
        $results = $conn->fetchAll("select 
                                    CASE 
                                    WHEN am.value IS NULL OR am.value = '' THEN 'Não Informado'
                                    else am.value
                                    END AS pessoaDeficiente,
                                    count(*) as total
                                from 
                                    agent_meta am 
                                where 
                                    am.key = 'pessoaDeficiente'
                                group by 
                                    pessoaDeficiente");

        foreach ($results as $_key => $_value) {
            $pessoaDeficiente[$_value['pessoadeficiente']] = $_value['total'];
        }

        // Faixa de idade
        $results = $conn->fetchAll("
                                SELECT 
                                CONCAT(FLOOR((idade::numeric / 10)) * 10, ' - ', FLOOR((idade::numeric / 10) + 1) * 10 - 1) AS faixa_idade,
                                COUNT(*) AS total
                            FROM (
                                SELECT 
                                    CASE 
                                        WHEN EXTRACT(YEAR FROM age(current_date, am.value::date)) < 1 THEN '0' 
                                        WHEN EXTRACT(YEAR FROM age(current_date, am.value::date)) > 120 THEN '121' 
                                        ELSE EXTRACT(YEAR FROM age(current_date, am.value::date))::text 
                                    END AS idade
                                FROM 
                                    agent_meta am 
                                    join agent a on am.object_id = a.id
                                WHERE 
                                    am.key = 'dataDeNascimento' AND
                                    am.value <> '' and
                                    a.type = 1
                            ) AS subquery
                            GROUP BY 
                                CONCAT(FLOOR((idade::numeric / 10)) * 10, ' - ', FLOOR((idade::numeric / 10) + 1) * 10 - 1)
                            ORDER BY 
                                faixa_idade;
                            ");

        foreach ($results as $_key => $_value) {
            $faixas_de_idade[$_value['faixa_idade']] = $_value['total'];
        }

        // tempo de funcao
        $results = $conn->fetchAll("
                        SELECT 
                        CONCAT(FLOOR((idade::numeric / 10)) * 10, ' - ', FLOOR((idade::numeric / 10) + 1) * 10 - 1) AS faixa_idade,
                        COUNT(*) AS total
                    FROM (
                        SELECT 
                            CASE 
                                WHEN EXTRACT(YEAR FROM age(current_date, am.value::date)) < 1 THEN '0' 
                                WHEN EXTRACT(YEAR FROM age(current_date, am.value::date)) > 120 THEN '121' 
                                ELSE EXTRACT(YEAR FROM age(current_date, am.value::date))::text 
                            END AS idade
                        FROM 
                            agent_meta am 
                            join agent a on am.object_id = a.id
                        WHERE 
                            am.key = 'dataDeNascimento' AND
                            am.value <> '' and
                            a.type = 2
                    ) AS subquery
                    GROUP BY 
                        CONCAT(FLOOR((idade::numeric / 10)) * 10, ' - ', FLOOR((idade::numeric / 10) + 1) * 10 - 1)
                    ORDER BY 
                        faixa_idade;
                    ");

        foreach ($results as $_key => $_value) {
            $tempo_funcao[$_value['faixa_idade']] = $_value['total'];
        }

        $_data = [
            'OPORTUNIDADES',
            'Total de oportunidades' => $total_oportunidades[0],
            'Total de oportunidades publicadas' => $total_oportunidades_publicados[0],
            'Total de oportunidades rascunho' => $total_oportunidades_rascunho[0],
            'Total de oportunidades lixeira' => $total_oportunidades_lixeira[0],
            'Total de oportunidades arquivados' => $total_oportunidades_arquivado[0],
            'Total de oportunidades por área de atuação' => $total_oportunidade_por_area_atuacao,
            'ESPAÇOS',
            'Total de espacos' => $total_espacos[0],
            'Total de espacos publicados' => $total_espacos_publicados[0],
            'Total de espacos rascunho' => $total_espacos_rascunho[0],
            'Total de espacos lixeira' => $total_espacos_lixeira[0],
            'Total de espacos arquivados' => $total_espacos_arquivado[0],
            'Total de espacos por área de atuação' => $total_espaco_por_area_atuacao,
            'PROJETOS',
            'Total de projetos' => $total_projetos[0],
            'Total de projetos publicados' => $total_projetos_publicados[0],
            'Total de projetos rascunho' => $total_projetos_rascunho[0],
            'Total de projetos lixeira' => $total_projetos_lixeira[0],
            'Total de projetos arquivados' => $total_projetos_arquivado[0],
            'INSCRIÇÕES',
            'Total de inscrições' => $total_inscricoes[0],
            'Total de inscrições enviadas' => $total_inscricoes_enviadas[0],
            'Total de inscrições rascunho' => $total_inscricoes_rascunho[0],
            'Total de inscrições selecionadas' => $total_inscricoes_selecionadas[0],
            'Total de inscrições não selecionadas' => $total_inscricoes_nao_selecionadas[0],
            'Total de inscrições inválidas' => $total_inscricoes_nao_invalidas[0],
            'Total de inscrições suplente' => $total_inscricoes_nao_suplente[0],
            'AGENTES',
            'Total de agentes' => $total_agentes[0],
            'Total de agentes COM inscrições' => $total_agentes_inscritos_em_editais[0],
            'Total de agentes SEM inscrições' => $total_agentes_nunca_inscritos_em_editais[0],
            'Total de agentes CONTEMPLADOS em algum edital' => $total_agentes_contemplados_em_editais[0],
            'Total de agentes NÃO CONTEMPLADOS em editais' => $total_agentes_nao_contemplados_em_editais[0],
            'Total de agentes publicados' => $total_agentes_publicado[0],
            'Total de agentes rascunhos' => $total_agentes_rascunhos[0],
            'Total de agentes lixeira' => $total_agentes_lixeira[0],
            'Total de agentes arquivados' => $total_agentes_arquivados[0],
            'Total de agentes somente com CPF (Pessoa Física)' => $total_agente_pessoa_fisica[0],
            'Total de agentes somente com CNPJ (Pessoa Jurídica)' => $total_agente_pessoa_juridica[0],
            'Total de agentes com CPF e CNPJ (MEI)' => $total_agente_MEI[0],
            'Total de agentes individual' => $total_agentes_individual[0],
            'Total de agentes coletivos' => $total_agentes_coletivo[0],
            'Total de agentes idoso' => $total_agentes_idoso[0],
            'Total de agentes comunidades tradicional' => $comunidadeTradicional,
            'Total de agentes outras comunidades tradicionais' => $outras_comunidades_tradicionais[0],
            'Total de agentes por pessoa com deficiência' => $pessoaDeficiente,
            'Total de agentes individual por faixa de idade' => $faixas_de_idade,
            'Total de agentes coletivo por tempo de fundação' => $tempo_funcao,
            'Total de agentes individuais SEM CNPJ' => $total_agentes_individuais_com_cpf_sem_cnpj[0],
            'Total de agentes individuais COM CNPJ' => $total_agentes_individuais_com_cpf_com_cnpj[0],
            'Total de agentes coletivos SEM CNPJ' => $total_agentes_coletivos_com_cpf_sem_cnpj[0],
            'Total de agentes coletivos COM CNPJ' => $total_agentes_coletivos_com_cpf_com_cnpj[0],
            'Total de agentes por área de atuação' => $total_agentes_por_area_atuacao,
        ];

        $multipleValues = [
            'raca' => (object) ['complement' => '', 'value' => 'raca'],
            'municipio' => (object) ['complement' => '', 'value' => 'En_Municipio'],
            'genero' => (object) ['complement' => '', 'value' => 'genero'],
            'escolaridade' => (object) ['complement' => '', 'value' => 'escolaridade'],
        ];

        foreach ($multipleValues as $key => $data) {
            $results = $conn->fetchAll("
                SELECT 
                    CASE 
                        WHEN normalized_value IS NULL OR normalized_value = '' THEN 'Não Informado' 
                        ELSE normalized_value 
                    END AS {$key},
                    COUNT(*) AS total
                FROM (
                    SELECT 
                        unaccent(lower(CASE 
                            WHEN am.value IS NULL OR am.value = '' THEN 'Não Informado' 
                            ELSE am.value 
                        END)) AS normalized_value
                    FROM 
                        agent_meta am 
                    WHERE 
                        am.key = '{$data->value}' 
                        AND (am.value IS NOT NULL OR am.value = '') 
                ) AS subquery
                GROUP BY 
                    {$key}
                order by {$key} asc;
            ");

            $type = "Total de agentes por {$key}";
            foreach ($results as $_key => $_value) {
                $_data[$type][$_value[$key]] = $_value['total'];
            }
        }

        dump($_data);
    }
}
