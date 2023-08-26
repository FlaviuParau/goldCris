<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('blugento_importer/importer'), 'rootcategory', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'comment' => 'Root Category in which the categories are created'
    ));

$installer->endSetup();
