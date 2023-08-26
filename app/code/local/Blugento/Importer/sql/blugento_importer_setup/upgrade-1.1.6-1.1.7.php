<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('blugento_importer/importer'), 'extra_price_percent', array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => true,
        'default' => 0,
        'comment' => 'Extra Price (Percent)'
    ));

$installer->endSetup();
