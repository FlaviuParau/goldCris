<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('blugento_importer/importer'), 'removeproductsfromcategories', array(
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'nullable' => true,
        'size' => null,
        'after' => 'processcategories',
        'default' => 1,
        'comment' => 'Remove products from old categories'
    ));
$installer->endSetup();

