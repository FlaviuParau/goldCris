<?php

class Blugento_CategoryShowcase_Model_System_Config_Source_Image_Height
{
	public function toOptionArray()
	{
		return array(
			array(
				'label' => Mage::helper('blugento_categoryshowcase')->__('-- Please Select a Value --'),
				'value' => ''
			),
			array(
				'label' => Mage::helper('blugento_categoryshowcase')->__('200'),
				'value' => 200
			),
			array(
				'label' => Mage::helper('blugento_categoryshowcase')->__('300'),
				'value' => 300
			),
			array(
				'label' => Mage::helper('blugento_categoryshowcase')->__('400'),
				'value' => 400
			),
			array(
				'label' => Mage::helper('blugento_categoryshowcase')->__('500'),
				'value' => 500
			),
			array(
				'label' => Mage::helper('blugento_categoryshowcase')->__('600'),
				'value' => 600
			),
		);
	}
}
