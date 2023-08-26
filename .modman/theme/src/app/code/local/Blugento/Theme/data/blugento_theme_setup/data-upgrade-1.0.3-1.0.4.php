<?php

$installer = $this;
$installer->startSetup();

$configUpdate = Mage::getModel('core/config');
$configUpdate->saveConfig('catalog/product_image/base_width', '515', 'default', 0);
$configUpdate->saveConfig('catalog/product_image/small_width', '300', 'default', 0);

$installer->endSetup();
