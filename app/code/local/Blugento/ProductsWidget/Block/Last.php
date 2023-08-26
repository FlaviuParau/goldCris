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
 * @package     Blugento_ProductsWidget
 * @author      Mihai Rastasan <mihai.rastasan@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Blugento_ProductsWidget_Block_Last extends Blugento_ProductsWidget_Block_Abstract
{
	/**
	 * Internal constructor, that is called from real constructor
	 *
	 */
	protected function _construct()
	{
		$this->_type = 'PW_LAST_';
		
		return parent::_construct();
	}
	
	/**
	 * Return new products collection.
	 *
	 * @return Mage_Catalog_Model_Resource_Product_Collection
	 * @throws Mage_Core_Model_Store_Exception
	 */
	protected function _getProductCollection()
	{
		/** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
		$collection = Mage::getResourceModel('catalog/product_collection');
		$collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());
		
		$collection = $this->_addProductAttributesAndPrices($collection)
			->addStoreFilter()
			->addAttributeToSort('created_at', 'desc');
		
		if (!$collection) {
			return null;
		}
		
		$maxItems = $this->getMaxItems() ? $this->getMaxItems() : 12;
		$collection->getSelect()->limit($maxItems);
		
		$storeId = Mage::app()->getStore()->getId();
		
		$collection->addMinimalPrice()
			->addFinalPrice()
			->setStore($storeId)
			->addStoreFilter($storeId)
			->setCurPage(1);
		
		
		if (!$this->getAllowOutOfStock()) {
			Mage::getSingleton('cataloginventory/stock')
				->addInStockFilterToCollection($collection);
		}
		
		$existingProductsId = array();
		foreach ($collection as $product) {
			$existingProductsId[] = $product->getId();
		}
		
		/** @var Blugento_ProductsWidget_Model_Cache $cacheModel */
		$cacheModel = Mage::getModel('blugento_productswidget/cache');
		$cacheModel->saveCacheInfo($this->_cacheKey, $existingProductsId);
		
		return $collection;
	}
}
