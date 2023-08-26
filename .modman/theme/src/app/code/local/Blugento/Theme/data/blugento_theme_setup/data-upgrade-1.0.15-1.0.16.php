<?php

$installer = $this;
$installer->startSetup();

$configUpdate = Mage::getModel('core/config');
$configUpdate->saveConfig('catalog/product_image/base_height', '515', 'default', 0);

$installer->endSetup();
