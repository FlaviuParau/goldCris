<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingPerItem
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->updateAttribute('catalog_product', 'am_shipping_peritem', array('apply_to' => 'simple,configurable,bundle'));
$installer->endSetup();
