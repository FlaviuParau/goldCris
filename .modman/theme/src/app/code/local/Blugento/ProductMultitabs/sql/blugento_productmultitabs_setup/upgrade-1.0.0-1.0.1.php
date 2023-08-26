<?php
$installer = $this;
$installer->startSetup();

/** Add 'sort_order' column to 'blugento_productmultitabs_tabs' table */
$installer->getConnection()
    ->addColumn('blugento_productmultitabs_tabs', 'sort_order', array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => false,
        'length' => 11,
        'default' => 0,
        'after' => 'products_codes',
        'comment' => 'Sort Order'
    ));

/** Add 'content_block' column to 'blugento_productmultitabs_tabs' table */
$installer->getConnection()
    ->addColumn('blugento_productmultitabs_tabs', 'content_block', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => null,
        'comment' => 'Content Block',
        'after' => 'content'
    ));

/** Add 'content_attribute' column to 'blugento_productmultitabs_tabs' table */
$installer->getConnection()
    ->addColumn('blugento_productmultitabs_tabs', 'content_attribute', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => null,
        'comment' => 'Content from Product Attribute',
        'after' => 'content_block'
    ));

$installer->endSetup();