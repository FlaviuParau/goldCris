<?php

class Blugento_SeoEnhancements_Model_System_Config_Source_SelectedAttributes
{
	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		$result = array();
		
		$attributeCollection = Mage::getResourceModel('eav/entity_attribute_collection')
			->addFieldToFilter('entity_type_id', array('eq' => 4));
		
		foreach ($attributeCollection as $attribute) {
			if ($attribute->getFrontendLabel()) {
				$attributeId = $attribute->getData('attribute_code');
				$name        = $attribute->getData('frontend_label');
				
				$result[] = array(
					'value' => $attributeId,
					'label' => $name
				);
			}
		}
		
		return $result;
	}
}
