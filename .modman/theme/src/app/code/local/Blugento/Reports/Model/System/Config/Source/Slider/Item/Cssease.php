<?php

class Blugento_Reports_Model_System_Config_Source_Slider_Item_Cssease
{
	public function toOptionArray()
	{
		return array(
			array(
				'label' => Mage::helper('blugento_reports')->__('None'),
				'value' => ''
			),
			array(
				'label' => Mage::helper('blugento_reports')->__('Linear'),
				'value' => 'linear'
			),
			array(
				'label' => Mage::helper('blugento_reports')->__('Ease'),
				'value' => 'ease'
			),
			array(
				'label' => Mage::helper('blugento_reports')->__('Ease In Out'),
				'value' => 'ease-in-out'
			),
			array(
				'label' => Mage::helper('blugento_reports')->__('Ease Out Elastic'),
				'value' => 'easeOutElastic'
			),
		);
	}
}
