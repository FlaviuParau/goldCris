<?php

class Blugento_CatalogInventory_Model_Observer
{
	/**
	 * Return creditmemo items qty to stock and set product stock status
	 *
	 * @param Varien_Event_Observer $observer
	 * @throws Exception
	 */
	public function refundOrderInventory($observer)
	{
		/* @var $creditmemo Mage_Sales_Model_Order_Creditmemo */
		$creditmemo = $observer->getEvent()->getCreditmemo();
		
		foreach ($creditmemo->getAllItems() as $item) {
			$productId = $item->getProductId();
			$product   = Mage::getModel('catalog/product')->load($productId);
			
			if (!$product->isConfigurable()) {
				$stockItem = $product->getStockItem();
				
				$stockItem->setIsInStock(1);
				$stockItem->save();
				
				$product->setStockItem($stockItem);
				$product->save();
			}
		}
	}
}
