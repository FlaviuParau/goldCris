<?php
/**
 * Blugento Marketing
 * Block Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Customfilters
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Marketing_Block_Checkout extends Mage_Core_Block_Template
{
    /**
     * Return script code for cart/checkout page
     *
     * @param string $page
     * @param int $storeId
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getScriptCode($page, $storeId)
    {
        $script = Mage::getStoreConfig('blugento_marketing/settings/' . $page, $storeId);

        $amount = Mage::getModel('checkout/cart')->getQuote()->getGrandTotal();
        $currency = Mage::app()->getStore()->getCurrentCurrencyCode();
        $items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();

        $ids = array();
        foreach ($items as $item) {
            $ids[] = $item->getProductId();
        }

        $productIds = implode(', ', $ids);

        $script = str_replace(array('{{value}}', '{{currency}}', '{{products}}'), array($amount, $currency, $productIds), $script);

        return $script;
    }
}
