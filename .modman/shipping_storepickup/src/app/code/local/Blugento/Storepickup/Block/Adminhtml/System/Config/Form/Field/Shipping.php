<?php

class Blugento_Storepickup_Block_Adminhtml_System_Config_Form_Field_Shipping
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function _prepareToRender()
    {
        $this->addColumn('title', array(
            'label' => Mage::helper('blugento_storepickup')->__('Title'),
            'style' => 'width: 150px'
        ));
        $this->addColumn('price', array(
            'label' => Mage::helper('blugento_storepickup')->__('Shipping<br>Price'),
            'style' => 'width: 60px'
        ));
        $this->addColumn('additional_price', array(
            'label' => Mage::helper('blugento_storepickup')->__('Additional<br>Price (%)'),
            'style' => 'width: 60px'
        ));
        $this->addColumn('address', array(
            'label' => Mage::helper('blugento_storepickup')->__('Address'),
            'style' => 'width: 150px'
        ));
        $this->addColumn('info', array(
            'label' => Mage::helper('blugento_storepickup')->__('Information'),
            'style' => 'width: 150px'
        ));
        $this->addColumn('sort_order', array(
            'label' => Mage::helper('blugento_storepickup')->__('Sort Order'),
            'style' => 'width: 40px'
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('blugento_storepickup')->__('Add Shipping Method');
    }
}