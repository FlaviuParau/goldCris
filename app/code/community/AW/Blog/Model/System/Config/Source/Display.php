<?php

class AW_Blog_Model_System_Config_Source_Display
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
				'label' => Mage::helper('blog')->__('No')
			),
			array(
				'value' => 2,
				'label' => Mage::helper('blog')->__('Yes')
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
			1 => 'Standard',
			2 => 'Slider'
		);
		
		return $options;
	}
}
