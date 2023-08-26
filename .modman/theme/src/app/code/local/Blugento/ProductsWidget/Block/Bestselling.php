<?php

class Blugento_ProductsWidget_Block_Bestselling extends Blugento_ProductsWidget_Block_Abstract
{
	/**
	 * Internal constructor, that is called from real constructor
	 *
	 */
	protected function _construct()
	{
		$this->_type = 'PW_BESTSELLING_';
		
		return parent::_construct();
	}
	
	/**
	 * Return best selling product collection.
	 *
	 * @return Mage_Catalog_Model_Resource_Product_Collection|null
	 * @throws Exception
	 */
	protected function _getProductCollection()
	{
		$topProducts      = array();
		$numberOfProducts = $this->getMaxItems() ? $this->getMaxItems() : 6;
		
		$timePeriod = ($this->getNumberOfDays()) ? $this->getNumberOfDays() : 30;
		$today      = strtotime(date('Y-m-d H:i:s', Mage::getModel('core/date')->gmtTimestamp()));
		$last       = $today - (60 * 60 * 24 * $timePeriod);
		$from       = date('Y-m-d H:i:s', $last);
		$to         = date('Y-m-d H:i:s', $today);
		
		/** @var Blugento_ProductsWidget_Model_Bestselling $model */
		$model = Mage::getModel('blugento_productswidget/bestselling');
		
		if (Mage::getSingleton('customer/session')->isLoggedIn()) {
			$customer   = Mage::getSingleton('customer/session')->getCustomer();
			$collection = $model->processBestSellers($from, $to, $customer->getId());
			
			if (is_array($collection) && count($collection) == 0) {
				$collection = $model->processBestSellers($from, $to);
			} elseif (is_array($collection) && count($collection) < $numberOfProducts) {
				foreach ($model->processBestSellers($from, $to) as $item) {
					array_push($collection, $item);
				}
			}
			
		} else {
			$collection = $model->processBestSellers($from, $to);
		}
		
		if (is_array($collection) && count($collection)) {
			foreach ($collection as $item) {
				$topProducts[] = array(
					'id' => $item['product_id']
				);
			}
		}
		
		$selectedIds = array_map(function ($a) {
			return $a['id'];
		}, $topProducts);
		
		$selectedIds = array_unique($selectedIds);
		$ordIds = array_slice($selectedIds, 0, $numberOfProducts);
		
		/** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
		$collection = Mage::getResourceModel('catalog/product_collection');
		$collection = $this->_addProductAttributesAndPrices($collection)
			->addAttributeToFilter('entity_id', array('in' => $selectedIds))
			->addFieldToFilter('visibility', array(
				Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
				Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
			));

		if (!$this->getAllowOutOfStock()) {
			Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
		}
		
		if (!$collection) {
			return null;
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
