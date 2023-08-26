<?php
/**
 * Set value 0 (All store views) for all labels
 */
$installer = $this;
$installer->startSetup();

$table = 'blugento_productlabels_label';

$sql = "UPDATE $table
        SET stores = 0";

$installer->run($sql);

$installer->endSetup();