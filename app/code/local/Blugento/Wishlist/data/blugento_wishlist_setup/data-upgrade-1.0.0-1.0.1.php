<?php
$installer = $this;
$installer->startSetup();

$configModel = new Mage_Core_Model_Config();
$configModel->saveConfig('wishlist/email/disable_share', '1', 'default', 0);

$installer->endSetup();