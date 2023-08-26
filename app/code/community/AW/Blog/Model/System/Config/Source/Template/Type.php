<?php

class AW_Blog_Model_System_Config_Source_Template_Type
{
	public function toOptionArray()
	{
		return array(
			array(
				'label' => Mage::helper('blog')->__('Big Boxes'),
				'value' => 'grid-4 slider-enabled'
			),
			array(
				'label' => Mage::helper('blog')->__('Big Boxes - Only Images'),
				'value' => 'grid-4 box-image slider-enabled'
			),
			array(
				'label' => Mage::helper('blog')->__('Small Boxes'),
				'value' => 'grid-6 slider-disabled'
			),
			array(
				'label' => Mage::helper('blog')->__('Small Boxes - Only Images'),
				'value' => 'grid-6 box-image slider-disabled'
			),
			array(
				'label' => Mage::helper('blog')->__('Small Boxes - Left Image'),
				'value' => 'grid-4 box-left slider-disabled'
			),
			array(
				'label' => Mage::helper('blog')->__('Small Boxes - Right Image'),
				'value' => 'grid-4 box-right slider-disabled'
			)
		);
	}
}
