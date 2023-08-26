<?php

class IntelligentIT_SmartBill_Model_Config_DocumentTypeSelectionOptions extends Mage_Core_Model_Config_Data
{
    const INVOICE_VALUE = 0;
    const PROFORMA_VALUE = 1;

    public function toOptionArray()
    {
        return array(            
            self::INVOICE_VALUE => Mage::helper('smartbill')->__('Factura'),
            self::PROFORMA_VALUE => Mage::helper('smartbill')->__('Proforma'),
        );
    }    
}
