<?php
$installer = $this;
$installer->startSetup();

/** Add 'stores' column to 'blugento_productmultitabs_tabs' table */
$installer->getConnection()
    ->addColumn('blugento_productmultitabs_tabs', 'stores', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => null,
        'comment' => 'Stores',
        'after' => 'type'
    ));

$installer->endSetup();