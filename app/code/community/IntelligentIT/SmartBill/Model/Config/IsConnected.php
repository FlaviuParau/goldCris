<?php

// include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'ObserverLogin.php';

class IntelligentIT_SmartBill_Model_Config_IsConnected extends Mage_Core_Model_Config_Data
{
    /**
     * Fills the select field with values
     * 
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();

        // step 1
        $token = Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_TOKEN);
        $options[(int) !empty($token)] = !empty($token);
        // step2 (check login)
        if (!empty($token)) {
            $settingsResponse = Mage::helper('smartbill/Public')->curl(IntelligentIT_SmartBill_Helper_Public::SETTINGS_URL, null, array("Content-Type: application/json; charset=utf-8","Accept:application/json","Authorization: ECSDigest ".Mage::helper('smartbill/Public')->getAuthorization()), false, true, false);
            if (empty($settingsResponse)) {
                $options = array(0 => 0);

                // clear token
                Mage::getModel('core/config')->saveConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_TOKEN, '');
            }
        }

        // $options[0] = 0;
        return $options;
    }
}