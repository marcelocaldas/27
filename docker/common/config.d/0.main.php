<?php
use MapasCulturais\i;

return [
    'app.siteName' => 'JP Cultura',
    'app.siteDescription' => "A JP Cultura é uma plataforma colaborativa que reúne informações sobre agentes, espaços, eventos, projetos culturais e oportunidades",

    'themes.active' => "MapasPA",

    'app.lcode' => 'pt_BR',
    'homeHeader.banner' => 'img/banner-paulo-gustavo.png',
    'homeHeader.bannerLink' => 'https://mapacultural.pa.gov.br/files/agent/7/caderno-de-orientacoes-pcac-27-09-2023.pdf',
    'homeHeader.downloadableLink' => true,

    'homeHeader.secondBanner' => 'img/cultura-sustentabilidade.jpeg',
    'homeHeader.secondBannerLink' => 'https://secult.pa.gov.br/download/124/cultura-sustentabilidade-e-mudancas-climaticas/',
    'homeHeader.secondDownloadableLink' => true,

    'homeHeader.thirdBanner' => 'img/banner-acessibilidade.jpg',
    'homeHeader.thirdBannerLink' => 'https://www.secult.pa.gov.br/download/81/guias-de-acessibilidade-inclusao-e-protagonismo-da-pessoa-com-deficiencia-e-do-migrante/',
    'homeHeader.thirdDownloadableLink' => true,
    'app.geoDivisionsHierarchy' => [
        'pais'              => ['name' => i::__('País'),            'showLayer' => true],
        'estado'            => ['name' => i::__('Estado'),          'showLayer' => true],
        'mesorregiao'       => ['name' => i::__('Mesorregião'),     'showLayer' => true],
        'RI'  => ['name' => i::__('Região de integração'),'showLayer' => true],
        'microrregiao'      => ['name' => i::__('Microrregião'),    'showLayer' => true],
        'municipio'         => ['name' => i::__('Município'),       'showLayer' => true],
    ],

    'app.geoDivisionsFilters' => [15],
    
    'app.geoDivisionsShowAgentRI' => true,
];
