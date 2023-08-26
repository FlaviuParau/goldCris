<?php

class Blugento_Googleshopping_Block_Adminhtml_System_Config_Form_Field_Note
    extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return sprintf(
            '<tr id="row_%s"><td colspan="5" class="label" style="margin-bottom: 10px;">%s</td></tr>',
            $element->getHtmlId(),
            $element->getLabel()
        );
    }
}
