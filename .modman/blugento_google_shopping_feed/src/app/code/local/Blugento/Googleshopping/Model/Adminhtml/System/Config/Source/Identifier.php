<?php

class Blugento_Googleshopping_Model_Adminhtml_System_Config_Source_Identifier
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
                array('value' => '', 'label' => Mage::helper('adminhtml')->__('Deactivate')),
                array('value' => '1', 'label' => Mage::helper('adminhtml')->__('Activate when less than two')),
                array('value' => '2', 'label' => Mage::helper('adminhtml')->__('Activate')),
            );
        }

        return $this->options;
    }

}