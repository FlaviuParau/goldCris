<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('blugento_importer/history'), 'skipped_rows', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'after' => 'result',
        'comment' => 'Skipped Rows'
    ));

$installer->getConnection()
    ->addColumn($installer->getTable('blugento_importer/history'), 'missing_images', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'after' => 'result',
        'comment' => 'Missing or unimported images'
    ));

$installer->getConnection()
    ->addColumn($installer->getTable('blugento_importer/history'), 'updated', array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => true,
        'after' => 'result',
        'comment' => 'Number of updated products'
    ));

$installer->getConnection()
    ->addColumn($installer->getTable('blugento_importer/history'), 'imported', array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => true,
        'after' => 'result',
        'comment' => 'Number of imported products'
    ));

$installer->endSetup();
