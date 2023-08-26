<?php

/** Fix zip code for specific city */

$installer = $this;
$installer->startSetup();

$updateSql = 'UPDATE blugento_cautocomplete_city SET zipcode = "910001" WHERE city = "Calarasi" AND region = "Calarasi" AND zipcode = "910000"';
$installer->run($updateSql);

$installer->endSetup();
