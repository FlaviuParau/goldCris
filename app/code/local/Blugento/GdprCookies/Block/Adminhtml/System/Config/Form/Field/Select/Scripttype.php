<?php

class Blugento_GdprCookies_Block_Adminhtml_System_Config_Form_Field_Select_Scripttype extends Mage_Core_Block_Html_Select
{
    /**
     * @param $value
     * @return mixed
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render Block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {

            $scriptTypes = array(
                array('value' => 'text/javascript', 'label' => 'text/javascript'),
                array('value' => 'application/javascript', 'label' => 'application/javascript'),
            );

            foreach ($scriptTypes as $scriptType) {

                $this->addOption($scriptType['value'], $scriptType['label']);
            }
        }

        return parent::_toHtml();
    }

    /**
     * Get Options of the Element
     *
     * @return array
     */
    public function getOptions()
    {
        $options = $this->_options;
        asort($options);

        return $options;
    }
}
