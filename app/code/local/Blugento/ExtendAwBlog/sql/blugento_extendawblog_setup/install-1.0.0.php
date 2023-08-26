<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer  = $this;
$connection = $installer->getConnection();
$installer->startSetup();

$installer->getConnection()->addColumn('aw_blog',
    'sort_order', array('type'=> Varien_Db_Ddl_Table::TYPE_SMALLINT, 'default' => 100, 'nullable' => true, 'comment'=> 'Sort Order'));

$installer->endSetup();
