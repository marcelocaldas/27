<?php

/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 */

 $em = $app->em;
 $conn = $em->getConnection();
 
 $agents = $conn->fetchAll("SELECT * from agent");

 var_dump($agents);

?>
