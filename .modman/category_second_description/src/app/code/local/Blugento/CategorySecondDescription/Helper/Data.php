<?php

class Blugento_CategorySecondDescription_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLED = 'blugento_categoryseconddescription/general/enable';

    /**
     * Check if module is enabled
     *
     * @return int
     */
    public function isEnabled()
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_ENABLED);
    }

}