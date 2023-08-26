<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('blugento_importer/importer'), 'is_duplicate_images', array(
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'nullable' => true,
        'after' => 'remove_comma',
        'comment' => 'Is Duplicate Images'
    ));
$installer->endSetup();

