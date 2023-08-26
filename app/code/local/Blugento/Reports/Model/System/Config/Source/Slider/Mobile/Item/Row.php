<?php

class Blugento_Reports_Model_System_Config_Source_Slider_Mobile_Item_Row
{
	public function toOptionArray()
	{
		return array(
			array(
				'label' => Mage::helper('blugento_reports')->__('1 Item'),
				'value' => 1
			),
			array(
				'label' => Mage::helper('blugento_reports')->__('2 Items'),
				'value' => 2
			),
		);
	}
}
