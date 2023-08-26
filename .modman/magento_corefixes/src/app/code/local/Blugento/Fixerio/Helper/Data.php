<?php
class Blugento_Fixerio_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Return API Access Key
     *
     * @return string
     */
    public function getAccessKey() {
        return Mage::getStoreConfig('currency/fixerio/access_key');
    }
}