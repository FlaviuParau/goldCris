<?php

/**
 * HTML select element block with country options
 */
class Blugento_Pdf_Block_Adminhtml_System_Config_Form_Field_Notes_Country
    extends Mage_Core_Block_Html_Select
{

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if ( ! $this->getOptions()) {
            $countryModel = Mage::getModel('adminhtml/system_config_source_country');
            foreach ($countryModel->toOptionArray() as $country) {
                $this->addOption($country['value'], $country['label']);
            }
        }

        return parent::_toHtml();
    }

}
