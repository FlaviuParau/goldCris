<?php

$installer = $this;
$installer->startSetup();

$table = 'blugento_productlabels_label';

$sql = 'SELECT * FROM ' . $table;

$data = $installer->getConnection()->fetchAll($sql);

if (!count($data)) {
    $labelsData = Mage::getSingleton('blugento_productlabels/system_config_source_data')->toArray();

    $sql = 'INSERT INTO ' . $table . ' (name, status, type, path, enabled_on_product, position_on_product, enabled_on_category, position_on_category)
            VALUES ';

    foreach ($labelsData as $data) {
        $sql .= '("' . implode('", "', $data) . '"),';
    }

    $sql = substr($sql, 0, strlen($sql) - 1);

    $installer->run($sql);
}

$installer->endSetup();
