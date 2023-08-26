<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @category    Blugento
 * @package     Blugento_RichSnippets
 * @author      Stîncel-Toader Octavian-Cristian <tavi@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Richsnippets_Helper_Data extends Mage_Core_Helper_Abstract
{
    const YOTPO_REVIEWS_CACHE_ID = 'yotpo_reviews';
    const STAMPED_REVIEWS_CACHE_ID = 'stamped_reviews';

	/**
	 * Get Core Helper Values
	 *
	 * @param string $field
	 * @return Mage_Core_Helper_Abstract
	 */
	public function getCoreHelper($field)
	{
		return Mage::helper('core/' . $field);
	}
	
	/**
	 * Determine if Organization is enabled
	 *
	 * @return bool
	 */
	public function isOrgRichSnippetEnabled()
	{
		return $this->_getConfig('organization/enabled');
	}
	
	/**
	 * Get Config Organization
	 *
	 * @param string $field
	 * @return mixed
	 */
	public function organizationInfo($field)
	{
		return $this->_getConfig('organization/' . $field);
	}
	
	/**
	 * Get Config Product Review
	 *
	 * @return bool
	 */
	public function productReview()
	{
		return $this->_getConfig('product/review');
	}
	
	/**
	 * Determine if Configurable Product is split into Simple Products
	 *
	 * @return bool
	 */
	public function splitConfigurableProduct()
	{
		return $this->_getConfig('product/split_configurable_products');
	}
	
	/**
	 * Get Config Product Attributes Information
	 *
	 * @return mixed
	 */
	public function productInformation()
	{
		return $this->_getConfig('product/map_attributes');
	}
	
	/**
	 * Get Config Super Attributes Information
	 *
	 * @return mixed
	 */
	public function getSupperAttributes()
	{
		return $this->_getConfig('product/supper_attributes');
	}
	
	/**
	 * Determine if Breadcrumbs is enabled
	 *
	 * @return bool
	 */
	public function isBreadcrumbsEnabled()
	{
		return $this->_getConfig('breadcrumbs/enabled');
	}
	
	/**
	 * Determine if Website is enabled
	 *
	 * @return bool
	 */
	public function isWebsiteRichSnippetEnabled()
	{
		return $this->_getConfig('website/enabled');
	}
	
	/**
	 * Determine if Blog is enabled
	 *
	 * @return bool
	 */
	public function isBlogRichSnippetEnabled()
	{
		return $this->_getConfig('blog/enabled');
	}
	
	/**
	 * Determine if Product price is visible
	 *
	 * @return bool
	 */
	public function isPriceVisible()
	{
		return $this->_getConfig('product/show_price');
	}
	
	/**
	 * Get Imprint Config Data
	 *
	 * @param string $field
	 * @return mixed
	 */
	public function getImprintInfo($field)
	{
		return Mage::getStoreConfig('blugentolocalizer/imprint/' . $field);
	}
	
	/**
	 * Get Social Media Links
	 *
	 * @param string $field
	 * @return mixed
	 */
	public function getSocialMediaUrl($field)
	{
		return Mage::getStoreConfig('blugento_socialmedia/share_media_' . $field . '/' . $field .'url');
	}
	
	/**
	 * Get Config Values
	 *
	 * @param string $field
	 * @return mixed
	 */
	private function _getConfig($field)
	{
		return Mage::getStoreConfig('blugento_richsnippets/' . $field);
	}
}
