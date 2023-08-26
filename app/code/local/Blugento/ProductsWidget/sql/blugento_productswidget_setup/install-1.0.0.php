<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'blugento_productswidget_cache'
 */
if ($installer->getConnection()->isTableExists('blugento_productswidget_cache') != true) {
    $table = $installer->getConnection()
        ->newTable('blugento_productswidget_cache')
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ), 'ID')
        ->addColumn(
            'product_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            'Product ID')
        ->addColumn(
            'category_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            'Category ID')
        ->addColumn(
            'cache_key',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            155, array(),
            'Cache Key')
        ;
    $installer->getConnection()->createTable($table);
}

$installer->endSetup();
