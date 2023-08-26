<?php

class Blugento_Sliders_Block_Form_Element_Mobile_Image extends Varien_Data_Form_Element_Image
{
	/**
	 * Prepend the base image URL to the image filename
	 *
	 * @return null|string
	 */
	protected function _getUrl()
	{
		if ($this->getValue() && !is_array($this->getValue())) {
			return Mage::helper('blugento_sliders/image')->getMobileImageUrl($this->getValue());
		}
		
		return null;
	}
}