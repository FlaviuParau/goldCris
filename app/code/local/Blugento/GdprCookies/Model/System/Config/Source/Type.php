<?php

/**
 * Google Analytics system config source type
 *
 * @category   Mage
 * @package    Mage_GoogleAnalytics
 */
class Blugento_GdprCookies_Model_System_Config_Source_Type extends Mage_GoogleAnalytics_Model_System_Config_Source_Type
{
    /**
     * Get available options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage_GoogleAnalytics_Helper_Data::TYPE_UNIVERSAL,
                'label' => Mage::helper('googleanalytics')->__('Universal Analytics')
            ),
            array(
                'value' => Mage_GoogleAnalytics_Helper_Data::TYPE_ANALYTICS,
                'label' => Mage::helper('googleanalytics')->__('Google Analytics')
            ),
            array(
                'value' => 'analytics4',
                'label' => Mage::helper('googleanalytics')->__('Google Analytics 4')
            )
        );
    }
}
