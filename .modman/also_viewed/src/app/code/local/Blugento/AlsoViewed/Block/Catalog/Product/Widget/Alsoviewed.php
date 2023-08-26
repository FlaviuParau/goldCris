<?php
/**
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
 * @package     Blugento_AlsoViewed
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_AlsoViewed_Block_Catalog_Product_Widget_Alsoviewed
	extends Mage_Catalog_Block_Product_List
	implements Mage_Widget_Block_Interface
{
	/**
	 * Get product collection for also viewed widget
	 *
	 * @return array|null
	 */
	public function getProductCollection()
	{
		$currentProduct = parent::getProduct();
		
		if (!$currentProduct) {
			return null;
		}
		
		$productSku  = $currentProduct->getSku();
		$categoryIds = $currentProduct->getCategoryIds();
		$maxItems    = $this->getMaxItems() ?: 10;
		
		$collection = Mage::getModel('blugento_alsoviewed/alsoviewed')
			->getCollection()
			->addFieldToFilter('product_sku', array('neq' => $productSku))
			->setPageSize($maxItems);
		
		$_productCollection = array();
		
		foreach ($collection->getData() as $prod) {
			if ($this->getSameCategoryOnly()) {
				$productCatIds = explode(',', $prod['product_categories']);
				$result        = array_intersect($categoryIds, $productCatIds);
				
				if (!empty($result)) {
					$productIds[] = $prod['product_id'];
					$_productCollection = Mage::getModel('blugento_alsoviewed/product_collection')->getProducts(implode(',', $productIds));
				}
			} else {
				$productIds[] = $prod['product_id'];
				$_productCollection = Mage::getModel('blugento_alsoviewed/product_collection')->getProducts(implode(',', $productIds));
			}
		}
		
		return $_productCollection;
	}
}
