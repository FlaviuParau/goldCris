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
class Blugento_ProductsWidget_Block_Category extends Blugento_ProductsWidget_Block_Abstract
{
	/**
	 * Internal constructor, that is called from real constructor
	 *
	 */
	protected function _construct()
	{
		$this->_type = 'PW_CATEGORY_';
		
		return parent::_construct();
	}
	
	/**
	 * Return category product collection.
	 *
	 * @return Mage_Catalog_Model_Resource_Product_Collection|null
	 * @throws Mage_Core_Model_Store_Exception
	 */
	protected function _getProductCollection()
	{
		$categoryId = $this->getCategory();
		
		if ($categoryId) {
			$category = Mage::getModel('catalog/category')->load($categoryId);
			$collection = $category->getProductCollection()->addAttributeToSort('entity_id', 'DESC')->addAttributeToSelect('*');
			$collection->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
		}
		
		if (!$collection) {
			return null;
		}
		
		$storeId = Mage::app()->getStore()->getId();
		
		$maxItems = $this->getMaxItems() ? $this->getMaxItems() : 12;
		
		$collection->addMinimalPrice()
			->addFinalPrice()
			->setStore($storeId)
			->addStoreFilter($storeId)
			->setPageSize($maxItems)
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
		$cacheModel->saveCacheInfo($this->_cacheKey, $existingProductsId, $categoryId);
		
		return $collection;
	}
}
