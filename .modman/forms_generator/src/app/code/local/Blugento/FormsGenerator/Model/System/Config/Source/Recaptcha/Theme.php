<?php

class Blugento_FormsGenerator_Model_System_Config_Source_Recaptcha_Theme
{
	public function toOptionArray()
	{
		return array(
			array(
				'value' => '0',
				'label' => 'Light'
			),
			array(
				'value' => '1',
				'label' => 'Dark'
			),
		);
	}
}
