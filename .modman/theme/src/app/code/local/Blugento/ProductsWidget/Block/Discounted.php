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
class Blugento_ProductsWidget_Block_Discounted extends Blugento_ProductsWidget_Block_Abstract
{
	/**
	 * Internal constructor, that is called from real constructor
	 *
	 */
	protected function _construct()
	{
		$this->_type = 'PW_DISCOUNTED_';
		
		return parent::_construct();
	}
	
	/**
	 * Return discounted products collection.
	 *
	 * @return Mage_Catalog_Model_Resource_Product_Collection
	 * @throws Mage_Core_Model_Store_Exception
	 */
	public function _getProductCollection()
	{
		$todayStartOfDayDate = Mage::app()->getLocale()->date()
			->setTime('00:00:00')
			->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
		
		$todayEndOfDayDate = Mage::app()->getLocale()->date()
			->setTime('23:59:59')
			->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
		
		/** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
		$collection = Mage::getModel('catalog/product')->getCollection();
		$collection
			->addAttributeToSelect(array('*'))
			->addFieldToFilter('visibility', array(
				Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
				Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
			))
			->addAttributeToFilter(
				'special_from_date', array('or' => array(
				0 => array('date' => true, 'to' => $todayEndOfDayDate),
				1 => array('is' => new Zend_Db_Expr('null')))
			), 'left'
			)
			->addAttributeToFilter(
				'special_to_date', array('or' => array(
				0 => array('date' => true, 'from' => $todayStartOfDayDate),
				1 => array('is' => new Zend_Db_Expr('null')))
			), 'left'
			)
			->addAttributeToFilter(
				array(
					array('attribute' => 'special_from_date', 'is' => new Zend_Db_Expr('not null')),
					array('attribute' => 'special_to_date', 'is' => new Zend_Db_Expr('not null'))
				)
			)
			->addAttributeToSort('special_from_date', 'desc')
			->addFinalPrice()
			->getSelect()
			->where('price_index.final_price < price_index.price');

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
		$cacheModel->saveCacheInfo($this->_cacheKey, $existingProductsId);
		
		return $collection;
	}
}
