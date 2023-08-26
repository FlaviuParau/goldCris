<?php

class Blugento_Billing_Model_System_Config_Source_Street_Length
{
	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		return array(
			array('value' => '', 'label' => Mage::helper('blugento_billing')->__('Select a value')),
			array('value' => 75, 'label' => Mage::helper('blugento_billing')->__('75 characters')),
			array('value' => 85, 'label' => Mage::helper('blugento_billing')->__('85 characters')),
			array('value' => 95, 'label' => Mage::helper('blugento_billing')->__('95 characters')),
			array('value' => 100, 'label' => Mage::helper('blugento_billing')->__('100 characters')),
			array('value' => 120, 'label' => Mage::helper('blugento_billing')->__('120 characters')),
			array('value' => 140, 'label' => Mage::helper('blugento_billing')->__('140 characters')),
			array('value' => 160, 'label' => Mage::helper('blugento_billing')->__('160 characters')),
		);
	}
}
