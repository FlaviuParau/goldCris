<?php

// include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'DocumentsController.php';

class IntelligentIT_SmartBill_Model_Config_VATSelectionOptions extends Mage_Core_Model_Config_Data
{
    /**
     * Fills the select field with values
     * 
     * @return array
     */
    public function toOptionArray()
    {
        $nowValue = Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_PRODUCTS_VAT);
        $found = false;

        $options = array();
        $settingsResponse = Mage::helper('smartbill/Public')->curl(IntelligentIT_SmartBill_Helper_Public::SETTINGS_URL, null, array("Content-Type: application/json; charset=utf-8","Accept:application/json","Authorization: ECSDigest ".Mage::helper('smartbill/Public')->getAuthorization()), false, true, false);

        if (!empty($settingsResponse)) {
            $settingsResponse = json_decode($settingsResponse);
            $company = Mage::helper('smartbill/Public')->getCompanyDetailsByVatCode($settingsResponse->companies);

            if (!empty($company->defaultVatCode)) {
                $defaultVAT = $company->defaultVatCode;
                $options[$defaultVAT] = $defaultVAT.'%';

                Mage::helper('smartbill/Public')->_updateCompareValue($nowValue, $defaultVAT, $found);
            }
        }
        $options[9998] = Mage::helper('smartbill')->__('Preluat din Magento, pe produse');
        $options[9999] = Mage::helper('smartbill')->__('Preluat din SmartBill, pe produse');

        Mage::helper('smartbill/Public')->_updateCompareValue($nowValue, 9998, $found);
        Mage::helper('smartbill/Public')->_updateCompareValue($nowValue, 9999, $found);
        Mage::helper('smartbill/Public')->_updateOptionsWithNotFound($options, $found, 'cota TVA');

        return $options;
    }
}
