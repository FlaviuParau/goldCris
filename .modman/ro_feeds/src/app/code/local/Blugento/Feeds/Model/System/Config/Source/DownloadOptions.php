<?php

class Blugento_Feeds_Model_System_Config_Source_DownloadOptions
{
	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		return array(
			array('value' => 'text', 'label' => 'Plain text'),
			array('value' => 'csv', 'label' => 'CSV'),
			array('value' => 'xml', 'label' => 'XML'),
		);
	}
}
