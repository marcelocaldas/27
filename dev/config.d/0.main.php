<?php
use MapasCulturais\i;

$_ENV['APP_MODE'] = 'development';
$__process_assets = false;

return [
    /* MAIN */
    'themes.active' => 'MapasPA',
    'app.mode' => $_ENV['APP_MODE'],
    'doctrine.isDev' => false, // deixe true somente se estiver trabalhando nos mapeamentos das entidades
    
    /* SELOS */
    'app.verifiedSealsIds' => [1],

    /* ASSET MANAGER */
    'themes.assetManager' => new \MapasCulturais\AssetManagers\FileSystem([
        'publishPath' => BASE_PATH . 'assets/',

        'mergeScripts' => $__process_assets,
        'mergeStyles' => $__process_assets,

        'process.js' => !$__process_assets ?
                'cp {IN} {OUT}':
                'terser {IN} --source-map --output {OUT} ',

        'process.css' => !$__process_assets ?
                'cp {IN} {OUT}':
                'uglifycss {IN} > {OUT}',

        'publishFolderCommand' => 'cp -R {IN} {PUBLISH_PATH}{FILENAME}'
    ]),
    'app.geoDivisionsHierarchy' => [
        'pais'              => ['name' => i::__('País'),            'showLayer' => true],
        'estado'            => ['name' => i::__('Estado'),          'showLayer' => true],
        'mesorregiao'       => ['name' => i::__('Mesorregião'),     'showLayer' => true],
        'RI'  => ['name' => i::__('Região de integração'),'showLayer' => true],
        'microrregiao'      => ['name' => i::__('Microrregião'),    'showLayer' => true],
        'municipio'         => ['name' => i::__('Município'),       'showLayer' => true],
    ]
];