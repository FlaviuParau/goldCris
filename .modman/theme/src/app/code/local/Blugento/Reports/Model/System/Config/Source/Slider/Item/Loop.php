<?php

class Blugento_Reports_Model_System_Config_Source_Slider_Item_Loop
{
	public function toOptionArray()
	{
		return array(
			array(
				'label' => Mage::helper('blugento_reports')->__('True'),
				'value' => 1
			),
			array(
				'label' => Mage::helper('blugento_reports')->__('False'),
				'value' => 2
			)
		);
	}
}
