<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('blugento_importer/importer'), 'xml_entity_node', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'length' => 255,
        'after' => 'enclosure',
        'comment' => 'Entity XML Node Name'
    ));
$installer->endSetup();

