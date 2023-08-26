<?php

class IntelligentIT_SmartBill_Model_Config_ProductAttributes extends Mage_Core_Model_Config_Data
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')->addVisibleFilter();

        $attributeArray[] = [
            'label' => '--Selectati--',
            'value' => ''
        ];

        foreach ($attributes as $attribute) {
            if ($attribute->getFrontendInput() == 'text' || $attribute->getFrontendInput() == 'textarea') {
                $attributeArray[] = [
                    'label' => $attribute->getData('frontend_label'),
                    'value' => $attribute->getData('attribute_code')
                ];
            }
        }
        return $attributeArray;
    }
}
