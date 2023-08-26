<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Html page block
 *
 * @category   Mage
 * @package    Mage_Page
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Blugento_SeoEnhancements_Block_Html_Head extends Mage_Page_Block_Html_Head
{
    /**
     * Retrieve URL to robots file
     *
     * @return string
     */
    public function getRobots()
    {
        if (empty($this->_data['robots'])) {
            $this->_data['robots'] = Mage::getStoreConfig('design/head/default_robots');
        }
        
        //if ($this->getRequest()->getParam('p')){
            //$this->_data['robots'] = str_replace('NOINDEX,', '', $this->_data['robots']);
        //}
	    
        return $this->_data['robots'];
    }
    
    protected function getNewFavicon() {
		$this->_data['new_favicon'] = '';
		
	    if (Mage::helper('blugento_seoenhancements')->isAddNewFaviconEnabled() && empty($this->_data['new_favicon'])) {
		    $this->_data['new_favicon'] = $this->getMediaUrl() . 'seo_enhancements/favicon/' . $this->getNewFavIconConfigPath();
	    }
	
	    return $this->_data['new_favicon'];
    }
	
	protected function getNewOgLogo() {
		$this->_data['new_og_logo'] = '';

		if (Mage::helper('blugento_seoenhancements')->isAddNewOgLogoEnabled() && empty($this->_data['new_og_logo'])) {
			$this->_data['new_og_logo'] = $this->getMediaUrl() . 'seo_enhancements/og-logo/' . $this->getNewOgLogoConfigPath();
		}

		return $this->_data['new_og_logo'];
	}

	protected function getViewportUserScalable() {
		return Mage::getStoreConfig('blugento_seoenhancements/enhancements_group/viewport_user_scalable', Mage::app()->getStore()->getStoreId());
	}

	protected function getViewportMaximumScale() {
		return Mage::getStoreConfig('blugento_seoenhancements/enhancements_group/viewport_maximum_scale', Mage::app()->getStore()->getStoreId());
	}
	
	private function getMediaUrl() {
    	return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
	}
	
	private function getNewFavIconConfigPath() {
    	return Mage::getStoreConfig('blugento_seoenhancements/enhancements_group/add_new_favicon', Mage::app()->getStore()->getStoreId());
	}
	
	private function getNewOgLogoConfigPath() {
    	return Mage::getStoreConfig('blugento_seoenhancements/enhancements_group/add_new_logo', Mage::app()->getStore()->getStoreId());
	}
}

