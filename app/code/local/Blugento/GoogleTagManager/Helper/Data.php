<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @category    Blugento
 * @package     Blugento_GoogleTagManager
 * @author      Stîncel-Toader Octavian-Cristian <tavi@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_GoogleTagManager_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Is module enabled
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return $this->_getConfig('enabled');
	}

	/**
	 * Get GTM Account Id
	 *
	 * @return string
	 */
	public function getAccountId()
	{
		return $this->_getConfig('account');
	}

	/**
	 * Get script category
	 *
	 * @return string
	 */
	public function getCategory()
	{
		return $this->_getConfig('script_category');
	}
	
	/**
	 * Get config values
	 *
	 * @param string $field
	 * @return bool
	 */
	private function _getConfig($field)
	{
		return Mage::getStoreConfig('blugento_googletagmanager/settings/' . $field);
	}
}
