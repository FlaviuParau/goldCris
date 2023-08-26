<?php

class IntelligentIT_SmartBill_Model_Config_DocumentCurrencySelectionOptions extends Mage_Core_Model_Config_Data
{
    /**
     * Fills the select field with values
     * 
     * @return array
     */
    public function toOptionArray()
    {
        $options = array(
            '9998' => 'Moneda produselor',
            'RON' => 'RON - Leu',
        );

        return $options;
    }
}
