<?php

$installer = $this;
$installer->startSetup();

$configUpdate = Mage::getModel('core/config');
$configUpdate->saveConfig('customer/address/middlename_show', '0', 'default', 0);

$configUpdate = new Mage_Customer_Model_Entity_Setup('core_setup');
$configUpdate->updateAttribute('customer', 'middlename', 'is_visible', '0');

$installer->endSetup();
