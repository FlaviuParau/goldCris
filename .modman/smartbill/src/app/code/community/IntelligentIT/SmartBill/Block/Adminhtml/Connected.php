<?php

class IntelligentIT_SmartBill_Block_Adminhtml_Connected extends Mage_Adminhtml_Block_System_Config_Form_Field
{
  const SUCCESS = '<strong style="color:#3d6611;">Conectare cu succes la Smart Bill Cloud</strong>';
  const ERROR   = '<strong style="color:#df280a;">Nu s-a reusit conectarea la Smart Bill Cloud.</strong><br/>Verificati numele de utilizator si parola';
   /**
    * Returns html part of the setting
    *
    * @param Varien_Data_Form_Element_Abstract $element
    * @return string
    */
   protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
   {
      // step 1
      $token = Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_TOKEN);
      $isSuccess = !empty($token);
      // step 2
      if ($isSuccess) {
          $settingsResponse = Mage::helper('smartbill/Public')->curl(IntelligentIT_SmartBill_Helper_Public::SETTINGS_URL, null, array("Content-Type: application/json; charset=utf-8","Accept:application/json","Authorization: ECSDigest ".Mage::helper('smartbill/Public')->getAuthorization()), false, true, false);
          if (empty($settingsResponse)) {
            $isSuccess = false;

            // clear token
            Mage::getModel('core/config')->saveConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_TOKEN, '');
          }
      }

      return $isSuccess ? self::SUCCESS : self::ERROR;
   }
}
