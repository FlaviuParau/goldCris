<?php

class Blugento_Storepickup_Block_Onepage_Shipping_Method_Available extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
    /**
     * Get shipping method title.
     *
     * @param string $carrierCode
     * @return string mixed
     */
    public function getCarrierName($carrierCode)
    {
        if ($carrierCode == 'storepickup') {
            return Mage::getStoreConfig('carriers/blugento_storepickup/method_title');
        } else {
            if ($name = Mage::getStoreConfig('carriers/' . $carrierCode . '/title')) {
                return $name;
            }
            return $carrierCode;
        }
    }
}