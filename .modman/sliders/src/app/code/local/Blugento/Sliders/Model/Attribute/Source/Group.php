<?php

class Blugento_Sliders_Model_Attribute_Source_Group extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	/**
	 * Get all options as array - slider group collection
	 *
	 * @return array _options as option array
	 */
	public function getAllOptions()
	{
		if (!$this->_options) {
			$this->_options = Mage::getResourceModel('blugento_sliders/group_collection')->load()->toOptionArray();
			array_unshift($this->_options, array(
				'value' => '',
				'label' => Mage::helper('blugento_sliders')->__('Please select a banner group ...')
			));
		}
		
		return $this->_options;
	}
}
