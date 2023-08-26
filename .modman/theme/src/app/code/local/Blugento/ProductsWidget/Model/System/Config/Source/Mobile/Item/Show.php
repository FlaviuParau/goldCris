<?php

class Blugento_ProductsWidget_Model_System_Config_Source_Mobile_Item_Show
{
	public function toOptionArray()
	{
		return array(
			array(
				'label' => Mage::helper('blugento_productswidget')->__('Yes'),
				'value' => 1
			),
			array(
				'label' => Mage::helper('blugento_productswidget')->__('No'),
				'value' => 2
			),
		);
	}
}
