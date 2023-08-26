<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer  = $this;
$connection = $installer->getConnection();
$installer->startSetup();

$installer->getConnection()
    ->addColumn(
        $installer->getTable('blugento_generated_forms'),
        'store_id',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'size'      => 11,
            'comment'   => 'Store ID',
            'after'     => 'id'
        )
    );

$installer->endSetup();
