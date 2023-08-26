<?php

class Blugento_Googleshopping_Model_Resource_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{

    /**
     * @return bool
     */
    public function isEnabledFlat()
    {
        $storeId = $this->getStoreId();
        if (Mage::getStoreConfig('googleshopping/generate/bypass_flat', $storeId)) {
            return false;
        }

        if (!isset($this->_flatEnabled[$storeId])) {
            if (version_compare(Mage::getVersion(), '1.8', '>=')) {
                $flatHelper = $this->getFlatHelper();
                $this->_flatEnabled[$storeId] = $flatHelper->isAvailable() && $flatHelper->isBuilt($storeId);
            } else {
                $this->_flatEnabled[$storeId] = $this->getFlatHelper()->isEnabled($storeId);
            }
        }

        return $this->_flatEnabled[$storeId];
    }

}
