<?php

class IntelligentIT_SmartBill_Block_Adminhtml_Logo extends Mage_Adminhtml_Block_System_Config_Form_Field
{
  const LOGO_FILENAME = 'smartbill/logo.png';
   /**
    * Returns html part of the setting
    *
    * @param Varien_Data_Form_Element_Abstract $element
    * @return string
    */
   protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
   {
        return '<img src="'.Mage::getBaseUrl('media').self::LOGO_FILENAME.'" style="width:280px;" />';
   }
}
