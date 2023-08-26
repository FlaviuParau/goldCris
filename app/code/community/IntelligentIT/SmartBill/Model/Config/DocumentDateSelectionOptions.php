<?php

class IntelligentIT_SmartBill_Model_Config_DocumentDateSelectionOptions extends Mage_Core_Model_Config_Data
{
    const CURRENT_DATE = 0;
    const DOCUMENT_DATE = 1;

    public function toOptionArray()
    {
        return array(            
            self::CURRENT_DATE => Mage::helper('smartbill')->__('Data curenta'),
            self::DOCUMENT_DATE => Mage::helper('smartbill')->__('Data comanda'),
        );
    }    
}
