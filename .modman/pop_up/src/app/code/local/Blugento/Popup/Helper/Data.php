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
 * @package     Blugento_Popup
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Popup_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isEnabled()
    {
        return (int) Mage::getStoreConfig('blugento_popup/general/enabled');
    }

    public function getDisplayTime()
    {
        return (int) Mage::getStoreConfig('blugento_popup/general/display_time');
    }

    public function getCookieExpirationTime()
    {
        return (float) Mage::getStoreConfig('blugento_popup/general/cookie_expiration_time');
    }
	
	public function isBackgroundClickDisabled()
	{
		return (int) Mage::getStoreConfig('blugento_popup/general/disable_background_click');
	}
}