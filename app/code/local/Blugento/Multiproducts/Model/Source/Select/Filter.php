<?php
class Blugento_Multiproducts_Model_Source_Select_Filter
{
    public function toOptionArray()
    {
        return array(
            array(
                'label' => Mage::helper('multiproducts')->__('Default'),
                'value' => 1
            ),
            array(
                'label' => Mage::helper('multiproducts')->__('New products first'),
                'value' => 2
            )
        );
    }
}
