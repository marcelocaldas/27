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

        // $total_oportunidades = $conn->fetchColumn("select count(*) as total from opportunity");
        $total_oportunidades_publicados = $conn->fetchColumn("select count(*) as total from opportunity where status > 0");
        $total_oportunidades_oficiais = $conn->fetchColumn("select count(*) as total from  opportunity o where id in (select sr.object_id from seal_relation sr where sr.object_type = 'MapasCulturais\Entities\Opportunity' and sr.status = 1 and sr.seal_id in ('1','2','3','4')) and o.parent_id is null");
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
            join 
                term t on t.id = tr.term_id 
            join 
                opportunity o on o.id = tr.object_id and o.status > 0
            where 
                tr.object_type = 'MapasCulturais\Entities\Opportunity' and 
                t.taxonomy = 'area'
            group by area_atuacao
        ");
        foreach ($results as $_key => $_value) {
            $total_oportunidade_por_area_atuacao[$_value['area_atuacao']] = $_value['total'];
        }

        // $total_espacos = $conn->fetchColumn("select count(*) as total from space");
        $total_espacos_publicados = $conn->fetchColumn("select count(*) as total from space where status > 0");
        $total_espacos_oficiais = $conn->fetchColumn("select count(*) as total from  space e where e.id in (select sr.object_id from seal_relation sr where sr.object_type = 'MapasCulturais\Entities\Space' and sr.status = 1 and sr.seal_id in (1,2,3,4)) and e.parent_id is null");
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
            join 
                term t on t.id = tr.term_id 
            join 
                space e on e.id = tr.object_id and e.status > 0
            where 
                tr.object_type = 'MapasCulturais\Entities\Space' and 
                t.taxonomy = 'area'
            group by area_atuacao
        ");
        foreach ($results as $_key => $_value) {
            $total_espaco_por_area_atuacao[$_value['area_atuacao']] = $_value['total'];
        }

        // $total_projetos = $conn->fetchColumn("select count(*) as total from project");
        $total_projetos_publicados = $conn->fetchColumn("select count(*) as total from project where status > 0");
        $total_projetos_oficiais = $conn->fetchColumn("select count(*) as total from  project e where e.id in (select sr.object_id from seal_relation sr where sr.object_type = 'MapasCulturais\Entities\Project' and sr.status = 1 and sr.seal_id in (1,2,3,4)) and e.parent_id is null");
        $total_projetos_rascunho = $conn->fetchColumn("select count(*) as total from project where status = 0");
        $total_projetos_lixeira = $conn->fetchColumn("select count(*) as total from project where status = -10");
        $total_projetos_arquivado = $conn->fetchColumn("select count(*) as total from project where status = -2");
        
        $total_inscricoes = $conn->fetchColumn("select count(*) as total from registration");
        
   

        $total_agentes = $conn->fetchColumn("SELECT count(*) as Total from agent WHERE status > 0");
        $total_agentes_oficiais = $conn->fetchColumn("select count(*) as total from  agent e where e.id in (select sr.object_id from seal_relation sr where sr.object_type = 'MapasCulturais\Entities\Agent' and sr.status = 1 and sr.seal_id in (1,2,3,4)) and e.parent_id is null");
        $total_agentes_inscritos_em_editais = $conn->fetchColumn("select count(*) as total from agent a where a.id in (select r.agent_id from registration r where status > 0) AND a.status > 0");
        $total_agentes_nunca_inscritos_em_editais = $conn->fetchColumn("select count(*) as total from agent a where a.id not in (select r.agent_id from registration r) AND a.status > 0 and a.type = 1");
        $total_agentes_rascunhos = $conn->fetchColumn("SELECT count(*) as Total from agent where status = 0");
        $total_agentes_lixeira = $conn->fetchColumn("SELECT count(*) as Total from agent where status = -10");
        $total_agentes_arquivados = $conn->fetchColumn("SELECT count(*) as Total from agent where status = -2");
        $total_agentes_individual = $conn->fetchColumn("select count(*) from agent a where type = 1 and status > 0");
        $total_agentes_coletivo = $conn->fetchColumn("select count(*) from agent a where type = 2 and status > 0");
        $total_agentes_idoso = $conn->fetchColumn("select count(*) from agent_meta am  join agent a on a.id = am.object_id where am.key = 'idoso' and value <> '0' and a.status > 0");
        $outras_comunidades_tradicionais = $conn->fetchColumn("select count(*) as total from agent_meta am where am.key = 'comunidadesTradicionalOutros'");

         ####### INSCRICOES ########

        // Total de inscrições suplente
        $total_inscricoes_nao_suplente = $conn->fetchColumn("
            select 
                count(distinct number) 
            from 
                registration r 
            where 
            r.status = 8
        ");

        // Total de inscrições inválidas
        $total_inscricoes_nao_invalidas = $conn->fetchColumn("
            select 
                count(distinct number) 
            from 
                registration r 
            where 
            r.status = 2
        ");

        // Total de inscrições não selecionadas
        $total_inscricoes_nao_selecionadas = $conn->fetchColumn("
            select 
                count(distinct number) 
            from 
                registration r 
            join 
                seal_relation sr on sr.object_id = r.opportunity_id and sr.object_type = 'MapasCulturais\Entities\Opportunity'
            where 
            r.status = 3 and
            sr.seal_id in (1,2,3,4)
        ");

        // Total de inscrições enviadas
        $total_inscricoes_enviadas = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r
            where 
                r.opportunity_id in (
                    select 
                        distinct o.id 
                    from 
                        opportunity o 
                    join 
                        seal_relation sr on sr.object_id = o.id and sr.object_type = 'MapasCulturais\Entities\Opportunity'
                    where 
                        o.parent_id is null and 
                        o.status > 0 and
                        sr.seal_id in (1,2,3,4)
                )
            and r.status > 0
        ");

         // Total de inscrições rascunho
         $total_inscricoes_pendentes = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r
            where 
                r.opportunity_id in (
                    select 
                        o.id 
                    from 
                        opportunity o 
                    join 
                        seal_relation sr on sr.object_id = o.id and sr.object_type = 'MapasCulturais\Entities\Opportunity'
                    where 
                        o.parent_id is null and 
                        o.status > 0 and
                        sr.seal_id in (1,2,3,4)
                )
            and r.status = 1
        ");

        // Total de inscrições rascunho
        $total_inscricoes_rascunho = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r
            where 
                r.opportunity_id in (
                    select 
                        o.id 
                    from 
                        opportunity o 
                    join 
                        seal_relation sr on sr.object_id = o.id and sr.object_type = 'MapasCulturais\Entities\Opportunity'
                    where 
                        o.parent_id is null and 
                        o.status > 0 and
                        sr.seal_id in (1,2,3,4)
                )
            and r.status = 0
        ");

        // Total de inscrições selecionadas
        $total_inscricoes_selecionadas = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r 
            where 
                r.opportunity_id 
            in (
                select o.id 
            from 
                opportunity o
            join 
                opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
            where
                o.parent_id in (
                    select 
                        sr.object_id 
                    from 
                        seal_relation sr 
                    where 
                        sr.seal_id 
                    in 
                        (1,2,3,4) and sr.object_type = 'MapasCulturais\Entities\Opportunity'
                )
            )  and 
            r.status = 10
        ");

         // Total de inscrições pendentes
         $total_inscricoes_pendentes_na_ultima_fase = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r 
            where 
                r.opportunity_id 
            in (
                select o.id 
            from 
                opportunity o
            join 
                opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
            where
                o.parent_id in (
                    select 
                        sr.object_id 
                    from 
                        seal_relation sr 
                    where 
                        sr.seal_id 
                    in 
                        (1,2,3,4) and sr.object_type = 'MapasCulturais\Entities\Opportunity'
                )
            )  and 
            r.status = 1
        ");
     
         // Total de inscrições pendentes
         $total_inscricoes_rascunhos_na_ultima_fase = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r 
            where 
                r.opportunity_id 
            in (
                select o.id 
            from 
                opportunity o
            join 
                opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
            where
                o.parent_id in (
                    select 
                        sr.object_id 
                    from 
                        seal_relation sr 
                    where 
                        sr.seal_id 
                    in 
                        (1,2,3,4) and sr.object_type = 'MapasCulturais\Entities\Opportunity'
                )
            )  and 
            r.status = 0
        ");

        ####### AGENTES########
        
        // Total de agentes NÃO CONTEMPLADOS em editais
        $total_agentes_nao_contemplados_em_editais = $conn->fetchColumn("
            select 
                count(*)
            from 
                agent a
            where 
                a.id 
            in (
                select 
                    r.agent_id
                from 
                    registration r 
                where 
                    r.opportunity_id 
                in (
                    select 
                        o.id 
                    from 
                        opportunity o
                    join 
                        opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                )  and 
                r.status in (2,3,8)
            ) 
        ");
        
        // Total de agentes CONTEMPLADOS em algum edital
        $total_agentes_contemplados_em_editais = $conn->fetchColumn("
            select 
                count(*)
            from 
                agent a
            where 
                a.id 
            in (
                select 
                    r.agent_id
                from 
                    registration r 
                where 
                    r.opportunity_id 
                in (
                    select 
                        o.id 
                    from 
                        opportunity o
                    join 
                        opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                )  and 
                r.status = 10
            )
        ");

        // Total de agentes com CPF e CNPJ (MEI)
        $total_agente_MEI = $conn->fetchColumn("
            select 
                count(*) as total 
            from 
                agent_meta am 
            join agent a on a.id = am.object_id 
            where 
                am.key = 'cpf' and 
                am.object_id 
            in (
                select 
                    am2.object_id  
                from 
                    agent_meta am2 
                where 
                    am2.key = 'cnpj' and 
                    am2.value is not null AND trim(am2.value) <> ''
            ) and 
            a.status > 0
        ");

        //Total de agentes somente com CNPJ (Pessoa Jurídica)
        $total_agente_pessoa_juridica = $conn->fetchColumn("
            select 
                count(*) as total 
            from 
                agent_meta am 
            join 
                agent a on a.id = am.object_id 
            where 
                am.key = 'cnpj' and 
                am.object_id 
            not in (
                select 
                    am2.object_id 
                from 
                    agent_meta am2 
                where 
                    am2.key = 'cpf' and 
                    (am2.value is null or trim(am2.value) = '')
            ) and 
            A.status > 0
        ");

        // Total de agentes somente com CPF (Pessoa Física)'
        $total_agente_pessoa_fisica = $conn->fetchColumn("
            select 
                count(*) as total 
            from 
                agent_meta am 
            join 
                agent a on a.id = am.object_id 
            where 
                am.key = 'cpf' and 
                am.object_id 
            not in (
                select 
                    am2.object_id 
                from 
                    agent_meta am2 
                where 
                    (am2.key = 'cnpj') and 
                   ( am2.value is null or trim(am2.value) = '')
            ) and 
            A.status > 0
        ");


        // Agentes individuais por area de atuação
        $results = $conn->fetchAll("
            select 
                t.term as area_atuacao,
                count(*) as total
            from 
                term_relation tr 
            join 
                term t on t.id = tr.term_id 
            join 
                agent a on a.id = tr.object_id and a.status > 0 and a.type = 1
            where 
                tr.object_type = 'MapasCulturais\Entities\Agent' and 
                t.taxonomy = 'area'
            group by area_atuacao
        ");
        foreach ($results as $_key => $_value) {
            $total_agentes_individual_por_area_atuacao[$_value['area_atuacao']] = $_value['total'];
        }


        // Agentes coletivo por area de atuação
        $results = $conn->fetchAll("
            select 
                t.term as area_atuacao,
                count(*) as total
            from 
                term_relation tr 
            join 
                term t on t.id = tr.term_id 
            join 
                agent a on a.id = tr.object_id and a.status > 0 and a.type = 2
            where 
                tr.object_type = 'MapasCulturais\Entities\Agent' and 
                t.taxonomy = 'area'
            group by area_atuacao
        ");
        foreach ($results as $_key => $_value) {
            $total_agentes_coletivo_por_area_atuacao[$_value['area_atuacao']] = $_value['total'];
        }

        // Agentes individuais por segmento cultural
        $results = $conn->fetchAll("
            select 
                t.term as segmento,
                count(*) as total
            from 
                term_relation tr 
            join 
                term t on t.id = tr.term_id 
            join 
                agent a on a.id = tr.object_id and a.status > 0 and a.type = 1
            where 
                tr.object_type = 'MapasCulturais\Entities\Agent' and 
                t.taxonomy = 'segmento'
            group by segmento
        ");
        foreach ($results as $_key => $_value) {
            $total_agentes_individual_por_segmento_cultural[$_value['segmento']] = $_value['total'];
        }

        // Agentes coletivos por segmento cultural
        $results = $conn->fetchAll("
            select 
                t.term as segmento,
                count(*) as total
            from 
                term_relation tr 
            join 
                term t on t.id = tr.term_id 
            join 
                agent a on a.id = tr.object_id and a.status > 0 and a.type = 2
            where 
                tr.object_type = 'MapasCulturais\Entities\Agent' and 
                t.taxonomy = 'segmento'
            group by segmento
        ");
        foreach ($results as $_key => $_value) {
            $total_agentes_coletivos_por_segmento_cultural[$_value['segmento']] = $_value['total'];
        }

        // Individuais sem CNPJ
        $total_agentes_individuais_com_cpf_sem_cnpj = $conn->fetchColumn("
            select 
                count(*) as total 
            from 
                agent a 
            where 
                a.type = 1 and 
                a.status > 0 and
                a.id not in (select am.object_id from agent_meta am where am.key = 'cnpj' and (am.value is null or trim(am.value) = ''))
        ");

        // Individuais com CNPJ
        $total_agentes_individuais_com_cpf_com_cnpj = $conn->fetchColumn("
            select 
                count(*) as total 
            from 
                agent a 
            where 
                a.type = 1 and 
                a.status > 0 and
                a.id in (select am.object_id from agent_meta am where am.key = 'cnpj' and am.value is not null)	
        ");

        // Coletivos sem CNPJ
        $total_agentes_coletivos_com_cpf_sem_cnpj = $conn->fetchColumn("
          select 
              count(*) as total 
          from 
              agent a 
          where 
              a.type = 2 and 
              a.status > 0 and
              a.id not in (select am.object_id from agent_meta am where am.key = 'cnpj' and trim(am.value) <> '')
      ");

        // Coletivos com CNPJ
        $total_agentes_coletivos_com_cpf_com_cnpj = $conn->fetchColumn("
          select 
              count(*) as total 
          from 
              agent a 
          where 
              a.type = 2 and 
              a.status > 0 and
              a.id in (select am.object_id from agent_meta am where am.key = 'cnpj' and am.value is not null)
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
                                join 
                                    agent a on a.id = am.object_id and a.status > 0
                                where 
                                    am.key = 'comunidadesTradicional'
                                group by 
                                    comunidade");
        foreach ($results as $_key => $_value) {
            $comunidadeTradicional[$_value['comunidade']] = $_value['total'];
        }

        // Pessoa com deficiencia
        $results = $conn->fetchAll("
            select 
                CASE 
                    WHEN am.value IS NULL OR am.value = '' THEN 'Não Informado'
                    else am.value
                END AS pessoaDeficiente,
                count(*) as total
            from 
                agent_meta am 
            join agent a on a.id = am.object_id and a.status > 0
            where 
                am.key = 'pessoaDeficiente'
            group by 
                pessoaDeficiente
        ");

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
                                    join agent a on am.object_id = a.id and a.status > 0
                                    
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
                            join agent a on am.object_id = a.id and a.status > 0
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

       

        $results = $conn->fetchAll("
            select 
                o.id, o.name 
            from 
                opportunity o 
            join 
                seal_relation sr on sr.object_id = o.id and sr.object_type = 'MapasCulturais\Entities\Opportunity'
            where 
                o.status > 0 and 
                o.parent_id is null and
                sr.seal_id in (1,2,3,4)
            order by o.name
        ");

        $inscricoes_por_oportunidade = [];
        foreach($results as $values) {
            $inscricoes_por_oportunidade["#{$values['id']} -- ". $values['name']] = [
                'Rascunho' =>  $conn->fetchOne("
                    select 
                        count(*) 
                    from 
                        registration r
                    
                    where 
                        r.opportunity_id = {$values['id']}
                    and r.status = 0
                "),
                'Enviadas' =>  $conn->fetchOne("
                    select 
                        count(*) 
                    from 
                        registration r
                    where 
                        r.opportunity_id = {$values['id']}
                    and r.status > 0
                "),
                'Selecionadas' =>  $conn->fetchOne("
                    select 
                        count(*) 
                    from 
                        registration r 
                    where 
                        r.opportunity_id 
                    in (
                        select 
                            o.id 
                        from 
                            opportunity o
                        join 
                            opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                        WHERE 
                            o.parent_id = {$values['id']}
                    )  and 
                    r.status = 10
                "),
                'Suplentes' =>  $conn->fetchOne("
                    select 
                        count(distinct number) 
                    from 
                        registration r 
                    where 
                        r.opportunity_id = {$values['id']} and 
                        r.status = 8
                "),
                'Não selecionadas' =>  $conn->fetchOne("
                    select 
                        count(distinct number) 
                    from 
                        registration r 
                    where 
                        r.opportunity_id = {$values['id']} and 
                        r.status = 3
                "),
                'Inválidas' =>  $conn->fetchOne("
                select 
                    count(distinct number) 
                from 
                    registration r 
                where 
                    r.opportunity_id = {$values['id']} and 
                    r.status = 2
                "),
                'Pendentes na última fase' =>  $conn->fetchOne("
                    select 
                        count(*) 
                    from 
                        registration r 
                    where 
                        r.opportunity_id 
                    in (
                        select 
                            o.id 
                        from 
                            opportunity o
                        join 
                            opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                        WHERE 
                            o.parent_id = {$values['id']}
                    )  and 
                    r.status = 1
                "),
                'Rascunhos na última fase' =>  $conn->fetchOne("
                select 
                    count(*) 
                from 
                    registration r 
                where 
                    r.opportunity_id 
                in (
                    select 
                        o.id 
                    from 
                        opportunity o
                    join 
                        opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                    WHERE 
                        o.parent_id = {$values['id']}
                )  and 
                r.status = 0
            "),
            ];
        }

        $results = $conn->fetchAll("
            select 
                id, name 
            from 
                opportunity o 
            where 
                o.status > 0 and 
                o.parent_id is null AND
                o.object_type = 'MapasCulturais\Entities\Project' AND
                o.object_id in (1274,1278)
            order by o.name
        ");
        

        $inscricoes_por_oportunidade_paulo_gustavo = [];
        foreach($results as $values) {
            $inscricoes_por_oportunidade_paulo_gustavo["#{$values['id']} -- ". $values['name']] = [
                'Rascunho' =>  $conn->fetchOne("
                    select 
                        count(*) 
                    from 
                        registration r
                    where 
                        r.opportunity_id = {$values['id']}
                    and r.status = 0
                "),
                'Enviadas' =>  $conn->fetchOne("
                    select 
                        count(*) 
                    from 
                        registration r
                    where 
                        r.opportunity_id = {$values['id']}
                    and r.status > 0
                "),
                'Selecionadas' =>  $conn->fetchOne("
                    select 
                        count(*) 
                    from 
                        registration r 
                    where 
                        r.opportunity_id 
                    in (
                        select 
                            o.id 
                        from 
                            opportunity o
                        join 
                            opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                        WHERE 
                            o.parent_id = {$values['id']}
                    )  and 
                    r.status = 10
                "),
                'Suplentes' =>  $conn->fetchOne("
                    select 
                        count(*) 
                    from 
                        registration r 
                    where 
                        r.opportunity_id 
                    in (
                        select 
                            o.id 
                        from 
                            opportunity o
                        join 
                            opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                        WHERE 
                            o.parent_id = {$values['id']}
                    )  and 
                    r.status = 8
                "),
                'Não selecionadas' =>  $conn->fetchOne("
                    select 
                        count(*) 
                    from 
                        registration r 
                    where 
                        r.opportunity_id 
                    in (
                        select 
                            o.id 
                        from 
                            opportunity o
                        join 
                            opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                        WHERE 
                            o.parent_id = {$values['id']}
                    )  and 
                    r.status = 3
                "),
                'Inválidas' =>  $conn->fetchOne("
                    select 
                        count(*) 
                    from 
                        registration r 
                    where 
                        r.opportunity_id 
                    in (
                        select 
                            o.id 
                        from 
                            opportunity o
                        join 
                            opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                        WHERE 
                            o.parent_id = {$values['id']}
                    )  and 
                    r.status = 2
                "),
                'Pendente na última fase' =>  $conn->fetchOne("
                    select 
                        count(*) 
                    from 
                        registration r 
                    where 
                        r.opportunity_id 
                    in (
                        select 
                            o.id 
                        from 
                            opportunity o
                        join 
                            opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                        WHERE 
                            o.parent_id = {$values['id']}
                    )  and 
                    r.status = 1
                "),
                'Rascunho na última fase' =>  $conn->fetchOne("
                    select 
                        count(*) 
                    from 
                        registration r 
                    where 
                        r.opportunity_id 
                    in (
                        select 
                            o.id 
                        from 
                            opportunity o
                        join 
                            opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                        WHERE 
                            o.parent_id = {$values['id']}
                    )  and 
                    r.status = 0
                "),
            ];
        }

        $sem_inscricao_enviada = [];
        foreach($results as $values) {
            $r = $conn->fetchOne("
                select 
                    count(*) 
                from 
                    registration r
                where 
                    r.opportunity_id = {$values['id']}
                and r.status > 0
            ");

            if($r == 0) {
                $sem_inscricao_enviada["#{$values['id']} -- ". $values['name']] = $r;
            }
           
        }

        // Total de inscrições suplente
        $total_inscricoes_suplente_paulo_gustavo = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r 
            where 
                r.opportunity_id 
            in (
                select 
                    o.id 
                from 
                    opportunity o
                join 
                    opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                where 
                    o.object_type = 'MapasCulturais\Entities\Project' AND
                    o.object_id in (1274,1278)
            )  and 
            r.status = 8
        ");


        // Total de inscrições inválidas
        $total_inscricoes_invalidas_paulo_gustavo = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r 
            where 
                r.opportunity_id 
            in (
                select 
                    o.id 
                from 
                    opportunity o
                join 
                    opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                where 
                    o.object_type = 'MapasCulturais\Entities\Project' AND
                    o.object_id in (1274,1278)
            )  and 
            r.status = 2
        ");

        // Total de inscrições não selecionadas
        $total_inscricoes_nao_selecionadas_paulo_gustavo = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r 
            where 
                r.opportunity_id 
            in (
                select 
                    o.id 
                from 
                    opportunity o
                join 
                    opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                where 
                    o.object_type = 'MapasCulturais\Entities\Project' AND
                    o.object_id in (1274,1278)
            )  and 
            r.status = 3
        ");

         // Total de inscrições pendentes
         $total_inscricoes_paulo_pendentes_paulo_gustavo = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r
            where 
                r.opportunity_id in (
                    select 
                        o.id 
                    from 
                        opportunity o 
                    where 
                        o.parent_id is null and 
                        o.status > 0 AND
                        o.object_type = 'MapasCulturais\Entities\Project' AND
                        o.object_id in (1274,1278)
                )
            and r.status = 1
        ");

        // Total de inscrições enviadas
        $total_inscricoes_enviadas_paulo_gustavo = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r
            where 
                r.opportunity_id in (
                    select 
                        o.id 
                    from 
                        opportunity o 
                    where 
                        o.parent_id is null and 
                        o.status > 0 AND
                        o.object_type = 'MapasCulturais\Entities\Project' AND
                        o.object_id in (1274,1278)
                )
            and r.status > 0
        ");

        // Total de inscrições rascunho
        $total_inscricoes_rascunho_paulo_gustavo = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r
            where 
                r.opportunity_id in (
                    select 
                        o.id 
                    from 
                        opportunity o 
                    where 
                        o.parent_id is null and 
                        o.status > 0 AND
                        o.object_type = 'MapasCulturais\Entities\Project' AND
                        o.object_id in (1274,1278)
                )
            and r.status = 0
        ");

        // Total de inscrições selecionadas
        $total_inscricoes_selecionadas_paulo_gustavo = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r 
            where 
                r.opportunity_id 
            in (
                select 
                    o.id 
                from 
                    opportunity o
                join 
                    opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                where 
                    o.object_type = 'MapasCulturais\Entities\Project' AND
                    o.object_id in (1274,1278)
            )  and 
            r.status = 10
        ");

        // Total de inscrições rascunhos na última fase
        $total_inscricoes_rascunhos_na_última_fase_paulo_gustavo = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r 
            where 
                r.opportunity_id 
            in (
                select 
                    o.id 
                from 
                    opportunity o
                join 
                    opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                where 
                    o.object_type = 'MapasCulturais\Entities\Project' AND
                    o.object_id in (1274,1278)
            )  and 
            r.status = 0
        ");

         // Total de inscrições rascunhos na última fase
         $total_inscricoes_pendente_na_última_fase_paulo_gustavo = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r 
            where 
                r.opportunity_id 
            in (
                select 
                    o.id 
                from 
                    opportunity o
                join 
                    opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                where 
                    o.object_type = 'MapasCulturais\Entities\Project' AND
                    o.object_id in (1274,1278)
            )  and 
            r.status = 1
        ");

        $_data = [
            'OPORTUNIDADES',
            // 'Total de oportunidades' => $total_oportunidades[0],
            'Total de oportunidades publicadas' => $total_oportunidades_publicados[0],
            'Total de oportunidades oficiais' => $total_oportunidades_oficiais[0],
            'Total de oportunidades rascunho' => $total_oportunidades_rascunho[0],
            'Total de oportunidades lixeira' => $total_oportunidades_lixeira[0],
            'Total de oportunidades arquivados' => $total_oportunidades_arquivado[0],
            'Total de oportunidades por área de interesse' => $total_oportunidade_por_area_atuacao,
            'Oportunidades sem inscrições enviadas' => $sem_inscricao_enviada,
            'ESPAÇOS',
            // 'Total de espacos' => $total_espacos[0],
            'Total de espacos publicados' => $total_espacos_publicados[0],
            'Total de espacos oficiais' => $total_espacos_oficiais[0],
            'Total de espacos rascunho' => $total_espacos_rascunho[0],
            'Total de espacos lixeira' => $total_espacos_lixeira[0],
            'Total de espacos arquivados' => $total_espacos_arquivado[0],
            'Total de espacos por área de atuação' => $total_espaco_por_area_atuacao,
            'PROJETOS',
            // 'Total de projetos' => $total_projetos[0],
            'Total de projetos publicados' => $total_projetos_publicados[0],
            'Total de projetos oficiais' => $total_projetos_oficiais[0],
            'Total de projetos rascunho' => $total_projetos_rascunho[0],
            'Total de projetos lixeira' => $total_projetos_lixeira[0],
            'Total de projetos arquivados' => $total_projetos_arquivado[0],
            'INSCRIÇÕES',
            'Total de inscrições enviadas' => $total_inscricoes_enviadas[0],
            'Total de inscrições rascunho' => $total_inscricoes_rascunho[0],
            'Total de inscrições pendentes' => $total_inscricoes_pendentes[0],
            'Total de inscrições selecionadas' => $total_inscricoes_selecionadas[0],
            'Total de inscrições não selecionadas' => $total_inscricoes_nao_selecionadas[0],
            'Total de inscrições inválidas' => $total_inscricoes_nao_invalidas[0],
            'Total de inscrições suplente' => $total_inscricoes_nao_suplente[0],
            'Total de inscricao pendentes na última fase' => $total_inscricoes_pendentes_na_ultima_fase[0],
            'Total de inscricao rascunhos na última fase' => $total_inscricoes_rascunhos_na_ultima_fase[0],
            'Inscrições por oportunidade' => $inscricoes_por_oportunidade,
            'INSCRIÇÕES - PAULO GUSTAVO',
            'Total de inscrições enviadas - PAULO GUSTAVO' => $total_inscricoes_enviadas_paulo_gustavo[0],
            'Total de inscrições rascunho - PAULO GUSTAVO' => $total_inscricoes_rascunho_paulo_gustavo[0],
            'Total de inscrições pendente - PAULO GUSTAVO' => $total_inscricoes_paulo_pendentes_paulo_gustavo[0],
            'Total de inscrições selecionadas - PAULO GUSTAVO' => $total_inscricoes_selecionadas_paulo_gustavo[0],

            'Total de inscrições rascunho na última fase - PAULO GUSTAVO' => $total_inscricoes_rascunhos_na_última_fase_paulo_gustavo[0],
            'Total de inscrições pendentes na última fase - PAULO GUSTAVO' => $total_inscricoes_pendente_na_última_fase_paulo_gustavo[0],

            'Total de inscrições não selecionadas - PAULO GUSTAVO' => $total_inscricoes_nao_selecionadas_paulo_gustavo[0],
            'Total de inscrições inválidas - PAULO GUSTAVO' => $total_inscricoes_invalidas_paulo_gustavo[0],
            'Total de inscrições suplente - PAULO GUSTAVO' => $total_inscricoes_suplente_paulo_gustavo[0],
            'Inscrições por oportunidade PAULO GUSTAVO' => $inscricoes_por_oportunidade_paulo_gustavo,
            'AGENTES',
            'Total de agentes publicados' => $total_agentes[0],
            'Total de agentes oficiais' => $total_agentes_oficiais[0],
            'Total de agentes COM inscrições' => $total_agentes_inscritos_em_editais[0],
            'Total de agentes SEM inscrições' => $total_agentes_nunca_inscritos_em_editais[0],
            'Total de agentes CONTEMPLADOS em algum edital' => $total_agentes_contemplados_em_editais[0],
            'Total de agentes NÃO CONTEMPLADOS em editais' => $total_agentes_nao_contemplados_em_editais[0],
            'Total de agentes rascunhos' => $total_agentes_rascunhos[0],
            'Total de agentes lixeira' => $total_agentes_lixeira[0],
            'Total de agentes arquivados' => $total_agentes_arquivados[0],

            'Total de agentes somente com CPF (Pessoa Física)' => $total_agente_pessoa_fisica[0],
            'Total de agentes somente com CNPJ (Pessoa Jurídica)' => $total_agente_pessoa_juridica[0],
            'Total de agentes com CPF e CNPJ (MEI)' => $total_agente_MEI[0],
            'Total de agentes coletivos SEM CNPJ' => $total_agentes_coletivos_com_cpf_sem_cnpj[0],

            'Total de agentes individual' => $total_agentes_individual[0],
            'Total de agentes coletivos' => $total_agentes_coletivo[0],
            // 'Total de agentes idoso' => $total_agentes_idoso[0],
            'Total de agentes comunidades tradicional' => $comunidadeTradicional,
            'Total de agentes outras comunidades tradicionais' => $outras_comunidades_tradicionais[0],
            // 'Total de agentes por pessoa com deficiência' => $pessoaDeficiente,
            'Total de agentes individual por faixa de idade' => $faixas_de_idade,
            // 'Total de agentes coletivo por tempo de fundação' => $tempo_funcao,
            // 'Total de agentes individuais SEM CNPJ' => $total_agentes_individuais_com_cpf_sem_cnpj[0],
            // 'Total de agentes individuais COM CNPJ' => $total_agentes_individuais_com_cpf_com_cnpj[0],
            // 'Total de agentes coletivos COM CNPJ' => $total_agentes_coletivos_com_cpf_com_cnpj[0],
            'Total de agentes individual por área de atuação' => $total_agentes_individual_por_area_atuacao,
            'Total de agentes coletivo por área de atuação' => $total_agentes_coletivo_por_area_atuacao,
            'Total de agentes individual por segmento cultural' => $total_agentes_individual_por_segmento_cultural,
            // 'Total de agentes coletivos por segmento cultural' => $total_agentes_coletivos_por_segmento_cultural,
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
                        trim(unaccent(lower(CASE 
                            WHEN am.value IS NULL OR am.value = '' THEN 'Não Informado' 
                            ELSE am.value 
                        END))) AS normalized_value
                    FROM 
                        agent_meta am 
                    JOIN agent a on a.id = am.object_id and a.status > 0
                    WHERE 
                        am.key = '{$data->value}' AND
                        (am.value IS NOT NULL OR trim(am.value) = '') 
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

    public function buildQuery($values, $wheres = [], $joins = [])
    {
        $showPquery = $values['showQuery'] ?? null;
        $select = $values['select'] ?? "count(distinct(id))";
        $from = $values['from'];
        $group = $values['group'] ?: "";

        if (($join = $values['join'] ?? []) || $joins) {
            $where = array_merge($join, $joins);
            $join = implode("\n                    ", $join);
            $join = "{$join}";
        }

        if (($where = $values['where'] ?? []) || $wheres) {
            $where = array_merge($where, $wheres);
            $where = implode(") AND (", $where);
            $where = "({$where})";
        }
        
        $join = $join ?: "";
        $where = $where ?: "";


        $sql = "
                SELECT 
                    {$select}
                FROM
                    {$from}
                    {$join}
                WHERE
                    {$where}
                    {$group}
            ";
        
        if($showPquery) {
            dump($sql);
            exit;
        }

        return $sql;
    }


    public function GET_querysv2()
    {
        $this->requireAuthentication();

        $app = App::i();

        if (!$app->user->is('admin')) {
            return;
        }

        $em = $app->em;
        $conn = $em->getConnection();

        $sessions = [
            "AGENTES" => [
                [
                    'label' => 'Total de usuários',
                    'select' => "count(distinct(u.id))",
                    'from' => "usr u",
                    'join' => [
                        'join agent a on a.id = u.profile_id'
                    ],
                    'where' => [
                        'u.status > 0',
                    ],
                ],
                [
                    // Existem agentes coletivos contemplados editais e agentes em rascunho contemplados em editais. Por isso o WHERE esta como esta
                    'label' => 'Total de agentes publicados',
                    'from' => "agent a",
                    'where' => [
                        'a.status > 0',
                    ],
                ],
                [
                    'label' => 'Total de agentes individuais',
                    'from' => "agent a",
                    'where' => [
                        'a.type = 1',
                        'a.status > 0'
                    ],
                ],
                [
                    'label' => 'Total de agentes coletivos',
                    'from' => "agent a",
                    'where' => [
                        'a.type = 2',
                        'a.status > 0'
                    ],
                ],
                [
                    'select' => 'count(distinct(a.id)) as total',
                    'label' => 'Total de agentes individuais com CPF',
                    'from' => "agent a",
                    'join' => [
                        "join agent_meta am on am.object_id = a.id and am.key = 'cpf' and trim(am.value) <> ''"
                    ],
                    'where' => [
                        'a.status > 0',
                        'a.type = 1'
                    ],
                ],
                [
                    'select' => 'count(distinct(a.id)) as total',
                    'label' => "Total de agentes individuais com CNPJ (MEI)",
                    'from' => "agent a",
                    'join' => [
                        "join agent_meta cnpj on cnpj.object_id = a.id and cnpj.key = 'cnpj' and trim(cnpj.value) <> ''"
                    ],
                    'where' => [
                        'a.status > 0',
                        'a.type = 1'
                    ],
                ],
                [
                    'label' => 'Total de agentes coletivos com CNPJ',
                    'select' => 'count(distinct(a.id)) as total',
                    'from' => "agent a",
                    'where' => [
                        "a.id in (select cnpj.object_id from agent_meta cnpj where cnpj.key = 'cnpj' and trim(cnpj.value) <> '')",
                        'a.status > 0',
                        'a.type = 2'
                    ],
                ],
                [
                    'label' => 'Total de agentes coletivos sem CNPJ',
                    'from' => "agent a",
                    'where' => [
                        "a.id not in (select cnpj.object_id from agent_meta cnpj where cnpj.key = 'cnpj' and trim(cnpj.value) <> '')",
                        'a.type = 2',
                        'a.status > 0'
                    ],
                ],
                [
                    'label' => 'Total de agentes COM inscrições enviadas',
                    'from' => "agent a",
                    'where' => [
                        'a.id in (select r.agent_id from registration r where status > 0)',
                        'a.type = 1'
                    ],
                ],
                [
                    'label' => 'Total de agentes CONTEMPLADOS em algum edital',
                    'from' => "agent a",
                    'where' => [
                        "a.id in (
                            select agent_id from registration where opportunity_id in (
                                select object_id from opportunity_meta where key = 'isLastPhase'
                            )  and status = 10
                        )",
                        'a.type = 1'
                    ],
                ],
                [
                    'label' => 'Total de agentes NÃO CONTEMPLADOS em editais',
                    'from' => "agent a",
                    'where' => [
                        "a.id not in (
                            select agent_id from registration where opportunity_id in (
                                select object_id from opportunity_meta where key = 'isLastPhase'
                            )  and status = 10
                        )",
                        'a.type = 1',
                        'a.status > 0'
                    ],
                ],
                [
                    'label' => 'Total de agentes com inscrições enviadas mas NÃO CONTEMPLADOS em editais',
                    'from' => "agent a",
                    'where' => [
                        "a.id not in (
                            select agent_id from registration where opportunity_id in (
                                select object_id from opportunity_meta where key = 'isLastPhase'
                            )  and status = 10
                        )",
                        'a.id in (select r.agent_id from registration r where status > 0)',
                        'type = 1'
                    ],
                ],
                [
                    'label' => 'Total de agentes por comunidade tradicional',
                    'select' => "
                        CASE 
                            WHEN am.value IS NULL OR am.value = '' THEN 'Não Informado'
                            else am.value
                        END AS alias,
                        count(*) as total
                    ",
                    'from' => "agent a",
                    'join' => [
                        "join agent_meta am on am.object_id = a.id and am.key = 'comunidadesTradicional'"
                    ],
                    'where' => [
                        'a.status > 0'
                    ],
                    'group' => "group by alias",
                    'fetch' => "fetchAll"
                ],
                [
                    'label' => 'Total de agentes inviduais por area de atuação',
                    'select' => "
                        t.term as alias,
                        count(*) as total
                    ",
                    'from' => "term_relation tr",
                    'join' => [
                        "join term t on t.id = tr.term_id",
                        "join agent a on a.id = tr.object_id and a.status > 0 and a.type = 1"
                    ],
                    'where' => [
                        "tr.object_type = 'MapasCulturais\Entities\Agent'",
                        "t.taxonomy = 'area'",
                    ],
                    'group' => "group by alias",
                    'fetch' => "fetchAll"
                ],
                [
                    'label' => 'Total de agentes coletivos por area de atuação',
                    'select' => "
                        t.term as alias,
                        count(*) as total
                    ",
                    'from' => "term_relation tr",
                    'join' => [
                        "join term t on t.id = tr.term_id",
                        "join agent a on a.id = tr.object_id and a.status > 0 and a.type = 2"
                    ],
                    'where' => [
                        "tr.object_type = 'MapasCulturais\Entities\Agent'",
                        "t.taxonomy = 'area'",
                    ],
                    'group' => "group by alias",
                    'fetch' => "fetchAll"
                ],
                [
                    'label' => 'Total de agentes individual por segmento cultural',
                    'select' => "
                        t.term as alias,
                        count(*) as total
                    ",
                    'from' => "term_relation tr",
                    'join' => [
                        "join term t on t.id = tr.term_id",
                        "join agent a on a.id = tr.object_id and a.status > 0 and a.type = 1"
                    ],
                    'where' => [
                        "tr.object_type = 'MapasCulturais\Entities\Agent'",
                        "t.taxonomy = 'segmento'",
                    ],
                    'group' => "group by alias",
                    'fetch' => "fetchAll"
                ],
                [
                    'label' => 'Total de agentes por raça',
                    'select' => "
                        CASE 
                            WHEN am.value IS NULL OR am.value = '' THEN 'Não Informado'
                            else am.value
                        END AS alias,
                        count(*) as total
                    ",
                    'from' => "agent a",
                    'join' => [
                        "join agent_meta am on am.object_id = a.id and am.key = 'raca'"
                    ],
                    'where' => [
                        'a.status > 0'
                    ],
                    'group' => "group by alias",
                    'fetch' => "fetchAll"
                ],
                [
                    'label' => 'Total de agentes por genero',
                    'select' => "
                        CASE 
                            WHEN am.value IS NULL OR am.value = '' THEN 'Não Informado'
                            else am.value
                        END AS alias,
                        count(*) as total
                    ",
                    'from' => "agent a",
                    'join' => [
                        "join agent_meta am on am.object_id = a.id and am.key = 'genero'"
                    ],
                    'where' => [
                        'a.status > 0'
                    ],
                    'group' => "group by alias",
                    'fetch' => "fetchAll"
                ],
                [
                    'label' => 'Total de agentes por escolaridade',
                    'select' => "
                        CASE 
                            WHEN am.value IS NULL OR am.value = '' THEN 'Não Informado'
                            else am.value
                        END AS alias,
                        count(*) as total
                    ",
                    'from' => "agent a",
                    'join' => [
                        "join agent_meta am on am.object_id = a.id and am.key = 'escolaridade'"
                    ],
                    'where' => [
                        'a.status > 0'
                    ],
                    'group' => "group by alias",
                    'fetch' => "fetchAll"
                ],
                [
                    'label' => 'Total de agentes individuais por RI',
                    'select' => "
                        CASE 
                            WHEN am.value IS NULL OR am.value = '' THEN 'Não Informado'
                            else am.value
                        END AS alias,
                        count(*) as total
                    ",
                    'from' => "agent a",
                    'join' => [
                        "join agent_meta am on am.object_id = a.id and am.key = 'geoRI'"
                    ],
                    'where' => [
                        'a.status > 0',
                        'a.type = 1'
                    ],
                    'group' => "group by alias",
                    'fetch' => "fetchAll"
                ],
                [
                    'label' => 'Total de agentes individuais por faixa de idade',
                    'result' =>  $this->agentByAgeGroup(),
                ]
            ],
            "INSCRIÇÕES" => [
                [
                    'label' => "Enviadas",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id'
                    ],
                    'where' => [
                        "r.opportunity_id in (
                            select 
                                distinct o.id 
                            from 
                                opportunity o 
                            join 
                                seal_relation sr on sr.object_id = o.id and sr.object_type = 'MapasCulturais\Entities\Opportunity'
                            where 
                                o.parent_id is null and 
                                o.status > 0 and
                                sr.seal_id in (1,2,3,4)
                        )",
                        "r.status > 0"
                    ],
                ],
                [
                    'label' => "Rascunho",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id'
                    ],
                    'where' => [
                        "r.opportunity_id in (
                            select 
                                distinct o.id 
                            from 
                                opportunity o 
                            join 
                                seal_relation sr on sr.object_id = o.id and sr.object_type = 'MapasCulturais\Entities\Opportunity'
                            where 
                                o.parent_id is null and 
                                o.status > 0 and
                                sr.seal_id in (1,2,3,4)
                        )",
                        "r.status = 0"
                    ],
                ],
                [
                    'label' => "Pendente",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id'
                    ],
                    'where' => [
                        "r.opportunity_id in (
                            select 
                                distinct o.id 
                            from 
                                opportunity o 
                            join 
                                seal_relation sr on sr.object_id = o.id and sr.object_type = 'MapasCulturais\Entities\Opportunity'
                            where 
                                o.parent_id is null and 
                                o.status > 0 and
                                sr.seal_id in (1,2,3,4)
                        )",
                        "r.status = 1"
                    ],
                ],
                [
                    'label' => "selecionadas",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id'
                    ],
                    'where' => [
                        "r.opportunity_id in 
                        (
                            select o.id 
                            from 
                                opportunity o
                            join 
                                opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                            where
                                o.parent_id in (
                                    select 
                                        sr.object_id 
                                    from 
                                        seal_relation sr 
                                    where 
                                        sr.seal_id in (1,2,3,4) and sr.object_type = 'MapasCulturais\Entities\Opportunity'
                                )
                        )",
                        "r.status = 10"
                    ],
                ],
                [
                    'label' => "Não selecionadas",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id'
                    ],
                    'where' => [
                        "r.opportunity_id in 
                        (
                            select o.id 
                            from 
                                opportunity o
                            join 
                                opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                            where
                                o.parent_id in (
                                    select 
                                        sr.object_id 
                                    from 
                                        seal_relation sr 
                                    where 
                                        sr.seal_id in (1,2,3,4) and sr.object_type = 'MapasCulturais\Entities\Opportunity'
                                )
                        )",
                        "r.status = 3"
                    ],
                ],
                [
                    'label' => "Inválidas",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id'
                    ],
                    'where' => [
                        "r.opportunity_id in 
                        (
                            select o.id 
                            from 
                                opportunity o
                            join 
                                opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                            where
                                o.parent_id in (
                                    select 
                                        sr.object_id 
                                    from 
                                        seal_relation sr 
                                    where 
                                        sr.seal_id in (1,2,3,4) and sr.object_type = 'MapasCulturais\Entities\Opportunity'
                                )
                        )",
                        "r.status = 2"
                    ],
                ],
                [
                    'label' => "Suplentes",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id'
                    ],
                    'where' => [
                        "r.opportunity_id in 
                        (
                            select o.id 
                            from 
                                opportunity o
                            join 
                                opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                            where
                                o.parent_id in (
                                    select 
                                        sr.object_id 
                                    from 
                                        seal_relation sr 
                                    where 
                                        sr.seal_id in (1,2,3,4) and sr.object_type = 'MapasCulturais\Entities\Opportunity'
                                )
                        )",
                        "r.status = 8"
                    ],
                ],
            ],
            "INSCRIÇÕES PAULO GUSTAVO" => [
                [
                    'label' => "Enviadas",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id',
                        "join opportunity o on o.id = r.opportunity_id"
                    ],
                    'where' => [
                        "o.parent_id is null",
                        "o.status > 0",
                        "o.object_type = 'MapasCulturais\Entities\Project'",
                        "o.object_id in (1274,1278)",
                        "r.status > 0"
                    ],
                ],
                [
                    'label' => "Rascunho",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id',
                        "join opportunity o on o.id = r.opportunity_id"
                    ],
                    'where' => [
                        "o.parent_id is null",
                        "o.status > 0",
                        "o.object_type = 'MapasCulturais\Entities\Project'",
                        "o.object_id in (1274,1278)",
                        "r.status = 0"
                    ],
                ],
                [
                    'label' => "Pendentes",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id',
                        "join opportunity o on o.id = r.opportunity_id"
                    ],
                    'where' => [
                        "o.parent_id is null",
                        "o.status > 0",
                        "o.object_type = 'MapasCulturais\Entities\Project'",
                        "o.object_id in (1274,1278)",
                        "r.status = 1"
                    ],
                ],
                [
                    'label' => "Selecionadas",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id',
                        "join opportunity o on o.id = r.opportunity_id",
                        "join opportunity_meta om on om.object_id = o.id AND om.key = 'isLastPhase'"
                    ],
                    'where' => [
                        "o.status in (1,-1)",
                        "o.object_type = 'MapasCulturais\Entities\Project'",
                        "o.object_id in (1274,1278)",
                        "r.status = 10"
                    ],
                ],
                [
                    'label' => "Não selecionadas",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id',
                        "join opportunity o on o.id = r.opportunity_id",
                        "join opportunity_meta om on om.object_id = o.id AND om.key = 'isLastPhase'"
                    ],
                    'where' => [
                        "o.status in (1,-1)",
                        "o.object_type = 'MapasCulturais\Entities\Project'",
                        "o.object_id in (1274,1278)",
                        "r.status = 3"
                    ],
                ],
                [
                    'label' => "Não selecionadas",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id',
                        "join opportunity o on o.id = r.opportunity_id",
                        "join opportunity_meta om on om.object_id = o.id AND om.key = 'isLastPhase'"
                    ],
                    'where' => [
                        "o.status in (1,-1)",
                        "o.object_type = 'MapasCulturais\Entities\Project'",
                        "o.object_id in (1274,1278)",
                        "r.status = 3"
                    ],
                ],
                [
                    'label' => "Inválidas",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id',
                        "join opportunity o on o.id = r.opportunity_id",
                        "join opportunity_meta om on om.object_id = o.id AND om.key = 'isLastPhase'"
                    ],
                    'where' => [
                        "o.status in (1,-1)",
                        "o.object_type = 'MapasCulturais\Entities\Project'",
                        "o.object_id in (1274,1278)",
                        "r.status = 2"
                    ],
                ],
                [
                    'label' => "Supĺentes",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id',
                        "join opportunity o on o.id = r.opportunity_id",
                        "join opportunity_meta om on om.object_id = o.id AND om.key = 'isLastPhase'"
                    ],
                    'where' => [
                        "o.status in (1,-1)",
                        "o.object_type = 'MapasCulturais\Entities\Project'",
                        "o.object_id in (1274,1278)",
                        "r.status = 8"
                    ],
                ],
            ],
        ];

        $result = [];
        foreach ($sessions as $session => $queries) {
            if ($session === "INSCRIÇÕES PAULO GUSTAVO") {
                continue;
            }

            $result[] = $session;
            foreach ($queries as $values) {
                if (!isset($values['result'])) {
                    $fetch = $values['fetch'] ?? "fetchOne";
                    $sql = $this->buildQuery($values);

                    $label = $values['label'];

                    if ($fetch == "fetchAll") {
                        $results = $conn->$fetch($sql);

                        foreach ($results as $_key => $_value) {
                            $result[$label][$_value['alias']] = $_value['total'];
                        }
                    } else {
                        $result[$label] = $conn->$fetch($sql);
                    }
                } else {
                    $label = $values['label'];
                    $result[$label] = $values['result'];
                }
            }
        }

        // Print dos dados
        $this->output("Segmentação Global", $result);

        $this->output("Segmentação Paulo Gustavo por oportunidade", $this->segmentadoPauloGustavo($sessions));

        $this->output("Segmentação por RI", $this->segmentacaoRI($sessions));

        $this->output("Segmentação Paulo Gustavo por RI", $this->segmentadoPauloGustavoPorRI($sessions));
    }

    public function output($label, $data)
    {
        if(isset($_GET['csv'])) {
            echo "<div style='white-space: pre-line;'>";
            echo "{$label},\n";
            echo ",\n";
            $result = print_r($data, true);
            $result =  str_replace(["[", "]"], '"', $result);
            $result =  str_replace([" => ", ")"], ',', $result);
            $result =  str_replace(["Array", "("], '', $result);
            echo $result;
            echo ",\n";
            echo ",\n";
            echo "</div>";
        }else {
            echo "<h1>{$label}</h1>";
            dump($data);
        }
    }

    
    public function agentByAgeGroup()
    {
        $app = App::i();
        $em = $app->em;
        $conn = $em->getConnection();

        $results = $conn->fetchAll("
            SELECT 
                CONCAT(FLOOR((idade::numeric / 10)) * 10, ' - ', FLOOR((idade::numeric / 10) + 1) * 10 - 1) AS faixa_idade,
                COUNT(*) AS total
            FROM (
                SELECT 
                    CASE 
                        WHEN EXTRACT(YEAR FROM age(current_date, am.value::date)) < 1 THEN '0' 
                        WHEN EXTRACT(YEAR FROM age(current_date, am.value::date)) > 100 THEN '100' 
                        ELSE EXTRACT(YEAR FROM age(current_date, am.value::date))::text 
                    END AS idade
                FROM 
                    agent_meta am 
                    JOIN agent a ON am.object_id = a.id AND a.status > 0
                    
                WHERE 
                    am.key = 'dataDeNascimento' AND
                    am.value <> '' AND
                    a.type = 1 AND
                    EXTRACT(YEAR FROM age(current_date, am.value::date)) >= 18 AND
                    EXTRACT(YEAR FROM age(current_date, am.value::date)) <= 110
            ) AS subquery
            GROUP BY 
                CONCAT(FLOOR((idade::numeric / 10)) * 10, ' - ', FLOOR((idade::numeric / 10) + 1) * 10 - 1)
            ORDER BY
                CASE 
                    WHEN CONCAT(FLOOR((idade::numeric / 10)) * 10, ' - ', FLOOR((idade::numeric / 10) + 1) * 10 - 1) = '100 - 109' THEN 1
                    ELSE 0
                END,
                CONCAT(FLOOR((idade::numeric / 10)) * 10, ' - ', FLOOR((idade::numeric / 10) + 1) * 10 - 1);
    
        ");

        $result = [];
        foreach ($results as $_key => $_value) {
            $result[$_value['faixa_idade']] = $_value['total'];
        }

        return $result;
    }


    public function segmentacaoRI($sessions)
    {
        $app = App::i();
        $em = $app->em;
        $conn = $em->getConnection();

        $ris = $conn->fetchAll("SELECT cod,name FROM geo_division WHERE type = 'RI'");
        $ri_results = [];
        foreach ($ris as $ri) {
            $ri_result = [];
            foreach ($sessions as $session => $queries) {
                if ($session === "INSCRIÇÕES PAULO GUSTAVO") {
                    continue;
                }
                $ri_result[] = $session;
                foreach ($queries as $values) {
                    if (!isset($values['result'])) {
                        $fetch = $values['fetch'] ?? "fetchOne";
                        $sql = $this->buildQuery($values, wheres: ["a.id in(select object_id from agent_meta where key = 'geoRI' and value = '{$ri['cod']}')"]);

                        $label = $values['label'];

                        if ($fetch == "fetchAll") {
                            $results = $conn->$fetch($sql);

                            foreach ($results as $_key => $_value) {
                                $ri_result[$label][$_value['alias']] = $_value['total'];
                            }
                        } else {
                            $app->log->debug($sql);
                            $ri_result[$label] = $conn->$fetch($sql);
                        }
                    } else {
                        $label = $values['label'];
                        $result[$label] = $values['result'];
                    }
                }
            }
            $ri_results[$ri['name']] = $ri_result;
        }

        return $ri_results;
    }

    public function segmentadoPauloGustavo($sessions)
    {
        $app = App::i();
        $em = $app->em;
        $conn = $em->getConnection();
        $opps_paulo_gustavo = $conn->fetchAll("SELECT o.id,o.name FROM opportunity o WHERE o.parent_id is null AND o.object_type = 'MapasCulturais\Entities\Project' AND o.object_id in (1274,1278)");

        $app = App::i();
        $em = $app->em;
        $conn = $em->getConnection();

        $opp_results = [];
        foreach ($opps_paulo_gustavo as $opp) {
            $results = [];
            foreach ($sessions as $session => $queries) {
                if ($session !== "INSCRIÇÕES PAULO GUSTAVO") {
                    continue;
                }

                foreach ($queries as $values) {
                    if (!isset($values['result'])) {
                        $fetch = $values['fetch'] ?? "fetchOne";
                        $complement = "o.id = {$opp['id']}";
                        if (in_array("o.status in (1,-1)", array_values($values['where']))) {
                            $complement = "o.parent_id = {$opp['id']}";
                        }
                        $sql = $this->buildQuery($values, wheres: [$complement]);

                        $label = $values['label'];

                        if ($fetch == "fetchAll") {
                            $results = $conn->$fetch($sql);

                            foreach ($results as $_key => $_value) {
                                $results[$label][$_value['alias']] = $_value['total'];
                            }
                        } else {
                            $app->log->debug($sql);
                            $results[$label] = $conn->$fetch($sql);
                        }
                    } else {
                        $label = $values['label'];
                        $result[$label] = $values['result'];
                    }
                }
            }
            $opp_results["#{$opp['id']} - " . $opp['name']] = $results;
        }

        return $opp_results;
    }

    public function segmentadoPauloGustavoPorRI($sessions)
    {
        $app = App::i();
        $em = $app->em;
        $conn = $em->getConnection();
        $opps_paulo_gustavo = $conn->fetchAll("SELECT o.id,o.name FROM opportunity o WHERE o.parent_id is null AND o.object_type = 'MapasCulturais\Entities\Project' AND o.object_id in (1274,1278)");
        $ris = $conn->fetchAll("SELECT cod,name FROM geo_division WHERE type = 'RI'");

        $app = App::i();
        $em = $app->em;
        $conn = $em->getConnection();

        $opp_results = [];
        foreach ($ris as $ri) {
            foreach ($opps_paulo_gustavo as $opp) {
                    $fetch = $values['fetch'] ?? "fetchOne";
                    $ri_result = [];
                    foreach ($sessions as $session => $queries) {
                        if ($session !== "INSCRIÇÕES PAULO GUSTAVO") {
                            continue;
                        }

                        foreach ($queries as $values) {
                            if (!isset($values['result'])) {
                                $complement = "o.id = {$opp['id']}";
                                if (in_array("o.status in (1,-1)", array_values($values['where']))) {
                                    $complement = "o.parent_id = {$opp['id']}";
                                }
                                $sql = $this->buildQuery($values, wheres: ["a.id in(select object_id from agent_meta where key = 'geoRI' and value = '{$ri['cod']}')", $complement]);
                                $label = $values['label'];
                                $ri_result[$label] = $conn->$fetch($sql);
                            } else {
                                $label = $values['label'];
                                $ri_result[$label] = $values['result'];
                            }
                        }
                    }
                $opp_results[$ri['name']]["#{$opp['id']} - " . $opp['name']] = $ri_result;
            }
        }

        return $opp_results;
    }
    
}
