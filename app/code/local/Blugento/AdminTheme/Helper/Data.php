<?php

class Blugento_AdminTheme_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Return product old price
     *
     * @param array $itemData
     * @return string
     */
    public function getOldPrice($itemData)
    {
        $id = $itemData['product_id'];

        if (Mage::helper('core')->isModuleEnabled('MindMagnet_Configurable')
            && $itemData['product_type'] == 'configurable')
        {
            $id = Mage::getModel('catalog/product')->getIdBySku($itemData['sku']);
        }

        return Mage::getModel('catalog/product')->load($id)->getPrice();
    }

    /**
     * Check if item weight should be displayed on order page
     *
     * @return int
     */
    public function displayWeightInOrderPage()
    {
        return Mage::getStoreConfig('sales/general/display_weight_on_order');
    }
}
