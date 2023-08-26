<?php
/**
 * Blugento Cart Settings
 * Block Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Cart
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Cart_Block_Product extends Mage_Catalog_Block_Product_View
{
    /**
     * Display or not
     * @return bool
     */
    public function displayCustomButton()
    {
        if (!Mage::getStoreConfig('blugento_cart/global_config/enable')) {
            return false;
        }
        if (Mage::getStoreConfig('blugento_cart/global_config/hide_all')) {
            return false;
        }

        return (bool) Mage::getStoreConfig('blugento_cart/global_config/custom_btn');
    }

    public function displayCustomButtonOutofstock()
    {
        if (!Mage::getStoreConfig('blugento_cart/global_config/enable')) {
            return false;
        }
        if (Mage::getStoreConfig('blugento_cart/global_config/hide_all')) {
            return false;
        }

        return (bool) Mage::getStoreConfig('blugento_cart/global_config/custom_btn_outofstock');
    }

    public function getCustomButtonText()
    {
        $text = Mage::getStoreConfig('blugento_cart/global_config/custom_btn_text');
        if (!$text) {
            $text = 'Request Product';
        }

        return $text;
    }

    public function getButtonURL()
    {
        $page = Mage::getStoreConfig('blugento_cart/global_config/cms_page');
        if (!$page) {
            return '#';
        }

        $product = $this->getProduct();
        return $this->getUrl($page, array('_query' => array('product' => $product->getId())));
    }
}
