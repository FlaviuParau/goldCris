<?php

class Blugento_Googleshopping_Model_Adminhtml_System_Config_Source_Pricemodel
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
                array('value' => '', 'label' => Mage::helper('adminhtml')->__('Use default price')),
                array('value' => 'min', 'label' => Mage::helper('adminhtml')->__('Use minimum price')),
                array('value' => 'max', 'label' => Mage::helper('adminhtml')->__('Use maximum price')),
                array('value' => 'total', 'label' => Mage::helper('adminhtml')->__('Use total price')),
            );
        }

        return $this->options;
    }
}