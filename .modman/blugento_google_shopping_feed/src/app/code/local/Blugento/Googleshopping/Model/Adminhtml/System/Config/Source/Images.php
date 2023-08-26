<?php

class Blugento_Googleshopping_Model_Adminhtml_System_Config_Source_Images
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
                array('value' => '', 'label' => Mage::helper('googleshopping')->__('Only Base Image')),
                array('value' => 'all', 'label' => Mage::helper('googleshopping')->__('All Images')),
            );
        }

        return $this->options;
    }
}