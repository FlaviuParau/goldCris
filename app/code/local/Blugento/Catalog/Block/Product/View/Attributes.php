<?php

class Blugento_Catalog_Block_Product_View_Attributes extends Mage_Catalog_Block_Product_View_Attributes
{
	/**
	 * Return attribute group name on product page
	 *
	 * @param array $excludeAttr
	 * @return array $data
	 * @throws Mage_Core_Model_Store_Exception
	 */
	public function getAdditionalDataCustom($excludeAttr = array())
	{
		$data       = array();
		$product    = $this->getProduct();
		$attributes = $product->getAttributes();
		
		foreach ($attributes as $attribute) {
			if ($attribute->getIsVisibleOnFront() && !in_array($attribute->getAttributeCode(), $excludeAttr)) {
				$value = $attribute->getFrontend()->getValue($product);
				if (is_string($value)) {
					if (strlen($value) && $product->getData($attribute->getAttributeCode())) {
						if ($attribute->getFrontendInput() == 'price') {
							$value = Mage::app()->getStore()->convertPrice($value,true);
						}
						$group = 0;
						if ($tmp = $attribute->getData('attribute_group_id')) {
							$group = $tmp;
						}
						$data[$group]['items'][$attribute->getAttributeCode()] = array(
							'label' => $attribute->getStoreLabel(),
							'value' => $value,
							'code'  => $attribute->getAttributeCode()
						);
						$data[$group]['attrid'] = $attribute->getId();
					}
				}
			}
		}
		
		foreach ($data as $groupId => &$group) {
			$groupModel = Mage::getModel('eav/entity_attribute_group')->load($groupId);
			$group['title'] = $groupModel->getAttributeGroupName();
		}
		
		return $data;
	}
}
