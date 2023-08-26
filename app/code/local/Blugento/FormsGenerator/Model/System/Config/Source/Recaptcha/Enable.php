<?php

class Blugento_FormsGenerator_Model_System_Config_Source_Recaptcha_Enable extends Mage_Core_Model_Abstract
{
	const STATUS_ENABLED  = 1;
	const STATUS_DISABLED = 0;
	
	public function toOptionArray()
	{
		return array(
			self::STATUS_ENABLED  => Mage::helper('blugento_formsgenerator')->__('Yes'),
			self::STATUS_DISABLED => Mage::helper('blugento_formsgenerator')->__('No')
		);
	}
}
