<?php

class Blugento_Googleshopping_Model_Adminhtml_System_Config_Source_Action
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
                array('value' => '', 'label' => Mage::helper('googleshopping')->__('-- None')),
                array('value' => 'strip_tags', 'label' => Mage::helper('googleshopping')->__('Strip Tags')),
                array('value' => 'round', 'label' => Mage::helper('googleshopping')->__('Remove trailing zeros')),
            );
        }

        return $this->options;
    }
}