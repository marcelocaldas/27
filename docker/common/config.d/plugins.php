<?php
$config_plugins = [
    'plugins' => [
        'MultipleLocalAuth',
        'AdminLoginAsUser',

        'MapasBlame' => [
            'namespace' => 'MapasBlame',
            'config' => [
                'request.logData.PATCH' => function ($data) {
                    return $data;
                },
            ]
        ],

        'SettingsPa' => [
            'namespace' => 'SettingsPa',
            'config' => [
                'agent_required_fields' => [
                    'emailPrivado' => true,
                    'telefone1' => true,
                    'En_Estado' => true,
                    'En_Municipio' => true,
                ],
                'agent1_required_fields' => [ // Obrigatoriedade dos Campos dos agentes individuais
                    'nomeCompleto' => true,
                    'genero' => true,
                    'cpf' => true,
                    'raca' => true,
                ],
                "title_home" => "",
                "images_home" => ["homeContent/img/edital_539.png"],
                "text_home" => "",
                "images_size_home" => "60%",
                "action_home_text" => 'Acessar edital',
                "action_home_link" => 'https://mapacultural.pa.gov.br/oportunidade/539/',
            ]
        ],
        
        "MetadataKeyword" => [
            "namespace" => "MetadataKeyword",
            "config" => [
                'location' => ['En_Municipio', 'En_Nome_Logradouro', 'En_Bairro', 'En_Estado']
            ]
        ],
        'Metabase' => [
            'namespace' => 'Metabase',
            'config' => [
                'links' => [
                    'opportunities' => [
                        'title' => 'Painel sobre oportunidades',
                        'link' => 'https://bi.mapacultural.pa.gov.br/public/dashboard/39ce65ee-9d2b-432e-b9d2-e688b18ece7d',
                        'text' => 'Tenha acesso ao número de oportunidades e  editais cadastrados, a quantidade de pessoas participantes inscritas, o perfil demográfico e mais informações.',
                    ],
                    'users' => [
                        'title' => 'Painel sobre usuários',
                        'link' => 'https://bi.mapacultural.pa.gov.br/public/dashboard/bdb52e6a-d225-4ad2-8f53-b3e81a8cbfb3',
                        'text' => 'Acesse e confira os dados gerais dos usuários da plataforma, como o total de pessoas cadastradas, atividades dos usuários e outras informações. ',

                    ],
                    'entities' => [
                        'title' => 'Painel geral das entidades ',
                        'link' => 'https://bi.mapacultural.pa.gov.br/public/dashboard/0d35b260-7c5b-4f19-8c52-10408eccee16',
                        'text' => 'Confira dados relacionados às entidades cadastradas na plataforma, como agentes individuais e coletivos, oportunidades, espaços, eventos e projetos.',
                    ],
                    'agent1' => [
                        'title' => 'Painel sobre agentes individuais',
                        'link' => 'https://bi.mapacultural.pa.gov.br/public/dashboard/dbf9eb35-9304-49a5-9c63-646687bdde41',
                        'text' => 'Saiba os números de agentes individuais cadastrados, quantos são criados mensalmente, por onde estão distribuídos no território e outras informações.',
                    ],
                    'agent2' => [
                        'title' => 'Painel sobre agentes coletivos',
                        'link' => 'https://bi.mapacultural.pa.gov.br/public/dashboard/58913ac8-d511-41e6-887c-4647d98f71e7',
                        'text' => 'Dados sobre a quantidade de  coletivos e instituições (com ou sem CNPJ) cadastrados, por onde se distribuem pelo estado e outras informações.',
                    ],

                    'spaces' => [
                        'title' => 'Painel sobre espaços',
                        'link' => 'https://bi.mapacultural.pa.gov.br/public/dashboard/7eb10b1d-43f3-4adf-aabc-fa46bdd0073a',
                        'text' => 'Conheça, entre outras informações, por onde os espaços estão distribuídos, a quantidade de espaços cadastros na plataforma, os tipos e as áreas de atuação.',
                    ],
                    'events' => [
                        'title' => 'Painel sobre eventos',
                        'link' => 'https://bi.mapacultural.pa.gov.br/public/dashboard/1bfdba17-1340-4ca9-9bc6-ab7dde8c8503',
                        'text' => 'Indicadores relacionados a quantidade de eventos cadastrados, às linguagens culturais e características, as datas de criação e também eventos agendados. ',
                    ],
                    'projects' => [
                        'title' => 'Painel sobre projetos',
                        'link' => 'https://bi.mapacultural.pa.gov.br/public/dashboard/6fe335cd-0f63-4ad6-95df-315c137e82e0',
                        'text' => 'Tenha acesso ao número total de projetos cadastrados, projetos certificados, quantidade de projetos com subprojetos, os tipos e outros dados. ',
                    ],
                ],
                'cards' =>[
                    [
                        'label' => 'Oportunidade',
                        'icon' => 'opportunity',
                        'iconClass' => 'opportunity__color',
                        'panelLink' => 'opportunities',
                        'data' => [
                            [
                                'label' => 'oportunidades criadas',
                                'entity' => Entities\Opportunity::class,
                                'query' => [],
                            ],
                            [
                                'label' => 'oportunidades certificadas',
                                'entity' => Entities\Opportunity::class,
                                'query' => [
                                    '@verified' => 1,
                                ],
                            ],
                        ],
                    ],
                    [
                        'label' => 'Agentes coletivos',
                        'icon' => 'agent-2',
                        'iconClass' => 'agent__color',
                        'panelLink' => 'agent2',
                        'data' => [
                            [
                                'label' => 'coletivos cadastrados',
                                'entity' => Entities\Agent::class,
                                'query' => [
                                    'type' => 'EQ(2)',
                                ],
                            ],
                            [
                                'label' => 'coletivos certificados',
                                'entity' => Entities\Agent::class,
                                'query' => [
                                    'type' => 'EQ(2)',
                                    '@verified' => 1,
                                ],
                            ],
                        ],
                    ],
                    [
                        'label' => 'Agentes individuais',
                        'icon' => 'agent-1',
                        'iconClass' => 'agent__color',
                        'panelLink' => 'agent1',
                        'data' => [
                            [
                                'label' => 'agentes individuais cadastrados',
                                'entity' => Entities\Agent::class,
                                'query' => [
                                    'type' => 'EQ(1)'
                                ],
                            ],
                        ],
                    ],
                    [
                        'label' => 'Espaços',
                        'icon' => 'space',
                        'iconClass' => 'space__color',
                        'panelLink' => 'spaces',
                        'data' => [
                            [
                                'label' => 'espaços cadastrados',
                                'entity' => Entities\Space::class,
                                'query' => [],
                            ],
                            [
                                'label' => 'espaços certificados',
                                'entity' => Entities\Space::class,
                                'query' => [
                                    '@verified' => 1
                                ],
                            ],
                        ],
                    ],
                    [
                        'label' => 'Projetos',
                        'icon' => 'project',
                        'iconClass' => 'project__color',
                        'panelLink' => 'projects',
                        'data' => [
                            [
                                'label' => 'projetos cadastrados',
                                'entity' => Entities\Project::class,
                                'query' => [],
                            ],
                        ],
                    ],
                    [
                        'label' => 'Eventos',
                        'icon' => 'event',
                        'iconClass' => 'event__color',
                        'panelLink' => 'events',
                        'data' => [
                            [
                                'label' => 'eventos cadastrados',
                                'entity' => Entities\Event::class,
                                'query' => [],
                            ],
                        ],
                    ],
                
                ],
            ],
        ]
    ]
];

if(!env("MAPAS_NETWORK_ENABLED", false)){
    unset($config_plugins['plugins']['MapasNetwork']);
}

return $config_plugins;