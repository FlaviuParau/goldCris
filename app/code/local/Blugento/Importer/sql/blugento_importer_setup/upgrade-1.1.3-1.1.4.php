<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('blugento_importer/importer'), 'skip_zero_prices', array(
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'nullable' => true,
        'default' => 0,
        'comment' => 'Skip 0 Prices'
    ));
$installer->endSetup();
