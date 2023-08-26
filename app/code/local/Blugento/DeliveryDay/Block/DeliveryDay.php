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

class Blugento_DeliveryDay_Block_DeliveryDay extends Mage_Catalog_Block_Product_View
{
    public function getDeliveryDay($product, $storeId, $difference = null)
    {
        $additionalDays = $this->getMinAdditionalDays($product, $storeId);
        $helper = Mage::helper('delivery_day');

        return $helper->getDisplayDate($additionalDays, $storeId, $difference);
    }

    public function getDifference($product, $storeId)
    {
        return (int)$this->getMaxAdditionalDays($product, $storeId) - $this->getMinAdditionalDays($product, $storeId);
    }

    private function getMinAdditionalDays($product, $storeId)
    {
        $additionalDays = (int)Mage::getStoreConfig('delivery_day/general/min_days', $storeId);
        $productDeliveryDay = $product->getData('delivery_day_min');
        if ($productDeliveryDay && is_numeric($productDeliveryDay) && $productDeliveryDay > 0) {
            $additionalDays = (int)$productDeliveryDay;
        }

        return $additionalDays;
    }

    private function getMaxAdditionalDays($product, $storeId)
    {
        $additionalDays = (int)Mage::getStoreConfig('delivery_day/general/max_days', $storeId);
        $productDeliveryDay = $product->getData('delivery_day_max');
        if ($productDeliveryDay && is_numeric($productDeliveryDay) && $productDeliveryDay > 0) {
            $additionalDays = (int)$productDeliveryDay;
        }

        return $additionalDays;
    }
}
