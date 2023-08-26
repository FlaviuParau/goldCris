<?php

class Blugento_AlsoViewed_Model_Source_Select_Slider_Item_Loop
{
	public function toOptionArray()
	{
		return array(
			array(
				'label' => Mage::helper('blugento_productswidget')->__('True'),
				'value' => 1
			),
			array(
				'label' => Mage::helper('blugento_productswidget')->__('False'),
				'value' => 2
			)
		);
	}
}
