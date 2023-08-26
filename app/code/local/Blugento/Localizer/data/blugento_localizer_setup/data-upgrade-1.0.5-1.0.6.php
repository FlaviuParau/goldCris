<?php

$installer = $this;
$installer->startSetup();

$config = Mage::getModel('core/config');

$config->saveConfig('tax/cart_display/subtotal', 1, 'default', 0);
$config->saveConfig('tax/sales_display/price', 2, 'default', 0);
$config->saveConfig('tax/sales_display/subtotal', 2, 'default', 0);
$config->saveConfig('tax/sales_display/shipping', 2, 'default', 0);
$config->saveConfig('tax/sales_display/grandtotal', 0, 'default', 0);
$config->saveConfig('tax/sales_display/full_summary', 0, 'default', 0);
$config->saveConfig('tax/sales_display/zero_tax', 0, 'default', 0);
$config->saveConfig('tax/sales_display/no_sum_on_details', 0, 'default', 0);
$config->saveConfig('tax/sales_display/hide_grandtotal_excl_tax', 0, 'default', 0);

$config->reinit();

$installer->endSetup();