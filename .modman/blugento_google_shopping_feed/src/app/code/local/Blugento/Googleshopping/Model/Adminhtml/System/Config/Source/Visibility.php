<?php

class Blugento_Googleshopping_Model_Adminhtml_System_Config_Source_Visibility
{

    /**
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
                array('value' => '1', 'label' => Mage::helper('adminhtml')->__('Not Visible Individually')),
                array('value' => '2', 'label' => Mage::helper('adminhtml')->__('Catalog')),
                array('value' => '3', 'label' => Mage::helper('adminhtml')->__('Search')),
                array('value' => '4', 'label' => Mage::helper('adminhtml')->__('Catalog, Search')),
            );
        }

        return $this->options;
    }

}