<?php

/** Remove Bucuresti from cities list and leave on Sectors */

$installer = $this;
$installer->startSetup();

$sql = 'DELETE FROM `blugento_cautocomplete_city` WHERE `city` LIKE "Bucuresti" AND `region` LIKE "Bucuresti";';

$installer->run($sql);


$installer->endSetup();
