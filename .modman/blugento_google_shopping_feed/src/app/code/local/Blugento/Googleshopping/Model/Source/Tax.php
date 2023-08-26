<?php

class Blugento_Googleshopping_Model_Source_Tax
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
                array('value' => '', 'label' => Mage::helper('googleshopping')->__('No')),
                array('value' => 'incl', 'label' => Mage::helper('googleshopping')->__('Force including Tax')),
                array('value' => 'excl', 'label' => Mage::helper('googleshopping')->__('Force excluding Tax')),
            );
        }

        return $this->options;
    }

}