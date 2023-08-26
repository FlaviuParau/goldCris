<?php
/**
 * Blugento Checkout
 * Helper Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Checkout
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Checkout_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLED = 'blugento_checkout/general/enabled';
    const XML_PATH_ORDER_COMMENT_ENABLED = 'blugento_checkout/general/order_comment_enabled';
    const XML_PATH_COUPON_FORM_ENABLED = 'blugento_checkout/general/coupon_enabled';

    /**
     * Check if module is enabled
     *
     * @return int
     */
    public function isEnabled()
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_ENABLED);
    }

    /**
     * Check if order comment is enabled
     *
     * @return int
     */
    public function isOrderCommentEnabled()
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_ORDER_COMMENT_ENABLED);
    }
	
	/**
	 * Check if checkout coupon form is enabled
	 *
	 * @return string
	 */
	public function isCheckoutCouponEnabled()
	{
		if ((int) Mage::getStoreConfig(self::XML_PATH_COUPON_FORM_ENABLED)) {
			return 'checkout/onepage/review/coupon.phtml';
		}
		
		return  '';
	}
}
