<?php

class Blugento_ProductsWidget_Model_System_Config_Source_Slider_Item_Cssease
{
	public function toOptionArray()
	{
		return array(
			array(
				'label' => Mage::helper('blugento_productswidget')->__('None'),
				'value' => ''
			),
			array(
				'label' => Mage::helper('blugento_productswidget')->__('Linear'),
				'value' => 'linear'
			),
			array(
				'label' => Mage::helper('blugento_productswidget')->__('Ease'),
				'value' => 'ease'
			),
			array(
				'label' => Mage::helper('blugento_productswidget')->__('Ease In Out'),
				'value' => 'ease-in-out'
			),
			array(
				'label' => Mage::helper('blugento_productswidget')->__('Ease Out Elastic'),
				'value' => 'easeOutElastic'
			),
		);
	}
}
