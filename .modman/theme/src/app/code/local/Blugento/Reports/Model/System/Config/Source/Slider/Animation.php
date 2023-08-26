<?php

class Blugento_Reports_Model_System_Config_Source_Slider_Animation
{
    public function toOptionArray()
    {
        return array(
	        array(
		        'label' => Mage::helper('blugento_reports')->__('6000 ms'),
		        'value' => 6000
	        ),
	        array(
		        'label' => Mage::helper('blugento_reports')->__('4000 ms'),
		        'value' => 4000
	        ),
	        array(
		        'label' => Mage::helper('blugento_reports')->__('3000 ms'),
		        'value' => 3000
	        ),
	        array(
		        'label' => Mage::helper('blugento_reports')->__('2000 ms'),
		        'value' => 2000
	        ),
	        array(
		        'label' => Mage::helper('blugento_reports')->__('1000 ms'),
		        'value' => 1000
	        ),
            array(
                'label' => Mage::helper('blugento_reports')->__('500 ms'),
                'value' => 500
            ),
            array(
                'label' => Mage::helper('blugento_reports')->__('400 ms'),
                'value' => 400
            ),
            array(
                'label' => Mage::helper('blugento_reports')->__('300 ms'),
                'value' => 300
            ),
            array(
                'label' => Mage::helper('blugento_reports')->__('200 ms'),
                'value' => 200
            ),
            array(
                'label' => Mage::helper('blugento_reports')->__('100 ms'),
                'value' => 100
            )
        );
    }
}
