<?php
class Blugento_Multiproducts_Model_Source_Select_Slider_Animation
{
    public function toOptionArray()
    {
        return array(
            array(
                'label'      => Mage::helper('multiproducts')->__('500 ms'),
                'value'      => 500
            ),
            array(
                'label'      => Mage::helper('multiproducts')->__('400 ms'),
                'value'      => 400
            ),
            array(
                'label'      => Mage::helper('multiproducts')->__('300 ms'),
                'sort_order' => 1,
                'value'      => 300
            ),
            array(
                'label'      => Mage::helper('multiproducts')->__('200 ms'),
                'value'      => 200
            ),
            array(
                'label'      => Mage::helper('multiproducts')->__('100 ms'),
                'value'      => 100
            )
        );
    }
}
