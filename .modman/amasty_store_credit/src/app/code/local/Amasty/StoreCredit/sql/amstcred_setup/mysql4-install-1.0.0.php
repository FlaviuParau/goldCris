<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
/* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer = $this;
$installer->startSetup();


$installer->run("
CREATE TABLE {$installer->getTable('amstcred/amount')} (
  `amount_id` int(11) NOT NULL auto_increment,
  `product_id` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_type_id` smallint (5) unsigned NOT NULL,
  `website_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `attribute_id` smallint (5) unsigned NOT NULL,
  `value` decimal(12,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY  (`amount_id`),
  CONSTRAINT `FK_AMSTCRED_AMOUNT_PRODUCT_ENTITY` FOREIGN KEY (`product_id`) REFERENCES {$installer->getTable('catalog_product_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_AMSTCRED_AMOUNT_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES {$installer->getTable('core_website')} (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_AMSTCRED_AMOUNT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$installer->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


$installer->run("
CREATE TABLE `{$installer->getTable('amstcred/customer_balance')}` (
	`balance_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`customer_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Customer Id',
	`website_id` SMALLINT(5) UNSIGNED NULL DEFAULT NULL COMMENT 'Website Id',
	`amount` DECIMAL(12,4) NOT NULL DEFAULT '0.0000' COMMENT 'Balance Amount',
	`base_currency_code` VARCHAR(3) NULL DEFAULT NULL COMMENT 'Base Currency Code',
	`subscribe_updates` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Is subscribe updates balance',
	PRIMARY KEY (`balance_id`),
	UNIQUE INDEX `UNQ_AMSTCRED_CUSTOMER_BALANCE_CUSTOMER_ID_WEBSITE_ID` (`customer_id`, `website_id`),
	INDEX `IDX_AMSTCRED_CUSTOMER_BALANCE_WEBSITE` (`website_id`),
	CONSTRAINT `FK_AMSTCRED_CUSTOMER_BALANCE_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES {$installer->getTable('core_website')} (`website_id`) ON UPDATE CASCADE ON DELETE SET NULL,
	CONSTRAINT `FK_AMSTCRED_CUSTOMER_BALANCE_CUSTOMER` FOREIGN KEY (`customer_id`) REFERENCES {$installer->getTable('customer_entity')} (`entity_id`) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
");


$installer->run("
CREATE TABLE `{$installer->getTable('amstcred/customer_balance_history')}` (
	`history_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`balance_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Balance',
	`updated_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Updated At',
	`action` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Action',
	`operation_name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Operation Name',
	`operation_data` TEXT NULL DEFAULT NULL COMMENT 'Operation Data',
	`comment` TEXT NULL DEFAULT NULL COMMENT 'Comment',
	`balance_amount` DECIMAL(12,4) NOT NULL DEFAULT '0.0000' COMMENT 'Balance Amount',
	`balance_delta` DECIMAL(12,4) NOT NULL DEFAULT '0.0000' COMMENT 'Balance Delta',
	`is_notified` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Is notified',
	PRIMARY KEY (`history_id`),
	INDEX `IDX_AMSTCRED_CUSTOMER_BALANCE_HISTORY_BALANCE` (`balance_id`),
	CONSTRAINT `FK_AMSTCRED_CUSTOMER_BALANCE_HISTORY_BALANCE` FOREIGN KEY (`balance_id`) REFERENCES `{$installer->getTable('amstcred/customer_balance')}` (`balance_id`) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
");


$installer->run("
CREATE TABLE `{$installer->getTable('amstcred/customer_balance_send')}` (
	`balance_send_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`sender_id` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT 'Sender Id',
	`recipient_id` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT 'Recipient Id',
	`recipient_name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Recipient name',
	`recipient_email` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Recipient email',
	`message` TEXT NULL DEFAULT NULL COMMENT 'Message',
	`website_id` SMALLINT(5) UNSIGNED NULL DEFAULT NULL COMMENT 'Website Id',
	`amount` DECIMAL(12,4) NOT NULL DEFAULT '0.0000' COMMENT 'Balance Amount',
	`created_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Updated At',
	`is_redeemed` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Redeemed',
	PRIMARY KEY (`balance_send_id`),
	INDEX `IDX_AMSTCRED_CUSTOMER_BALANCE_SEND_EMAIL_WEBSITE` (`recipient_email`, `website_id`, `is_redeemed`),
	CONSTRAINT `FK_AMSTCRED_CUSTOMER_BALANCE_SEND_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES {$installer->getTable('core_website')} (`website_id`) ON UPDATE CASCADE ON DELETE SET NULL,
	CONSTRAINT `FK_AMSTCRED_CUSTOMER_BALANCE_SEND_SENDER` FOREIGN KEY (`sender_id`) REFERENCES {$installer->getTable('customer_entity')} (`entity_id`) ON UPDATE CASCADE ON DELETE SET NULL,
	CONSTRAINT `FK_AMSTCRED_CUSTOMER_BALANCE_SEND_RECIPIENT` FOREIGN KEY (`recipient_id`) REFERENCES {$installer->getTable('customer_entity')} (`entity_id`) ON UPDATE CASCADE ON DELETE SET NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
");


$installer->addAttribute('catalog_product', 'amstcred_amount', array(
    'group' => 'Prices',
    'type' => 'decimal',
    'backend' => 'amstcred/attribute_backend_storeCredit_amount',
    'frontend' => '',
    'label' => 'Amounts',
    'input' => 'price',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible' => true,
    'required' => false,
    'user_defined' => false,
    'default' => '',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'apply_to' => 'amstcred',
    'is_configurable' => false,
    'used_in_product_listing' => true,
    'sort_order' => -5,
));


$installer->addAttribute('catalog_product', 'amstcred_allow_open_amount', array(
    'group' => 'Prices',
    'type' => 'int',
    'backend' => '',
    'frontend' => '',
    'label' => 'Allow Open Amount',
    'input' => 'select',
    'class' => '',
    'source' => 'eav/entity_attribute_source_boolean',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible' => true,
    'required' => true,
    'user_defined' => false,
    'default' => '',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'apply_to' => 'amstcred',
    'is_configurable' => false,
    'used_in_product_listing' => true,
    'sort_order' => -4,
));


$installer->addAttribute('catalog_product', 'amstcred_open_amount_min', array(
    'group' => 'Prices',
    'type' => 'decimal',
    'backend' => 'catalog/product_attribute_backend_price',
    'frontend' => '',
    'label' => 'Open Amount Min Value',
    'input' => 'price',
    'class' => 'validate-number',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible' => true,
    'required' => false,
    'user_defined' => false,
    'default' => '',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'apply_to' => 'amstcred',
    'is_configurable' => false,
    'used_in_product_listing' => true,
    'sort_order' => -3,
));
$installer->addAttribute('catalog_product', 'amstcred_open_amount_max', array(
    'group' => 'Prices',
    'type' => 'decimal',
    'backend' => 'catalog/product_attribute_backend_price',
    'frontend' => '',
    'label' => 'Open Amount Max Value',
    'input' => 'price',
    'class' => 'validate-number',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible' => true,
    'required' => false,
    'user_defined' => false,
    'default' => '',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'apply_to' => 'amstcred',
    'is_configurable' => false,
    'used_in_product_listing' => true,
    'sort_order' => -2,
));


$installer->addAttribute('catalog_product', 'amstcred_price_type', array(
    'group' => 'Prices',
    'type' => 'int',
    'backend' => '',
    'frontend' => '',
    'label' => 'Price equal to',
    'input' => 'select',
    'class' => '',
    'source' => 'amstcred/source_priceType',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible' => true,
    'required' => true,
    'user_defined' => false,
    'default' => '',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'apply_to' => 'amstcred',
    'is_configurable' => false,
    'used_in_product_listing' => true,
    'sort_order' => -1,
));

$installer->addAttribute('catalog_product', 'amstcred_price_percent', array(
    'group' => 'Prices',
    'type' => 'decimal',
    'backend' => '',
    'frontend' => '',
    'label' => 'Specify percent',
    'input' => 'text',
    'class' => 'validate-number',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible' => true,
    'required' => false,
    'user_defined' => false,
    'default' => '',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'apply_to' => 'amstcred',
    'is_configurable' => false,
    'used_in_product_listing' => true,
    'sort_order' => 0,
));


$installer->endSetup();


$installerSales = new Mage_Sales_Model_Resource_Setup('core_setup');
$installerSales->startSetup();


$installerSales->addAttribute('quote', 'base_amstcred_amount_used', array('type' => 'decimal'));
$installerSales->addAttribute('quote', 'amstcred_amount_used', array('type' => 'decimal'));
$installerSales->addAttribute('quote', 'amstcred_use_customer_balance', array('type' => 'integer'));

$installerSales->addAttribute('quote_address', 'base_amstcred_amount', array('type' => 'decimal'));
$installerSales->addAttribute('quote_address', 'amstcred_amount', array('type' => 'decimal'));


$installerSales->addAttribute('order', 'base_amstcred_amount', array('type' => 'decimal'));
$installerSales->addAttribute('order', 'amstcred_amount', array('type' => 'decimal'));

$installerSales->addAttribute('order', 'base_amstcred_amount_invoiced', array('type' => 'decimal'));
$installerSales->addAttribute('order', 'amstcred_amount_invoiced', array('type' => 'decimal'));

$installerSales->addAttribute('order', 'base_amstcred_amount_refunded', array('type' => 'decimal'));
$installerSales->addAttribute('order', 'amstcred_amount_refunded', array('type' => 'decimal'));


$installerSales->addAttribute('invoice', 'base_amstcred_amount', array('type' => 'decimal'));
$installerSales->addAttribute('invoice', 'amstcred_amount', array('type' => 'decimal'));

$installerSales->addAttribute('creditmemo', 'base_amstcred_amount', array('type' => 'decimal'));
$installerSales->addAttribute('creditmemo', 'amstcred_amount', array('type' => 'decimal'));


$installerSales->endSetup();


