<?php

class Blugento_ProductsWidget_Model_System_Config_Source_Slider_Item_Dots
{
	public function toOptionArray()
	{
		return array(
			array(
				'label' => Mage::helper('blugento_productswidget')->__('False'),
				'value' => 2
			),
			array(
				'label' => Mage::helper('blugento_productswidget')->__('True'),
				'value' => 1
			)
		);
	}
}
