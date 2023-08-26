<?php

class Blugento_Googleshopping_Model_Adminhtml_System_Config_Source_Producttype
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
                array('value' => '', 'label' => Mage::helper('adminhtml')->__('No')),
                array('value' => 'full', 'label' => Mage::helper('adminhtml')->__('Full Category Path')),
                array('value' => 'last', 'label' => Mage::helper('adminhtml')->__('Only Deepest Categoy')),
            );
        }

        return $this->options;
    }

}