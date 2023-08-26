<?php
// @var $installer Mage_Core_Model_Resource_Setup
// add a count on all products that the rule apply
$installer = $this;
$connection = $installer->getConnection();

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('blog/blog'),
    'enable_for_customer_group', array('type'=> Varien_Db_Ddl_Table::TYPE_TEXT,
                                       'length' => 255,
                                       'unsigned'  => true,
                                       'nullable' => false,
                                       'default'   => '0,1,2,3',
                                       'comment'=> 'Enable Post for Customer Group'
                                      )
    );

$installer->endSetup();
