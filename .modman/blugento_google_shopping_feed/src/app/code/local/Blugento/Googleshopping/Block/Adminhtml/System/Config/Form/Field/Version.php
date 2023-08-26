<?php

class Blugento_Googleshopping_Block_Adminhtml_System_Config_Form_Field_Version
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return mixed
     */
    public function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $modules = Mage::getConfig()->getNode('modules')->children();
        $modulesArray = (array)$modules;
        $element->setValue($modulesArray['Blugento_Googleshopping']->version);
        return parent::_getElementHtml($element);
    }

}