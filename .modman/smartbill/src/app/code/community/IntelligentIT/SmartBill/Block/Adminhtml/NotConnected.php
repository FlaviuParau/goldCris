<?php

class IntelligentIT_SmartBill_Block_Adminhtml_NotConnected extends Mage_Adminhtml_Block_System_Config_Form_Field
{
  const ERROR = '<span style="color: #df280a;">Nu sunteti conectat la Smart Bill Cloud.<br/>Accesati sectiunea Smart Bill > <a href="%s" style="color: #df280a;">Autentificare</a>.<br/>Conectati-va cu datele contului de Smart Bill Cloud.</span><br/><br/>Nu aveti cont Smart Bill Cloud?<br/>Inregistrati-va GRATUIT <a href="https://cloud.smartbill.ro/inregistrare-cont/" target="_blank" rel="noopener">aici</a>.';
  const JS = "<script type=\"text/javascript\">
  setTimeout(function() {
    var hideSections = ['row_settings_companydata_company', 'settings_vatsettingsdata', 'settings_docssettingsdata', 'settings_extrasettingsdata'];
    for(i in hideSections) {
      try {
        document.getElementById(hideSections[i]).innerHTML = '';
        Fieldset.applyCollapse(hideSections[i]);
        Fieldset.toggleCollapse(hideSections[i]);
      } catch (e) {}
    }

    // hide save button
    document.getElementsByClassName('save')[0].style = 'display:none';
  }, 100);
  </script>";
  const CSS = "<style type=\"text/css\">
  #row_settings_companydata_message_not_connected .scope-label {
    display: none !important;
  }
  </style>";
   /**
    * Returns html part of the setting
    *
    * @param Varien_Data_Form_Element_Abstract $element
    * @return string
    */
   protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
   {
      $errorMsg = str_replace('%s', Mage::helper("adminhtml")->getUrl("adminhtml/system_config/edit", array('section'=>'connect')), self::ERROR);

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

      return ($isSuccess ? '' : $errorMsg.self::JS).self::CSS;
   }
}
