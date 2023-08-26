<?php
class Blugento_Multiproducts_Model_Source_Select_Mode
{
    public function toOptionArray()
    {
        return array(
            array(
                'label' => Mage::helper('multiproducts')->__('Products'),
                'value' => 1
            )
        );
    }
}
