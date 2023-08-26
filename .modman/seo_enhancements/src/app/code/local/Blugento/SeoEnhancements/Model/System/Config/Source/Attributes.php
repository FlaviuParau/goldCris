<?php
class Blugento_SeoEnhancements_Model_System_Config_Source_Attributes
{
    public function toOptionArray()
    {
        $options = array();
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->addFieldToFilter('entity_type_id', array('eq' => 4));
        foreach ($attributes as $attribute) {
            if ($attribute->getFrontendLabel()) {
                $options[] = array('value' => $attribute->getFrontendLabel(), 'label' => $attribute->getFrontendLabel());
            }
        }
        return $options;
    }
}
