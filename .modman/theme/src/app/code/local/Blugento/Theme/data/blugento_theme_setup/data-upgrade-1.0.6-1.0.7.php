<?php

$installer = $this;
$installer->startSetup();

$configUpdate = Mage::getModel('core/config');
$configUpdate->saveConfig('web/cookie/cookie_restriction', '1', 'default', 0);

$installer->endSetup();
