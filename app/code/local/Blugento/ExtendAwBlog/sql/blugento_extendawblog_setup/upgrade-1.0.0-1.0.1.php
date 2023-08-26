<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$installer->getConnection()->modifyColumn('aw_blog', 'sort_order', array('type'=> Varien_Db_Ddl_Table::TYPE_SMALLINT, 'default' => 9999));
$installer->endSetup();