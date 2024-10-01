<?php

use MapasCulturais\i;

/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 */

$this->import(
    'entity-terms'
);
?>
<entity-terms :entity="entity" taxonomy="segmento" :editable="<?=$isEditable?>" classes="col-12" title="<?php i::_e('Segmento cultural'); ?>" ></entity-terms>
