<?php

$installer = $this;
$installer->startSetup();

$configModel = new Mage_Core_Model_Config();
$configModel->saveConfig('customer/address/street_lines', '1', 'default', 0);
$configModel->saveConfig('onestepcheckout/addfield/street_lines', '1', 'default', 0);

$installer->endSetup();