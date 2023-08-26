<?php

class Blugento_GdprCookies_Block_Adminhtml_System_Config_Form_Field_Select_Option extends Mage_Core_Block_Html_Select
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

            $categories = array(
                array('value' => 1, 'label' => 'Checked'),
                array('value' => 2, 'label' => 'Unchecked'),
                array('value' => 3, 'label' => 'Checked Read-Only'),
                array('value' => 4, 'label' => 'Disabled(Hidden)'),
            );

            foreach ($categories as $category) {

                $this->addOption($category['value'], $category['label']);
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
