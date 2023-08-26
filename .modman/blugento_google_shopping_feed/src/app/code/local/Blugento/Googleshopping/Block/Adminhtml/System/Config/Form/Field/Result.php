<?php

class Blugento_Googleshopping_Block_Adminhtml_System_Config_Form_Field_Result
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    /**
     * Return element html.
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    public function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return sprintf(
            '<tr id="row_%s"><td colspan="5" class="label" style="margin-bottom: 10px;"><span id="result"><span id="test_result"></span></span></td></tr>',
            $element->getHtmlId()
        );
    }

}