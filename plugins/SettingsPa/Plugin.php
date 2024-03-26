<?php

namespace SettingsPa;

use MapasCulturais\i;
use MapasCulturais\App;
use SettingsPa\Controller;
use MapasCulturais\Entities\Agent;

class Plugin extends \MapasCulturais\Plugin
{
    function __construct($config = [])
    {
        $config += [
            'required_fields' => [],
            // Título que aparece na homr
            "title_home" => env("HC_TEXT_HOME", ""),

            // Imagens que aparece na home. Preencher como um array Ex.: ["homeContent/img/img1.png", "homeContent/img/img2.png"]
            "images_home" => env("HC_IMAGES_HOME", ""),

            // Tamanho da imagem em percentual %
            "images_size_home" => env("HC_IMAGES_SIZE_HOME", null),

            // Texto que aparece na home
            "text_home" => env("HC_TEXT_HOME", ""),

            // Texto do botão de ação que aparece na home
            "action_home_text" => env("HC_TEXT_ACTIONS_HOME", ""),

            "action_home_link" => env("HC_LINK_ACTIONS_HOME", "#"),
        ];

        parent::__construct($config);
    }


    public function _init()
    {
        $app = App::i();

        $self = $this;

        $app->hook('template(<<*>>.<<*>>.main-footer-logo):before', function () use($app) {
            /** @var \MapasCulturais\Themes\BaseV2\Theme $this */

            $this->part('pa-footer-support');
        });

        $app->hook("registrationFieldTypes.saveToEntity", function($entity_field, $value) use ($app){
            if($entity_field == '@terms:segmento') {
                $this->terms['segmento'] = $value;
            }
        });

        $app->hook("registrationFieldTypes.fetchFromEntity", function($entity_field, &$value){
            if($entity_field == '@terms:segmento') {
                $value = $this->terms['segmento'];
            }
        });

        $app->hook("registrationFieldTypes--agent-<<owner|collective>>-field-config-fields_labels", function(&$fields_labels){
            $fields_labels['@terms:segmento'] = " " . i::__('Segmento cultural');
        });

        $app->hook("template(embedtools.formbuilder.registrationFieldTypes--agent-<<owner|collective>>-field-config):after", function($agent_fields){
            $this->part('registration-fields/agent-fields-config', ['agent_fields' => $agent_fields]);
        });

        $app->hook("template(embedtools.registrationform.registrationFieldTypes--agent-<<owner|collective>>-field):after", function(){
            $this->part('registration-fields/agent-fields-form');
        });

        $app->hook("template(agent.<<edit|single>>.header-content):after",function() use ($app){
            /** @var Theme $this */
            $this->addTaxonoyTermsToJs("segmento");
        });

        /**
         * Insere conteúdo na HOME
         */
        $app->hook('template(site.index.home-search):end', function () use ($self) {
            /** @var Theme $this */
            $this->part('HomeContent/home-search', ["config" => $self->config]);
        });

        $app->hook("app.register:after", function () use ($self){
            $metadata = $this->getRegisteredMetadata('MapasCulturais\Entities\Agent');
            foreach($self->config['agent_required_fields'] as $field => $bool){
                $metadata[$field]->is_required = $bool;
            }
        }, 10000);

        $app->hook("<<GET|POST|PATCH|PUT>>(agent.<<*>>):before", function () use ($app, $self) {
            $entity = $this->requestedEntity;

            if($entity && $entity->type && $entity->type->id == 1){
                $metadata = $app->getRegisteredMetadata('MapasCulturais\Entities\Agent');
                foreach($self->config['agent1_required_fields'] as $field => $bool){
                    $metadata[$field]->is_required = $bool;
                }
            }
           
        }, 10000);

        $this->reopenEvaluations();
    }

    public function register()
    {
        $app = App::i();

        $this->registerTaxonomies();

        $app->registerController('pasettings', Controller::class);
    }

    public function registerTaxonomies()
    {
        $app = App::i();

        $def = new \MapasCulturais\Definitions\Taxonomy(55, 'segmento','Segmento cultural',  [
                "Artes Visuais",
                "Artesanato",
                "Audiovisual",
                "Circo",
                "Cultura Alimentar",
                "Cultura Digital",
                "Cultura Gospel",
                "Cultura Urbana e Periférica",
                "Culturas Afro-Brasileiras",
                "Culturas Indígenas",
                "Culturas Populares",
                "Dança",
                "Livro e Leitura",
                "Moda e Design",
                "Museus e Memoriais de Base Comunitária",
                "Música",
                "Outros",
                "Patrimônio Cultural Imaterial",
                "Patrimônio Cultural Material",
                "Pontos e Pontões de Cultura",
                "Teatro",
            ]
        );

        $app->registerTaxonomy(Agent::class, $def);
        
    }

    /**
     * Função para reabrir as avaliações nos editais que tiveram impacto dos metadados duplicados
     */
    public function reopenEvaluations()
    {
        $app = App::i();

        $registrations = [
            '87' => [1008011510,861023019,704914517,1717721812,108611210,991305308,971781100,1724414325],
            '1637' => [291199732],
            '4962' => [954663694,1582265781,1300251898,387270181],
            '5733' => [221085162,636685825,514551935,182059864,184290889,10640057,1872717631,552105674,1140867149,236535641,1332036761,1939150034,2013943156],
            '7892' => [783040221,1939884915,383590827,1170889801,1505459223,983371632,1790198021,1047539705,496122907,184450529],
            '9895' => [1031163772,284572583,1796409874,959139481,960893672,800993079],
            '13678' =>[128610330,1017004333],
            '14130' => [408228563,747404067,1837201618,2138799060],
            '18624' => [124727272],
            '18886' => [687490744,229865550,759198640,210570406,1083125640,1071656047,453272953,428547035,1345259666,1114704854],
            '19546' =>[309673971],
            '33833' => [351668311,701303611,1376607618,1199640769,391660313,736369415,549472815,896557016,1025247320,953898915],
            '34173' => [797775625,608919730,1391359232,1246497131,646513322],
            '34817' => [1646738742],
            '38268' => [1783587541,311123941],
            '38304' => [1662662302,1357006803],
            '38305' => [1357328120,976954874],
            '38361' => [1849496215,383730522,1054554717],
            '38457' => [228729046,44384654,200236745,1753046844],
            '39208' => [1107699065,1866490670,559498666,1969838661,18886874,474878676,1937520260],
            '39446' => [2141724330,1539336726,1640048134,1952807930],
            '40990' => [544111598],
            '41415' => [1746277807,305143588,1083461095,1981682714,980426742,1478767121],
            '42996' => [1124157450],
            '43216' => [1305091283,1797091793,322400796,63733180],
            '43217' => [666123438],
            '43221' => [2108249190,473370286],
            '43231' => [1088899784,70548689,2021153788,1325873682,2073045899],
            '43273' => [856433699],
            '43316' => [1008278251],
            '43484' => [1470050380,583290680],
            '43485' => [1996035420,1270966656,141980241,456807708,2035355550,1470050380,1858601531,2118086674,401545803,91245080,1194682188,1303125473,583290680,1299531874,167536865],
            '43489' => [91245080,141980241,583290680],
            '44028' => [2097959991],
            '44011' => [2097959991],
            '38964' => [2097959991],

        ];

        $app->hook("can(RegistrationEvaluation.modify)", function($user, &$can) use ($registrations){
            $regs = $registrations["$user->id"] ?? [];
            if(in_array($this->registration->id, $regs)){
                $can = true;
            }
        });

        $app->hook("can(Registration.evaluate)", function($user, &$can) use ($registrations){
            $regs = $registrations["$user->id"] ?? [];
            if(in_array($this->id, $regs)){
                $can = true;
            }
        });
    }
    
}
