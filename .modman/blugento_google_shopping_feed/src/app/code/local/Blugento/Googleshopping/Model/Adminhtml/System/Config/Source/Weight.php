<?php

class Blugento_Googleshopping_Model_Adminhtml_System_Config_Source_Weight
{

    /**
     * Options array
     *
     * @var array
     */
    public $options = null;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = array(
                array('value' => 'lb', 'label' => Mage::helper('adminhtml')->__('Pounds (lb)')),
                array('value' => 'oz', 'label' => Mage::helper('adminhtml')->__('Ounces (oz)')),
                array('value' => 'g', 'label' => Mage::helper('adminhtml')->__('Grams (g)')),
                array('value' => 'kg', 'label' => Mage::helper('adminhtml')->__('Kilograms (kg)')),
            );
        }

        return $this->options;
    }
}