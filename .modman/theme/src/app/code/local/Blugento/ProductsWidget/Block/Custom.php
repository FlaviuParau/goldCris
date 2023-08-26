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
class Blugento_ProductsWidget_Block_Custom extends Blugento_ProductsWidget_Block_Abstract
{
	/**
	 * Internal constructor, that is called from real constructor
	 *
	 */
	protected function _construct()
	{
		$this->_type = 'PW_CUSTOM_';
		
		return parent::_construct();
	}
	
	/**
	 * Return custom product collection.
	 *
	 * @return Mage_Catalog_Model_Resource_Product_Collection|null
	 * @throws Exception
	 */
	protected function _getProductCollection()
	{
		$selectedIds = explode(',', $this->getProducts());
		$selectedIds = array_unique(array_map('trim', $selectedIds));
		
		if (!count($selectedIds)) {
			return null;
		}
		
		$maxItems = $this->getMaxItems() ? $this->getMaxItems() : 12;
		
		$ordIds = array();
		foreach ($selectedIds as $id) {
			$ordIds[] = $id;
		}
		
		$ordIds = array_slice($ordIds, 0, $maxItems);
		
		/** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
		$collection = Mage::getResourceModel('catalog/product_collection');
		
		$collection = $this->_addProductAttributesAndPrices($collection)
			->addAttributeToFilter('entity_id', array('in' => $selectedIds));
		
		if (!$this->getAllowOutOfStock()) {
			Mage::getSingleton('cataloginventory/stock')
				->addInStockFilterToCollection($collection);
		}
		
		$products = new Varien_Data_Collection();
		foreach ($ordIds as $key => $id) {
			foreach ($collection as $product) {
				if ($product->getId() == $id) {
					$products->addItem($product);
				}
			}
		}
		
		if (!count($products)) {
			return null;
		}
		
		$collection = $products;
		
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
