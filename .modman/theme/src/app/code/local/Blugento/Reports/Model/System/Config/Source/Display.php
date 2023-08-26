<?php

class Blugento_Reports_Model_System_Config_Source_Display
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $array = array(
            array(
                'value' => Blugento_Reports_Helper_Data::DISPLAY_TYPE_SLIDER,
                'label' => Mage::helper('blugento_reports')->__('Yes')
            ),
            array(
                'value' => Blugento_Reports_Helper_Data::DISPLAY_TYPE_STANDARD,
                'label' => Mage::helper('blugento_reports')->__('No')
            )
        );
        return $array;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $options = array(
           Blugento_Reports_Helper_Data::DISPLAY_TYPE_STANDARD => 'Standard',
           Blugento_Reports_Helper_Data::DISPLAY_TYPE_SLIDER   => 'Slider'
        );
        return $options;
    }
}
