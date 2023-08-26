<?php

$installer = $this;
$installer->startSetup();

try {
	$sql = 'UPDATE `blugento_popup` SET `stores` = 0';
	$installer->run($sql);
} catch (Exception $e) {
	Mage::logException($e);
}

$installer->endSetup();
