<?php

class IntelligentIT_SmartBill_Model_Config_WarehouseSelectionOptions extends Mage_Core_Model_Config_Data
{
    /**
     * Fills the select field with values
     * 
     * @return array
     */
    public function toOptionArray()
    {
        $nowValue = Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_WAREHOUSE);
        $found = false;

        $options = array();
        $settingsResponse = Mage::helper('smartbill/Public')->curl(IntelligentIT_SmartBill_Helper_Public::SETTINGS_URL, null, array("Content-Type: application/json; charset=utf-8","Accept:application/json","Authorization: ECSDigest ".Mage::helper('smartbill/Public')->getAuthorization()), false, true, false);

        if (!empty($settingsResponse)) {
            $settingsResponse = json_decode($settingsResponse);
            $company = Mage::helper('smartbill/Public')->getCompanyDetailsByVatCode($settingsResponse->companies);

            if (!empty($company->warehouses)
             && is_array($company->warehouses)) {
                foreach ($company->warehouses as $key => $value) {
                    // $options[$value->symbol] = $value->symbol.' - '.$value->name;
                    $options[$value] = $value;

                    Mage::helper('smartbill/Public')->_updateCompareValue($nowValue, $value, $found);
                }
            }
        }

        // $options[9999] = 'Fara gestiune';
        // TODO: fix from server API
        if ( empty($options) ) {
            $options['Fara gestiune'] = 'Fara gestiune';
            // force autoselect
            Mage::getModel('core/config')->saveConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_WAREHOUSE, 'Fara gestiune');
            $found = true;
        }
        
        Mage::helper('smartbill/Public')->_updateOptionsWithNotFound($options, $found, 'gestiune');

        return $options;
    }
}
