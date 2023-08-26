<?php

class IntelligentIT_SmartBill_Model_Config_TransportVATSelectionOptions extends Mage_Core_Model_Config_Data
{
    /**
     * Fills the select field with values
     * 
     * @return array
     */
    public function toOptionArray()
    {
        $nowValue = Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_TRANSPORT_VAT);
        $found = false;

        $options = array();
        $settingsResponse = Mage::helper('smartbill/Public')->curl(IntelligentIT_SmartBill_Helper_Public::SETTINGS_URL, null, array("Content-Type: application/json; charset=utf-8","Accept:application/json","Authorization: ECSDigest ".Mage::helper('smartbill/Public')->getAuthorization()), false, true, false);

        if (!empty($settingsResponse)) {
            $settingsResponse = json_decode($settingsResponse);
            $company = Mage::helper('smartbill/Public')->getCompanyDetailsByVatCode($settingsResponse->companies);

            if (!empty($company->taxes)
             && is_array($company->taxes)) {
                foreach ($company->taxes as $key => $value) {
                    $options[$value->id] = $value->name.' ('.$value->percentage.'%)';

                    Mage::helper('smartbill/Public')->_updateCompareValue($nowValue, $value->id, $found);
                }
            }
        }

        // force 
        uasort($options, array($this, 'customOrderVAT'));

        Mage::helper('smartbill/Public')->_updateOptionsWithNotFound($options, $found, 'cota TVA');

        return $options;
    }

    public function customOrderVAT($a, $b) {

        // if (strpos($a, '24%')!==false
        //  && strpos($b, '24%')!==false) {
        //     return 0;
        // }

        // return (strpos($a, '24%')!==false) ? -1 : 1;
        if (strpos($a, '20%')!==false
         && strpos($b, '20%')!==false) {
            return 0;
        }

        return (strpos($a, '20%')!==false) ? -1 : 1;
    }
}
