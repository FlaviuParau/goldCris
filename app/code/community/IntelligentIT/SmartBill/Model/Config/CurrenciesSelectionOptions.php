<?php

class IntelligentIT_SmartBill_Model_Config_CurrenciesSelectionOptions extends Mage_Core_Model_Config_Data
{
    /**
     * Fills the select field with values
     * 
     * @return array
     */
    public function toOptionArray()
    {
        $nowValue = Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_DOCUMENT_CURRENCY);
        $found = false;

        $options = array();
        $settingsResponse = Mage::helper('smartbill/Public')->curl(IntelligentIT_SmartBill_Helper_Public::SETTINGS_URL, null, array("Content-Type: application/json; charset=utf-8","Accept:application/json","Authorization: ECSDigest ".Mage::helper('smartbill/Public')->getAuthorization()), false, true, false);

        if (!empty($settingsResponse)) {
            $settingsResponse = json_decode($settingsResponse);
            $company = Mage::helper('smartbill/Public')->getCompanyDetailsByVatCode($settingsResponse->companies);

            if (!empty($company->currencies)
             && is_array($company->currencies)) {
                foreach ($company->currencies as $key => $value) {
                    $options[$value->symbol] = $value->symbol.' - '.$value->name;

                    Mage::helper('smartbill/Public')->_updateCompareValue($nowValue, $value->symbol, $found);
                }
            }
        }

        Mage::helper('smartbill/Public')->_updateOptionsWithNotFound($options, $found, 'moneda');

        return $options;
    }
}
