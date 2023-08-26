<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'blugento_cautocomplete_city'
 */
if ($installer->getConnection()->isTableExists('blugento_cautocomplete_city') != true) {
    $table = $installer->getConnection()
        ->newTable('blugento_cautocomplete_city')
        ->addColumn(
            'id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ),
            'ID'
        )
        ->addColumn(
            'city',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            255, array(),
            'City'
        )
        ->addColumn(
            'region',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            255, array(),
            'Region'
        )
        ->addColumn(
            'region_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            11,
            array(),
            'Region ID'
        )
        ->addColumn(
            'zipcode',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            255,
            array(
                'nullable' => true,
                'default' => null
            ),
            'Zipcode'
        )
        ->addColumn(
            'country_code',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            255, array(),
            'Country Code'
        )
        ->addColumn(
            'priority',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            11,
            array(),
            'Priority'
        )
    ;
    $installer->getConnection()->createTable($table);
}

$installer->endSetup();