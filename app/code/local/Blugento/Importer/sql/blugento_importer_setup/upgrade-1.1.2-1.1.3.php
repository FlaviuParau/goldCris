<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()->modifyColumn(
    $installer->getTable('blugento_importer/importer'),
    'default_visibility',
    array (
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 225
    )
);

$installer->getConnection()->modifyColumn(
    $installer->getTable('blugento_importer/importer'),
    'default_tax_class_id',
    array (
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 225
    )
);

$installer->getConnection()->modifyColumn(
    $installer->getTable('blugento_importer/importer'),
    'default_status',
    array (
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 225
    )
);

$installer->getConnection()->modifyColumn(
    $installer->getTable('blugento_importer/importer'),
    'default_weight',
    array (
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 225
    )
);

$installer->endSetup();