<?php

$installer = $this;
$installer->startSetup();

$installer->updateAttribute('catalog_product', 'blugento_cart_custom', 'used_in_product_listing', true);

$installer->endSetup();