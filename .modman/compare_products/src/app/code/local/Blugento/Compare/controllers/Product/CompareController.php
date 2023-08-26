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
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog comapare controller
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */

require_once(Mage::getModuleDir('controllers','Mage_Catalog') . DS . 'Product' . DS . 'CompareController.php');

class Blugento_Compare_Product_CompareController extends Mage_Catalog_Product_CompareController
{
	/**
	 * Add item to compare list
	 */
	public function addAction()
	{
		if (!$this->_validateFormKey()) {
			$this->_redirectReferer();
			return;
		}
		
		$productId = (int) $this->getRequest()->getParam('product');
		if ($productId
			&& (Mage::getSingleton('log/visitor')->getId() || Mage::getSingleton('customer/session')->isLoggedIn())
		) {
			$product = Mage::getModel('catalog/product')
				->setStoreId(Mage::app()->getStore()->getId())
				->load($productId);
			
			if ($product->getId()/* && !$product->isSuper()*/) {
				Mage::getSingleton('catalog/product_compare_list')->addProduct($product);
				Mage::getSingleton('catalog/session')->addSuccess(
					$this->__('The product %s has been added to comparison list.', Mage::helper('core')->escapeHtml($product->getName()))
				);
				Mage::dispatchEvent('catalog_product_compare_add_product', array('product'=>$product));
			}
			
			Mage::helper('catalog/product_compare')->calculate();
		}
		
		$this->_redirectReferer();
	}
	
	/**
	 * Remove item from compare list
	 */
	public function removeAction()
	{
		if ($productId = (int) $this->getRequest()->getParam('product')) {
			$product = Mage::getModel('catalog/product')
				->setStoreId(Mage::app()->getStore()->getId())
				->load($productId);
			
			if($product->getId()) {
				/** @var $item Mage_Catalog_Model_Product_Compare_Item */
				$item = Mage::getModel('catalog/product_compare_item');
				if(Mage::getSingleton('customer/session')->isLoggedIn()) {
					$item->addCustomerData(Mage::getSingleton('customer/session')->getCustomer());
				} elseif ($this->_customerId) {
					$item->addCustomerData(
						Mage::getModel('customer/customer')->load($this->_customerId)
					);
				} else {
					$item->addVisitorId(Mage::getSingleton('log/visitor')->getId());
				}
				
				$item->loadByProduct($product);
				
				if($item->getId()) {
					$item->delete();
					Mage::getSingleton('catalog/session')->addSuccess(
						$this->__('The product %s has been removed from comparison list.', $product->getName())
					);
					Mage::dispatchEvent('catalog_product_compare_remove_product', array('product'=>$item));
					Mage::helper('catalog/product_compare')->calculate();
				}
			}
		}
		
		if (!$this->getRequest()->getParam('isAjax', false)) {
			$this->_redirectReferer();
		}
	}
}
