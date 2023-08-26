<?php

$installer = $this;
$installer->startSetup();

$table = 'blugento_productmultitabs_tabs';

/** Add value "0" to store column for all tabs. */
$sql = 'UPDATE ' . $table . '
        SET stores = 0';

$installer->run($sql);

$installer->endSetup();
