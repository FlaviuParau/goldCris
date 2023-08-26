<?php

$installer = $this;
$installer->startSetup();

$whitelistBlock = Mage::getModel('admin/block')->load('blugento_productlabels/catalog_product_label', 'block_name');
$whitelistBlock->setData('block_name', 'blugento_productlabels/catalog_product_label');
$whitelistBlock->setData('is_allowed', 1);
$whitelistBlock->save();

$installer->endSetup();