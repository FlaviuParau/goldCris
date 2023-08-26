<?php
/**
 * Blugento Cart Settings
 * Helper Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Cart
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Cart_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLED                  = 'blugento_cart/global_config/enable';
    const XML_PATH_HIDE_ALL                 = 'blugento_cart/global_config/hide_all';
    const XML_PATH_DISPLAY_BUTTON           = 'blugento_cart/global_config/custom_btn';
    const XML_PATH_DISPLAY_BUTTON_OUT_OF_STOCK = 'blugento_cart/global_config/custom_btn_outofstock';
    const XML_PATH_DISPLAY_BUTTON_REDIRECT  = 'blugento_cart/global_config/custom_btn_redirect';
    const XML_PATH_BUTTON_TEXT              = 'blugento_cart/global_config/custom_btn_text';
    const XML_PATH_CATEGORY_BUTTON_TEXT     = 'blugento_cart/global_config/custom_category_btn_text';
    const XML_PATH_BUTTON_PAGE              = 'blugento_cart/global_config/cms_page';
    const XML_PATH_DISPLAY_PRICE            = 'blugento_cart/global_config/hide_price';

    /**
     * Check if module is enabled
     *
     * @return int
     */
    public function isEnabled()
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_ENABLED);
    }

    public function getHideAll()
    {
        if (!$this->isEnabled()) {
            return false;
        }

        return (int) Mage::getStoreConfig(self::XML_PATH_HIDE_ALL);
    }

    public function getDisplayCustomButton()
    {
        if (!$this->isEnabled()) {
            return false;
        }

        return (int) Mage::getStoreConfig(self::XML_PATH_DISPLAY_BUTTON);
    }

    public function getDisplayCustomButtonOutofstock()
    {
        if (!$this->isEnabled()) {
            return false;
        }

        return (int) Mage::getStoreConfig(self::XML_PATH_DISPLAY_BUTTON_OUT_OF_STOCK);
    }

    public function getDisplayCustomBtnRedirect()
    {
        if (!$this->isEnabled()) {
            return false;
        }

        return (int) Mage::getStoreConfig(self::XML_PATH_DISPLAY_BUTTON_REDIRECT);
    }
	
	public function getDisplayPrice()
	{
		if (!$this->isEnabled()) {
			return false;
		}
		
		return (int) Mage::getStoreConfig(self::XML_PATH_DISPLAY_PRICE);
	}

    public function getCustomBtnText()
    {
        if (!$this->isEnabled()) {
            return '';
        }

        $text = Mage::getStoreConfig(self::XML_PATH_BUTTON_TEXT);

        if (!$text) {
            $text = 'Request Product';
        }

        return $text;
    }
	
	public function getCategoryCustomBtnText()
	{
		if (!$this->isEnabled()) {
			return '';
		}
		
		return Mage::getStoreConfig(self::XML_PATH_CATEGORY_BUTTON_TEXT);
	}

    public function getCustomBtnPage()
    {
        if (!$this->isEnabled()) {
            return '';
        }

        return Mage::getStoreConfig(self::XML_PATH_BUTTON_PAGE);
    }

    public function getButtonURL($productId)
    {
        $page = Mage::getStoreConfig('blugento_cart/global_config/cms_page');
        if (!$page) {
            return '#';
        }

        return Mage::getUrl($page, array('_query' => array('product' => $productId)));
    }
	
	public function getSuccessPageURL($productId)
	{
		$page = Mage::getStoreConfig('blugento_cart/global_config/cms_success_page');
		if (!$page) {
			return '#';
		}
		
		return $page . '/?product=' . $productId;
	}

    /**
     * Return shipping methods to display price in cart.
     *
     * @param int $storeId
     * @return string
     */
    public function getShippingMethodsCode($storeId)
    {
        return Mage::getStoreConfig('blugento_cart/shipping_price/shipping_methods', $storeId);
    }

    /**
     * Check if shipping price on cart is enabled.
     *
     * @param int $storeId
     * @return int
     */
    public function isShippingPriceCartEnabled($storeId)
    {
        return (int) Mage::getStoreConfig('blugento_cart/shipping_price/enabled', $storeId);
    }

    /**
	 * Check is custom config is enabled
	 *
	 * @return mixed
	 */
	public function isCustomConfigEnabled()
	{
		return Mage::getStoreConfig('blugento_cart/custom_config/enabled');
	}

    /**
	 * Get Attributes Information
	 *
	 * @return mixed
	 */
	public function attributesInformation()
	{
		return Mage::getStoreConfig('blugento_cart/custom_config/map_attributes');
	}
}
