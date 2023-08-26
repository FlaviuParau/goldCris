<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
	->addColumn('blugento_popup', 'stores', array(
		'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
		'length' => null,
		'comment' => 'Stores',
        'after' => 'status',
	));

$installer->endSetup();
