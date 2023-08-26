<?php

class Blugento_Billing_Model_System_Config_Source_Attributeclass
{
    public function toOptionArray()
    {
        return array(
            Blugento_Billing_Helper_Data::HIDDEN_ATTRIBUTE   => Mage::helper('blugento_billing')->__('No'),
            Blugento_Billing_Helper_Data::OPTIONAL_ATTRIBUTE => Mage::helper('blugento_billing')->__('Optional'),
            Blugento_Billing_Helper_Data::REQUIRED_ATTRIBUTE => Mage::helper('blugento_billing')->__('Required')
        );
    }
}
