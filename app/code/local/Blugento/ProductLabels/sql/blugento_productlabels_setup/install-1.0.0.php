<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'blugento_productlabels_label'
 */
if ($installer->getConnection()->isTableExists('blugento_productlabels_label') != true) {
    $table = $installer->getConnection()
        ->newTable('blugento_productlabels_label')
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ), 'ID'
        )
        ->addColumn(
            'name',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255, array(),
            'Label name'
        )
        ->addColumn(
            'status',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'default' => 0
            ),
            'Label status'
        )
        ->addColumn(
            'type',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255, array(),
            'Label type'
        )
        ->addColumn(
            'path',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255, array(),
            'Label path'
        )
        ->addColumn(
            'categories',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(),
            'Applied Categories'
        )
        ->addColumn(
            'enabled_on_product',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'default' => 0
            ),
            'Enabled on Product Page'
        )
        ->addColumn(
            'position_on_product',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'default' => 1
            ),
            'Position on product image on product page'
        )
        ->addColumn(
            'enabled_on_category',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'default' => 0
            ),
            'Enabled on Category Page'
        )
        ->addColumn(
            'position_on_category',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'default' => 1
            ),
            'Position on product image on category page'
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