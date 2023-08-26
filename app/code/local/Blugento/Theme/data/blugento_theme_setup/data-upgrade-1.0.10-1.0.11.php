<?php

$installer = $this;
$installer->startSetup();

$configUpdate = Mage::getModel('core/config');
$configUpdate->saveConfig('checkout/options/enable_agreements', '1', 'default', 0);
$configUpdate->saveConfig('checkout/sidebar/display', '1', 'default', 0);

$installer->endSetup();
