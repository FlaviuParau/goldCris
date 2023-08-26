<?php

class Blugento_Googleshopping_Model_Adminhtml_System_Config_Source_Status
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
                array('value' => '1', 'label' => Mage::helper('adminhtml')->__('Enabled')),
                array('value' => '2', 'label' => Mage::helper('adminhtml')->__('Disabled')),
            );
        }

        return $this->options;
    }

}