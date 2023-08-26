<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('blugento_importer/importer'), 'remove_comma', array(
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'nullable' => true,
        'size' => null,
        'after' => 'xml_entity_node',
        'comment' => 'Remove comma symbol from numbers'
    ));
$installer->endSetup();
