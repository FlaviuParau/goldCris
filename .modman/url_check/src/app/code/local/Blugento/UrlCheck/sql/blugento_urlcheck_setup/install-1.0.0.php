<?php
$installer  = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();

$installer->updateAttribute('catalog_product', 'name', 'backend_model', 'blugento_urlcheck/product_attribute_backend_name');

$installer->endSetup();
