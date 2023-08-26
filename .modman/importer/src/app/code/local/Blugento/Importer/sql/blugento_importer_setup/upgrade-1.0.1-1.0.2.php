<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('blugento_importer/importer'), 'processcategories', array(
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'nullable' => true,
        'size' => null,
        'after' => 'processimages',
        'comment' => 'Process Categories'
    ));
$installer->endSetup();
