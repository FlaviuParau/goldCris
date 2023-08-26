<?php

class Facebook_AdsExtension_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function validToRun()
    {
        $frequency = Mage::getStoreConfig('Facebook_AdsExtension/product_feed/frequency');
        $lastUpdate = Mage::getStoreConfig('Facebook_AdsExtension/product_feed/run_time');

        $currentTime = strtotime(now());
        if (!$lastUpdate) {
            return true;
        }
        $addReq = $frequency * 3600;
        $validToRunAt = strtotime($lastUpdate) + $addReq;
        if ($currentTime >= $validToRunAt) {
            return true;
        }

        return false;
    }

    /**
     * Return the product attributes
     *
     * @return array
     */
    public function getAttributeOptions()
    {
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection');

        $attributeArray[] = [
            'label' => Mage::helper('Facebook_AdsExtension')->__('--Please Select--'),
            'value' => ''
        ];

        foreach($attributes as $attribute){
            $attributeArray[] = [
                'value' => $attribute->getData('attribute_code'),
                'label' => $attribute->getData('frontend_label') . " ( " . $attribute->getData('attribute_code') . " )"
            ];
        }
        return $attributeArray;
    }

    /**
     * Return the attributes map
     */
    public function getAttributesMap()
    {
        $conf = Mage::getStoreConfig('Facebook_AdsExtension/product_feed/product_attributes');
        $mapValues = Mage::helper('core/unserializeArray')->unserialize($conf);

        $mapFields  = array();
        foreach ($mapValues as $value) {
            $magentoCode = isset($value['magento_code']) ? $value['magento_code'] : null;
            $feedCode    = (isset($value['feed_code']) && $value['feed_code'] !='') ? $value['feed_code'] : $value['magento_code'];

            if ($magentoCode) {
                $mapFields[$magentoCode]  = $feedCode;
            }
        }

        return $mapFields;
    }
}
