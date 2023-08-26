<?php

class Blugento_Googleshopping_Model_Adminhtml_System_Config_Source_Conditions
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
                array(
                    'value' => '',
                    'label' => Mage::helper('googleshopping')->__('')
                ),
                array(
                    'value' => 'eq',
                    'label' => Mage::helper('googleshopping')->__('Equal')
                ),
                array(
                    'value' => 'neq',
                    'label' => Mage::helper('googleshopping')->__('Not equal')
                ),
                array(
                    'value' => 'gt',
                    'label' => Mage::helper('googleshopping')->__('Greater than')
                ),
                array(
                    'value' => 'gteq',
                    'label' => Mage::helper('googleshopping')->__('Greater than or equal to')
                ),
                array(
                    'value' => 'lt',
                    'label' => Mage::helper('googleshopping')->__('Less than')
                ),
                array(
                    'value' => 'lteg',
                    'label' => Mage::helper('googleshopping')->__('Less than or equal to')
                ),
                array(
                    'value' => 'in',
                    'label' => Mage::helper('googleshopping')->__('In')
                ),
                array(
                    'value' => 'nin',
                    'label' => Mage::helper('googleshopping')->__('Not in')
                ),
                array(
                    'value' => 'like',
                    'label' => Mage::helper('googleshopping')->__('Like')
                ),
                array(
                    'value' => 'nlike',
                    'label' => Mage::helper('googleshopping')->__('Like')
                ),
                array(
                    'value' => 'empty',
                    'label' => Mage::helper('googleshopping')->__('Empty')
                ),
                array(
                    'value' => 'not-empty',
                    'label' => Mage::helper('googleshopping')->__('Not Empty')
                ),
                array(
                    'value' => 'finset',
                    'label' => Mage::helper('googleshopping')->__('In Set')
                ),
            );
        }

        return $this->options;
    }
}