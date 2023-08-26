<?php

class Blugento_Googleshopping_Model_Adminhtml_System_Config_Source_Countries
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
            $countries = array();
            $countries[] = array('value' => '', 'label' => Mage::helper('googleshopping')->__('-- All Countries'));

            $source = Mage::getModel('adminhtml/system_config_source_country')->toOptionArray();
            unset($source[0]);

            $this->options = array_merge($countries, $source);
        }

        return $this->options;
    }
}