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
 * @package     Blugento_GoogleTagManager
 * @author      Stîncel-Toader Octavian-Cristian <tavi@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_GoogleTagManager_Block_TM extends Mage_Core_Block_Template
{
	/**
	 * DataLayer Model
	 *
	 * @var Blugento_GoogleTagManager_Model_Request _dataLayerModel
	 */
	protected $_dataLayerModel = null;
	
	/**
	 * Google Tag Manager data
	 *
	 * @var Blugento_GoogleTagManager_Helper_Data _gtmHelper
	 */
	protected $_gtmHelper = null;
	
	/**
	 * Blugento_GoogleTagManager_Block_TM constructor
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->_dataLayerModel = Mage::getModel('blugento_googletagmanager/request');
		$this->_gtmHelper      = Mage::helper('blugento_googletagmanager');
	}
	
	/**
	 * Return Customer Data Layer Information
	 */
	public function getCustomerGtmTrackingCode()
	{
		echo $this->_getVariables($this->_dataLayerModel->getCustomerVariables());
	}
	
	/**
	 * Return Product View Data Layer Information
	 */
	public function getProductGtmTrackingCode()
	{
		echo $this->_getVariables($this->_dataLayerModel->getProductVariables());
	}
	
	/**
	 * Return Product Impressions Data Layer Information
	 */
	public function getProductImpressionGtmTrackingCode()
	{
		echo $this->_getVariables($this->_dataLayerModel->getProductImpressionVariables());
	}
	
	/**
	 * Return Product Click Data Layer Information
	 */
	public function getProductClickGtmTrackingCode()
	{
		echo $this->_getVariables($this->_dataLayerModel->getProductClickVariables());
	}
	
	/**
	 * Return Category View Data Layer Information
	 */
	public function getCategoryGtmTrackingCode()
	{
		echo $this->_getVariables($this->_dataLayerModel->getCategoryVariables());
	}
	
	/**
	 * Return Add to Cart Product Data Layer Information
	 */
	public function getAddQuoteItemGtmTrackingCode()
	{
		echo $this->_getVariables($this->_dataLayerModel->getAddQuoteItemVariables());
	}
	
	/**
	 * Return Remove from Cart Product Data Layer Information
	 */
	public function getRemoveQuoteItemGtmTrackingCode()
	{
		echo $this->_getVariables($this->_dataLayerModel->getRemoveQuoteItemVariables());
	}
	
	/**
	 * Return Order and Products Success Page Data Layer Information
	 */
	public function getSuccessPageGtmTrackingCode()
	{
		echo $this->_getVariables($this->_dataLayerModel->getSuccessPageVariables());
	}

	/**
	 * Return Initiate Checkout Data Layer Information
	 */
	public function getInitiateCheckoutTrackingCode()
	{
		echo $this->_getVariables($this->_dataLayerModel->getInitiateCheckoutVariables());
	}
	
	/**
	 * Return Order and Products Global Transaction Event Information
	 */
	public function getGlobalSuccessPageGtmTrackingCode()
	{
		echo $this->_getGlobalCode($this->_dataLayerModel->getGlobalTransactionEvent());
	}
	
	/**
	 * Return Category Filters Data Layer Information
	 */
	public function getCategoryFilterGtmTrackingCode()
	{
		echo $this->_getVariables($this->_dataLayerModel->getCategoryFilterVariables());
	}
	
	/**
	 * Return Category Sort Data Layer Information
	 */
	public function getCategorySortGtmTrackingCode()
	{
		echo $this->_getVariables($this->_dataLayerModel->getCategorySortVariables());
	}
	
	/**
	 * Return Data Layer Information
	 *
	 * @param array $variables
	 * @return string
	 */
	private function _getVariables($variables)
	{
		if (!$this->_gtmHelper->isEnabled()) {
			return '';
		}
		
		if ($variables) {
			$result   = array();
			$result[] = sprintf("dataLayer.push(%s);\n", json_encode($variables, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
			
			return implode("\n", $result) . "\n";
		} else {
			return '';
		}
	}
	
	/**
	 * Return Global Gtm Information
	 *
	 * @param array $variables
	 * @return string
	 */
	private function _getGlobalCode($variables)
	{
		if ($variables) {
			$result   = array();
			$result[] = sprintf("Blugento.Gtm.push(%s);\n", json_encode($variables, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
			
			return implode("\n", $result);
		} else {
			return '';
		}
	}
}
