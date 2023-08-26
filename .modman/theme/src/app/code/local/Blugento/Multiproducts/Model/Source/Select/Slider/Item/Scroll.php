<?php
class Blugento_Multiproducts_Model_Source_Select_Slider_Item_Scroll
{
    public function toOptionArray()
    {
        return array(
            array(
                'label' => Mage::helper('multiproducts')->__('3 Items'),
                'value' => 3
            ),
            array(
                'label' => Mage::helper('multiproducts')->__('2 Items'),
                'value' => 2
            ),
            array(
                'label' => Mage::helper('multiproducts')->__('1 Item'),
                'value' => 1
            )
        );
    }
}
