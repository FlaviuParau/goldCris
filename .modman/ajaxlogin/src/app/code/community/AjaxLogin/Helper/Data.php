<?php

class AjaxLogin_Helper_Data extends Mage_Core_Helper_Abstract {

    protected $logFileName = 'ajaxlogin.log';

    /**
     * Log data
     * @param string|object|array data to log
     */
    public function log($data)
    {
        Mage::log($data, null, $this->logFileName);
    }
	
	/**
	 * Check if extension is enabled
	 *
	 * @return int
	 */
	public function isExtensionEnabled()
	{
		return (int) Mage::getStoreConfig('ajaxlogin/options/enable');
	}
	
	/**
	 * Check if extension is enabled
	 *
	 * @return int
	 */
	public function isFirstLogInOptionEnabled()
	{
		return (int) Mage::getStoreConfig('ajaxlogin/options/enable_first_login');
	}
	
	/**
	 * Get javascript path for first login option
	 *
	 * @return string
	 */
	public function getFirstLoginScriptPath()
	{
		if ($this->isExtensionEnabled() && $this->isFirstLogInOptionEnabled()) {
			$path = 'js/ajaxlogin/script-login.js';
		} else {
			$path = '';
		}
		
		return $path;
	}

}