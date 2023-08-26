<?php

class Blugento_Googleshopping_Model_Adminhtml_System_Config_Source_Categorytype
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
                array('value' => 'include', 'label' => Mage::helper('googleshopping')->__('Include by Category')),
                array('value' => 'exclude', 'label' => Mage::helper('googleshopping')->__('Exclude by Category')),
            );
        }

        return $this->options;
    }
}