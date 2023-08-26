<?php

class Blugento_Googleshopping_Model_Adminhtml_System_Config_Source_Condition
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
                array('value' => 'new', 'label' => Mage::helper('googleshopping')->__('New')),
                array('value' => 'refurbished', 'label' => Mage::helper('googleshopping')->__('Refurbished')),
                array('value' => 'used', 'label' => Mage::helper('googleshopping')->__('Used')),
            );
        }

        return $this->options;
    }

}