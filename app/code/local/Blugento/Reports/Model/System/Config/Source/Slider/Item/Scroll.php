<?php

class Blugento_Reports_Model_System_Config_Source_Slider_Item_Scroll
{
    public function toOptionArray()
    {
        return array(
            array(
                'label' => Mage::helper('blugento_reports')->__('3 Items'),
                'value' => 3
            ),
            array(
                'label' => Mage::helper('blugento_reports')->__('2 Items'),
                'value' => 2
            ),
            array(
                'label' => Mage::helper('blugento_reports')->__('1 Item'),
                'value' => 1
            )
        );
    }
}
