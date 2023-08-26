<?php

class Blugento_Cart_Block_Marketing extends Mage_Core_Block_Template
{
	/**
	 * @return mixed|string
	 * @throws Mage_Core_Model_Store_Exception
	 */
	public function getConversionCode()
	{
		$productID           = Mage::app()->getRequest()->getParam('product');
		$cartSettingsSession = Mage::getSingleton('core/session')->getCartSettingsSession();
		$code                = Mage::getStoreConfig('blugento_cart/global_config/conversion');
		
		if ($productID) {
			$product = Mage::getModel('catalog/product')->load($productID);
			
			$code    = str_replace(
				array(
					'{{productSKU}}',
					'{{productName}}',
					'{{productPrice}}',
					'{{name}}',
					'{{email}}',
					'{{telephone}}',
					'{{address}}',
					'{{county}}',
					'{{locality}}',
					'{{company}}',
					'{{regNr}}',
					'{{vat}}',
					'{{headquarter}}'
				),
				array(
					$product->getSku(),
					$product->getName(),
					$product->getSpecialPrice() ?: $product->getPrice(),
					$cartSettingsSession['name'],
					$cartSettingsSession['email'],
					$cartSettingsSession['telephone'],
					$cartSettingsSession['address'],
					$cartSettingsSession['county'],
					$cartSettingsSession['locality'],
					$cartSettingsSession['company'],
					$cartSettingsSession['reg-nr'],
					$cartSettingsSession['vat'],
					$cartSettingsSession['headquarter'],
				),
				$code
			);
			
			$code .= $this->getGACode($product);
		}
		
		return $code;
	}
	
	/**
	 * @param $product
	 * @return string
	 * @throws Mage_Core_Model_Store_Exception
	 */
	public function getGACode($product)
	{
		$affiliation = Mage::getStoreConfig('blugento_cart/global_config/ga_affiliation');
		$price       = $product->getSpecialPrice() ? $product->getSpecialPrice() : $product->getPrice();
		
		$code = '<script>ga(\'ecommerce:addTransaction\', {
                  \'id\': \'' . 'order_from_contact_form' . '\',
                  \'affiliation\': \'' . $affiliation . '\',
                  \'revenue\': \'' . $price . '\',
                  \'shipping\': \'-\',
                  \'tax\': \'' . $this->_getProductTaxRate($product) . '\'
                });';
		
		$code .= 'ga(\'ecommerce:addItem\', {
                      \'id\': \'' . $product->getId() . '\',
                      \'name\': \'' . $product->getName() . '\',
                      \'sku\': \'' . $product->getSku() . '\',
                      \'category\': \'' . $this->_getProductCategoryName($product) . '\',
                      \'price\': \'' . $price . '\',
                      \'quantity\': \'1\'
                    });';

		return $code . '</script>';

	}
	
	/**
	 * @param $item
	 * @return string
	 */
	protected function _getProductCategoryName($item)
	{
		$product     = Mage::getModel('catalog/product')->load($item->getProductId());
		$categoryIds = $product->getCategoryIds();

		if (count($categoryIds)) {
			$firstCategoryId = $categoryIds[0];
			$_category = Mage::getModel('catalog/category')->load($firstCategoryId);

			return $_category->getName();
		}

		return '';
	}
	
	/**
	 * @param $item
	 * @return mixed
	 * @throws Mage_Core_Model_Store_Exception
	 */
	protected function _getProductTaxRate($item)
	{
		$store      = Mage::app()->getStore('default');
		$request    = Mage::getSingleton('tax/calculation')->getRateRequest(null, null, null, $store);
		$taxClassId = $item->getTaxClassId();
		$tax        = Mage::getSingleton('tax/calculation')->getRate($request->setProductClassId($taxClassId));
		
		return $tax;
	}
}
