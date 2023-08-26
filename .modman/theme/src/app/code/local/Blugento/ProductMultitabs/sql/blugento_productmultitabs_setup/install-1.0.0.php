<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'blugento_productmultitabs_tabs'
 */
if ($installer->getConnection()->isTableExists('blugento_productmultitabs_tabs') != true) {
    $table = $installer->getConnection()
        ->newTable('blugento_productmultitabs_tabs')
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ),
            'ID'
        )
        ->addColumn(
            'name',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            'Tab Name'
        )
        ->addColumn(
            'identifier',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            'Tab Identifier'
        )
        ->addColumn(
            'content',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            'Tab Content'
        )
        ->addColumn(
            'active_on_products',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            'Active on Products'
        )
        ->addColumn(
            'products_codes',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            'Products Codes'
        )
        ->addColumn(
            'status',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            11,
            array(
                'default' => 1
            ),
            'Status'
        )
        ->addColumn(
            'type',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255, array(),
            'Tab type'
        )
        ->addColumn(
            'created_at',
            Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            null,
            array(
                'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT
            ),
            'Created At'
        )
        ->addColumn(
            'updated_at',
            Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            null,
            array(
                'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE
            ),
            'Updated At'
        );

    $installer->getConnection()->createTable($table);
}

$installer->endSetup();