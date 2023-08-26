<?php

$installer = $this;
$installer->startSetup();

try {
    $sql = 'UPDATE `blugento_popup` SET `category_pages` = "all"';
    $installer->run($sql);
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();
