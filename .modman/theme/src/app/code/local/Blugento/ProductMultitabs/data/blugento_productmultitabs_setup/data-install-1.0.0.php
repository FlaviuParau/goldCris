<?php

/** Add default tabs to 'blugento_productmultitabs_tabs' table. */

$installer = $this;
$installer->startSetup();

$table = 'blugento_productmultitabs_tabs';

$tabData = Mage::getSingleton('blugento_productmultitabs/system_config_source_data')->toArray();

$sql = 'INSERT INTO ' . $table . ' (name, identifier, content, active_on_products, products_codes, status, type)
        VALUES ';

foreach ($tabData as $data) {
    $sql .= '("' . $data["name"] . '", "' . $data["identifier"] . '", "' . $data["content"] . '", 1, ' . ' NULL, "' . $data["status"] . '", "' . $data["type"] .'"),';
}

$sql = substr($sql, 0, strlen($sql) - 1);

$installer->run($sql);

$installer->endSetup();
