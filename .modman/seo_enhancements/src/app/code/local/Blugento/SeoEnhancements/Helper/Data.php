<?php

class Blugento_SeoEnhancements_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function isCanonicalLinkReview()
	{
		return Mage::getStoreConfig('blugento_seoenhancements/enhancements_group/canonical_review');
	}
	
	public function isChangePageAndMetaTitleOptionEnabled()
	{
		return Mage::getStoreConfig('blugento_seoenhancements/enhancements_group/change_page_and_meta_title');
	}
	
	public function isAddNewFaviconEnabled() {
		return Mage::getStoreConfig('blugento_seoenhancements/enhancements_group/change_default_favicon', Mage::app()->getStore()->getStoreId());
	}
	
	public function isAddNewOgLogoEnabled() {
		return Mage::getStoreConfig('blugento_seoenhancements/enhancements_group/change_default_logo', Mage::app()->getStore()->getStoreId());
	}
	
	public function isAddNewSiteLogoEnabled() {
		return Mage::getStoreConfig('blugento_seoenhancements/enhancements_group/change_default_site_logo', Mage::app()->getStore()->getStoreId());
	}

    public function isCanonicalLinkCmsPages()
    {
        return Mage::getStoreConfig('blugento_seoenhancements/enhancements_group/canonical_on_cms_pages');
    }
}
