<?php

class Blugento_Googleshopping_Model_Source_Attribute
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $backendTypes = array(
            'text',
            'select',
            'textarea',
            'date',
            'int',
            'boolean',
            'static',
            'varchar',
            'decimal'
        );

        $optionArray = array();

        /** @var Mage_Catalog_Model_Resource_Product_Attribute_Collection $attributes */
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
            ->addVisibleFilter()
            ->addFieldToFilter('backend_type', $backendTypes);

        $optionArray[] = array(
            'label' => Mage::helper('googleshopping')->__('- Product ID'),
            'value' => 'entity_id'
        );
        $optionArray[] = array(
            'label' => Mage::helper('googleshopping')->__('- Final Price'),
            'value' => 'final_price'
        );

        foreach ($attributes as $attribute) {
            $optionArray[] = array(
                'label' => str_replace("'", "", $attribute->getData('frontend_label')),
                'value' => $attribute->getData('attribute_code')
            );
        }

        return $optionArray;
    }

}