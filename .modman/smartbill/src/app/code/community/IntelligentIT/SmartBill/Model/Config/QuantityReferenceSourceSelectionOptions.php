<?php

class IntelligentIT_SmartBill_Model_Config_QuantityReferenceSourceSelectionOptions extends Mage_Core_Model_Config_Data
{
    const ORDERED_VALUE = 0;
    const INVOICED_VALUE = 1;
    const SHIPPED_VALUE = 2;

    public function toOptionArray()
    {
        return array(            
            self::ORDERED_VALUE => Mage::helper('sales')->__('Ordered'),
            self::INVOICED_VALUE => Mage::helper('sales')->__('Invoiced'),
            self::SHIPPED_VALUE => Mage::helper('sales')->__('Shipped'),
        );
    }    
}
