<?php

class IntelligentIT_SmartBill_Model_Config_ProformaSeriesSelectionOptions extends Mage_Core_Model_Config_Data
{
    /**
     * Fills the select field with values
     * 
     * @return array
     */
    public function toOptionArray()
    {
        $nowValue = Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_PROFORMA_SERIES);
        $found = false;

        $options = array();
        $settingsResponse = Mage::helper('smartbill/Public')->curl(IntelligentIT_SmartBill_Helper_Public::SETTINGS_URL, null, array("Content-Type: application/json; charset=utf-8","Accept:application/json","Authorization: ECSDigest ".Mage::helper('smartbill/Public')->getAuthorization()), false, true, false);

        if (!empty($settingsResponse)) {
            $settingsResponse = json_decode($settingsResponse);
            $company = Mage::helper('smartbill/Public')->getCompanyDetailsByVatCode($settingsResponse->companies);

            if (!empty($company->estimateSeries)
             && is_array($company->estimateSeries)) {
                foreach ($company->estimateSeries as $key => $value) {
                    $options[$value] = $value;

                    Mage::helper('smartbill/Public')->_updateCompareValue($nowValue, $value, $found);
                }
            }
        }

        Mage::helper('smartbill/Public')->_updateOptionsWithNotFound($options, $found, 'seria');

        return $options;
    }
}
