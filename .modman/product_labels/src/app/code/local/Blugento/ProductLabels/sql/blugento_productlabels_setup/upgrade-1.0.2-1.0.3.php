<?php

$installer = $this;
$installer->startSetup();

/** Add 'created_type' column to 'blugento_productlabels_label' table */
$installer->getConnection()
    ->addColumn('blugento_productlabels_label', 'created_type', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => null,
        'comment' => 'Created Type',
        'after' => 'type'
    ));

$installer->endSetup();