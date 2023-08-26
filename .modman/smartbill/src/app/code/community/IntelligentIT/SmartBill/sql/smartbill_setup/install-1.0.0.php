<?php

$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'smartbill_document_url', 'varchar(255)');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'smartbill_document_series', 'varchar(15)');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'smartbill_document_number', 'varchar(25)');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'smartbill_document_json', 'text');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'smartbill_order_items_prices', 'text');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'smartbill_tax_settings', 'text');
// $installer->addAttribute('order', 'smartbill_document_url', array('type'=>Varien_Db_Ddl_Table::TYPE_VARCHAR, 'length'=>255, 'default'=>''));
$installer->endSetup();