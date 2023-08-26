<?php
/**
 * Blugento AjaxCart
 * Helper Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_AjaxCart
 * @author Daniel Gheoltan <daniel.gheoltan@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_AjaxCart_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLED = 'blugento_ajaxcart/general/enable';

    /**
     * Check if module is enabled
     *
     * @return int
     */
    public function isEnabled()
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_ENABLED);
    }
	
	public function isCartDeleteEnabled()
	{
		return (int) Mage::getStoreConfig('blugento_ajaxcart/general/enable_cart_confirm');
	}
	
	public function isSimpleProductImageEnabled()
	{
		return (int) Mage::getStoreConfig('blugento_ajaxcart/general/show_simple_product_image');
	}
}
