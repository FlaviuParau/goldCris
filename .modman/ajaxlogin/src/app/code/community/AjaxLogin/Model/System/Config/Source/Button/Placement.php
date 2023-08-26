<?php

class AjaxLogin_Model_System_Config_Source_Button_Placement
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
				'value' => 'top',
				'label' => Mage::helper('ajaxlogin')->__('Top'),
			),
			array(
				'value' => 'default',
				'label' => Mage::helper('ajaxlogin')->__('Default'),
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
			'top' => 'Top',
			'default' => 'Default',
		);
		
		return $options;
	}
}
