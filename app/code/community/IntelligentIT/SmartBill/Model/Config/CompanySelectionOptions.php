<?php

class IntelligentIT_SmartBill_Model_Config_CompanySelectionOptions extends Mage_Core_Model_Config_Data
{
    /**
     * Fills the select field with values
     * 
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $settingsResponse = Mage::helper('smartbill/Public')->curl(IntelligentIT_SmartBill_Helper_Public::SETTINGS_URL, null, array("Content-Type: application/json; charset=utf-8","Accept:application/json","Authorization: ECSDigest ".Mage::helper('smartbill/Public')->getAuthorization()), false, true, false);

        if (!empty($settingsResponse)) {
            $settingsResponse = json_decode($settingsResponse);

            if (!empty($settingsResponse->companies)
             && is_array($settingsResponse->companies)) {
                foreach ($settingsResponse->companies as $key => $value) {
                    $options[$value->vatCode] = urldecode($value->name);
                }
            }
        }

        return $options;
    }
}
