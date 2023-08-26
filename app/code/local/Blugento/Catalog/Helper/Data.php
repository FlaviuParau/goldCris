<?php

class Blugento_Catalog_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function showSimpleProductSku()
	{
		return (int) Mage::getStoreConfig('configswatches/general/dynamic_sku');
	}
	
	public function updateDecimalQty()
	{
		return (int) Mage::getStoreConfig('configswatches/general/decimal_qty');
	}
}
