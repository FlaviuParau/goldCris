<?php

class Blugento_ProductsWidget_Model_System_Config_Source_Mobile_Item_Row
{
	public function toOptionArray()
	{
		return array(
			array(
				'label' => Mage::helper('blugento_productswidget')->__('1 Item'),
				'value' => 1
			),
			array(
				'label' => Mage::helper('blugento_productswidget')->__('2 Items'),
				'value' => 2
			),
		);
	}
}
