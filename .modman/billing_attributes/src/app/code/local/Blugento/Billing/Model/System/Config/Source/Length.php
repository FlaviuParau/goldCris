<?php

class Blugento_Billing_Model_System_Config_Source_Length
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
			array('value' => 7, 'label' => Mage::helper('blugento_billing')->__('7 digits')),
			array('value' => 8, 'label' => Mage::helper('blugento_billing')->__('8 digits')),
			array('value' => 9, 'label' => Mage::helper('blugento_billing')->__('9 digits')),
			array('value' => 10, 'label' => Mage::helper('blugento_billing')->__('10 digits')),
			array('value' => 12, 'label' => Mage::helper('blugento_billing')->__('12 digits')),
			array('value' => 14, 'label' => Mage::helper('blugento_billing')->__('14 digits')),
			array('value' => 16, 'label' => Mage::helper('blugento_billing')->__('16 digits')),
		);
	}
}
