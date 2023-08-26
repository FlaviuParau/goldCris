<?php
/**
 * Blugento Cart Settings
 * Attribute Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Cart
 * @link http://www.blugento.com
 */

class Blugento_Cart_Model_System_Config_Source_Attributes
{
	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		$result = array('' => 'Choose an attribute');
		
		$attributeCollection = Mage::getResourceModel('catalog/product_attribute_collection')
			->addVisibleFilter()
			->addFieldToFilter('frontend_input', array('text', 'select', 'textarea'));

		foreach ($attributeCollection as $attribute) {
			$attributeId = $attribute->getData('attribute_code');
			$name        = $attribute->getData('frontend_label');
			
			$result[] = array(
				'value' => $attributeId,
				'label' => $name
			);
		}
		
		return $result;
	}
}
