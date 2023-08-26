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

class Blugento_GoogleTagManager_Model_Observer
{
	/**
	 * Dispatch by:: catalog_controller_product_view
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Blugento_GoogleTagManager_Model_Observer $this
	 */
	public function observeProductClick(Varien_Event_Observer $observer)
	{
		$_product = $observer->getEvent()->getProduct();
		
		$product = array();
		$product['name']     = $_product->getName();
		$product['id']       = $_product->getId();
		$product['price']    = $_product->getSpecialPrice() ?: $_product->getPrice();
		$product['brand']    = $this->_getModel()->getProductManufacturer($_product);
		$product['category'] = $this->_getModel()->getProductCategories($_product);
		$product['variant']  = '';
		
		if (Mage::helper('catalog/product')->canUseCanonicalTag()) {
			$params = array('_ignore_category' => true);
			$product['url'] =  $_product->getUrlModel()->getUrl($_product, $params);
		} else {
			$product['url'] = $_product->getProductUrl();
		}
		
		Mage::getSingleton('checkout/session')->setProductClick($product);
		
		return $this;
	}
	
	/**
	 * Dispatch by:: checkout_cart_add_product_complete
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Blugento_GoogleTagManager_Model_Observer $this
	 */
	public function addQuoteItem(Varien_Event_Observer $observer)
	{
		$_product = $observer->getEvent()->getProduct();
		
		$product             = $this->_getModel()->getProductInfo($_product);
		$product['quantity'] = Mage::app()->getRequest()->getParam('qty');
		
		Mage::getSingleton('checkout/session')->setProductQuoteAddItem($product);

		return $this;
	}

	/**
	 * Dispatch by:: sales_quote_remove_item
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Blugento_GoogleTagManager_Model_Observer $this
	 */
	public function removeQuoteItem(Varien_Event_Observer $observer)
	{
		$quoteItem = $observer->getEvent()->getQuoteItem();
		/** @var Mage_Catalog_Model_Product $product */
		$product   = Mage::getModel('catalog/product')->load($quoteItem->getProductId());
		
		$product             = $this->_getModel()->getProductInfo($product);
		$product['quantity'] = $observer->getEvent()->getQuoteItem()->getQty();

		Mage::getSingleton('checkout/session')->setProductQuoteRemoveItem($product);

		return $this;
	}
	
	/**
	 * Dispatch by:: sales_order_place_after
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Blugento_GoogleTagManager_Model_Observer $this
	 */
	public function orderPlaceAfter(Varien_Event_Observer $observer)
	{
		$quote = $observer->getEvent()->getOrder();
		
		$transaction = array();
		$cart        = array();
		$items       = array();
		
		$transaction['id']          = $quote->getRealOrderId();
		$transaction['affiliation'] = '';
		$transaction['revenue']     = $quote->getGrandTotal();
		$transaction['tax']         = $quote->getTaxAmount();
		$transaction['shipping']    = $quote->getShippingAmount();
		$transaction['coupon']      = $quote->getCouponCode();
		$transaction['payment']     = $quote->getPayment()->getMethod();
		$transaction['delivery']    = $quote->getShippingMethod();
		$transaction['type']        = ($quote->getBillingAddress()->getBlugentoPurchaseType() == 25) ?
			$this->_getHelper()->__('Personal Purchase') : $this->_getHelper()->__('Company Purchase');
		
		foreach ($quote->getAllVisibleItems() as $item) {
			/** @var Mage_Catalog_Model_Product $_product */
			$_product = Mage::getModel('catalog/product')->load($item->getProductId());
			
			$_item             = $this->_getModel()->getProductInfo($_product);
			$_item['quantity'] = $item->getQtyOrdered();
			$_item['coupon']   = '';

			$items[] = $_item;
		}
		
		Mage::getSingleton('checkout/session')->setOrderTransactionData($transaction);
		Mage::getSingleton('checkout/session')->setOrderItemsData($items);
		
		return $this;
	}
	
	/**
	 * @return Blugento_GoogleTagManager_Model_Request
	 */
	private function _getModel()
	{
		/** @var $model Blugento_GoogleTagManager_Model_Request */
		$model = Mage::getModel('blugento_googletagmanager/request');
		
		return $model;
	}
	
	/**
	 * @return Blugento_GoogleTagManager_Helper_Data
	 */
	private function _getHelper()
	{
		/** @var $helper Blugento_GoogleTagManager_Helper_Data */
		$helper = Mage::helper('blugento_googletagmanager');
		
		return $helper;
	}
}
