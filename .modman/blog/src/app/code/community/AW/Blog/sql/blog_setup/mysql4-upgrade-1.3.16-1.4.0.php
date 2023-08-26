<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$connection = $installer->getConnection();

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('blog/blog'),
    'featured_image', array('type'=> Varien_Db_Ddl_Table::TYPE_TEXT,'length' => 255, 'nullable' => false, 'comment'=> 'Featured Image'));

$installer->endSetup();
