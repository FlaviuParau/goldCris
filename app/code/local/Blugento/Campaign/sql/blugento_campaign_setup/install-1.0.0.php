<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'blugento_campaign'
 */
if ($installer->getConnection()->isTableExists('blugento_campaign') != true) {
    $table = $installer->getConnection()
        ->newTable('blugento_campaign')
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
            255,
            array(),
            'Campaign name'
        )
        ->addColumn(
            'code',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(),
            'Campaign code'
        )
        ->addColumn(
            'status',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'default' => 0
            ),
            'Campaign status'
        )
        ->addColumn(
            'start_date',
            Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            null,
            array(
                'default' => null
            ),
            'Start Date'
        )
        ->addColumn(
            'end_date',
            Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            null,
            array(
                'default' => null
            ),
            'End Date'
        )
        ->addColumn(
            'associated_category',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'default' => null
            ),
            'Associated Category'
        )
        ->addColumn(
            'cms_page',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(),
            'CMS Page'
        )
        ->addColumn(
            'layout',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(),
            'Used Layout'
        )
        ->addColumn(
            'show_out_of_stock',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'default' => 0
            ),
            'Display Out of Stock Products'
        )
        ->addColumn(
            'shortcode',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(),
            'Short Code'
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

$whitelistBlock = Mage::getModel('admin/block')->load('blugento_campaign/catalog_product_list', 'block_name');
$whitelistBlock->setData('block_name', 'blugento_campaign/catalog_product_list');
$whitelistBlock->setData('is_allowed', 1);
$whitelistBlock->save();

$whitelistBlockAjax = Mage::getModel('admin/block')->load('blugento_campaign/catalog_product_ajax_list', 'block_name');
$whitelistBlockAjax->setData('block_name', 'blugento_campaign/catalog_product_ajax_list');
$whitelistBlockAjax->setData('is_allowed', 1);
$whitelistBlockAjax->save();

$installer->endSetup();
