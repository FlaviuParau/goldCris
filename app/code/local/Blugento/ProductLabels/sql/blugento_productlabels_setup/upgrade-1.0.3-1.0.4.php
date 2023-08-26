<?php

$installer = $this;
$installer->startSetup();

/** Add 'stores' column to 'blugento_productlabels_label' table */
$installer->getConnection()
    ->addColumn('blugento_productlabels_label', 'stores', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => null,
        'comment' => 'Stores',
        'after' => 'position_on_category'
    ));

$installer->endSetup();