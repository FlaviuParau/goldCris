<?php

class AjaxLogin_Model_System_Config_Source_Button_Action
{
	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		$array = array(
			array(
				'value' => 1,
				'label' => Mage::helper('ajaxlogin')->__('Show register'),
			),
			array(
				'value' => 2,
				'label' => Mage::helper('ajaxlogin')->__('Show CMS Block'),
			)
		);
		
		return $array;
	}
	
	/**
	 * Get options in "key-value" format
	 *
	 * @return array
	 */
	public function toArray()
	{
		$options = array(
			1 => Mage::helper('ajaxlogin')->__('Show register'),
			2 => Mage::helper('ajaxlogin')->__('Show CMS Block'),
		);
		
		return $options;
	}
}
