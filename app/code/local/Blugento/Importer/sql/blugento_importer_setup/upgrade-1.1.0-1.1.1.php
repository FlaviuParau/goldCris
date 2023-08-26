<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('blugento_importer/importer'), 'new_file_structure', array(
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'nullable' => true,
        'size' => null,
        'after' => 'file_path',
        'default' => 0,
        'comment' => 'New file structure for configurable products'
    ));
$installer->endSetup();

