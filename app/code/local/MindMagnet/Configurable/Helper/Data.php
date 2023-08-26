<?php

class MindMagnet_Configurable_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Get javascript file path
	 *
	 * @return string
	 */
	public function getConfigurableJs()
	{
		if ($this->isShowPriceOptionEnabled()) {
			$path = 'js/mindmagnet_configurable/show_price/configurable.js';
		} else {
			$path = 'js/mindmagnet_configurable/configurable.js';
		}
		return $path;
	}
	
	/**
	 * Determine if the option is enabled
	 *
	 * @return int
	 */
	protected function isShowPriceOptionEnabled()
	{
		return (int) Mage::getStoreConfig('mindmagnet_configurable/options/enable');
	}
}
