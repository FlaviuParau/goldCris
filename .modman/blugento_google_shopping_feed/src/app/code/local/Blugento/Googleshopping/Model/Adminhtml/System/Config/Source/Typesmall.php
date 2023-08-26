<?php

class Blugento_Googleshopping_Model_Adminhtml_System_Config_Source_Typesmall
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
                array('value' => 'fixed', 'label' => Mage::helper('googleshopping')->__('Static Values')),
                array('value' => 'attribute', 'label' => Mage::helper('googleshopping')->__('Use Attribute')),
            );
        }

        return $this->options;
    }

}