<?php

class Blugento_Googleshopping_Model_Adminhtml_System_Config_Source_Producttypes
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
                array('value' => '', 'label' => Mage::helper('googleshopping')->__('Simple & Parent Products')),
                array('value' => 'simple', 'label' => Mage::helper('googleshopping')->__('Only Simple Products')),
                array('value' => 'parent', 'label' => Mage::helper('googleshopping')->__('Only Parent Products')),

            );
        }

        return $this->options;
    }
}