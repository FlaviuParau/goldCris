<?php

class Blugento_SeoEnhancements_Helper_SiteLogo extends Mage_Page_Block_Html_Header
{
	protected function getNewSiteLogo() {
		$this->_data['new_site_logo'] = '';
		
		if (Mage::helper('blugento_seoenhancements')->isAddNewSiteLogoEnabled() && empty($this->_data['new_site_logo'])) {
			$this->_data['new_site_logo'] = $this->getMediaUrl() . 'seo_enhancements/site-logo/' . $this->getNewSiteLogoConfigPath();
		}
		
		return $this->_data['new_site_logo'];
	}
	
	private function getMediaUrl() {
		return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
	}
	
	private function getNewSiteLogoConfigPath() {
		return Mage::getStoreConfig('blugento_seoenhancements/enhancements_group/add_new_site_logo', Mage::app()->getStore()->getStoreId());
	}
}
