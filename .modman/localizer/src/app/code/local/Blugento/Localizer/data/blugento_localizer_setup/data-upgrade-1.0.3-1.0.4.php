<?php

$installer = $this;
$installer->startSetup();

$configUpdate = Mage::getModel('core/config');

$configData = Mage::getStoreConfig('design/footer/copyright');
$configData = str_replace('2016', '{{year}}', $configData);

$configUpdate->saveConfig('design/footer/copyright', $configData, 'default', 0);

$installer->endSetup();