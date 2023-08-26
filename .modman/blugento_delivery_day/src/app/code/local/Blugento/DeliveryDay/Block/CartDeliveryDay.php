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
 * @package     Blugento_DeliveryDay
 * @author      Marius Boia <marius.boia@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_DeliveryDay_Block_CartDeliveryDay extends Mage_Core_Block_Template
{
    public function getCartDeliveryDay($storeId, $difference = null)
    {
        $additionalDays = $this->getCartMinAdditionalDays($storeId);
        $helper = Mage::helper('delivery_day');

        return $helper->getDisplayDate($additionalDays, $storeId, $difference);
    }

    public function getDifference($storeId)
    {
        return (int)$this->getCartMaxAdditionalDays($storeId) - $this->getCartMinAdditionalDays($storeId);
    }

    private function getCartMinAdditionalDays($storeId)
    {
        $generalAdditionalDays = (int)Mage::getStoreConfig('delivery_day/general/min_days', $storeId);

        $cart = Mage::getModel('checkout/cart')->getQuote();
        $biggestDeliveryDay = 0;
        foreach ($cart->getAllItems() as $item) {
            $product = $item->getProduct();
            $productDeliveryDay = $product->getData('delivery_day_min');
            if ($productDeliveryDay && is_numeric($productDeliveryDay) && $productDeliveryDay > 0) {
                //nothing
            } else {
                $productDeliveryDay = $generalAdditionalDays;
            }
            if ($productDeliveryDay > $biggestDeliveryDay) {
                $biggestDeliveryDay = $productDeliveryDay;
            }
        }
        $additionalDays = $biggestDeliveryDay;

        return $additionalDays;
    }

    private function getCartMaxAdditionalDays($storeId)
    {
        $generalAdditionalDays = (int)Mage::getStoreConfig('delivery_day/general/max_days', $storeId);
        $cart = Mage::getModel('checkout/cart')->getQuote();
        $biggestDeliveryDay = 0;
        foreach ($cart->getAllItems() as $item) {
            $product = $item->getProduct();
            $productDeliveryDay = $product->getData('delivery_day_max');
            if ($productDeliveryDay && is_numeric($productDeliveryDay) && $productDeliveryDay > 0) {
                //nothing
            } else {
                $productDeliveryDay = $generalAdditionalDays;
            }
            if ($productDeliveryDay > $biggestDeliveryDay) {
                $biggestDeliveryDay = $productDeliveryDay;
            }

        }
        $additionalDays = $biggestDeliveryDay;

        return $additionalDays;
    }
}

