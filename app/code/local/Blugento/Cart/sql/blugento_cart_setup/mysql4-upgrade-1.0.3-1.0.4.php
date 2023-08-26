<?php

$installer = $this;
$installer->startSetup();

try {
	$cmsPage           = Mage::getModel('cms/page');
	$identifier        = 'blugento-product-inquiry';
	$successIdentifier = 'blugento-product-inquiry-success';
	
	if ($cmsPage->load($identifier, 'identifier')->getId()) {
		$cmsPage->setIdentifier('cerere-produs')->save();
	}
	
	if ($cmsPage->load($successIdentifier, 'identifier')->getIdentifier()) {
		$cmsPage->setIdentifier('cerere-produs-success')->save();
	}
	
	Mage::getConfig()->saveConfig('blugento_cart/global_config/cms_page', 'cerere-produs', 'default', 0);
	Mage::getConfig()->saveConfig('blugento_cart/global_config/cms_success_page', 'cerere-produs-success', 'default', 0);
} catch (Exception $e) {
	Mage::logException($e);
}

$installer->endSetup();
