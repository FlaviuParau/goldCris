<?php

class Blugento_GdprCookies_Block_Adminhtml_System_Config_Form_Field_Select_Scriptasync extends Mage_Core_Block_Html_Select
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

            $scriptAsyncs = array(
                array('value' => 'empty', 'label' => 'Empty'),
                array('value' => 'async', 'label' => 'Async'),
                array('value' => 'defer', 'label' => 'Defer')
            );

            foreach ($scriptAsyncs as $scriptAsync) {

                $this->addOption($scriptAsync['value'], $scriptAsync['label']);
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
