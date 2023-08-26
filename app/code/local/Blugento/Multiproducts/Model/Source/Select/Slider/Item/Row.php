<?php
class Blugento_Multiproducts_Model_Source_Select_Slider_Item_Row
{
    public function toOptionArray()
    {
        return array(
            array(
                'label' => Mage::helper('multiproducts')->__('6 Items'),
                'value' => 6
            ),
            array(
                'label' => Mage::helper('multiproducts')->__('5 Items'),
                'value' => 5
            ),
            array(
                'label' => Mage::helper('multiproducts')->__('4 Items'),
                'value' => 4
            ),
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
