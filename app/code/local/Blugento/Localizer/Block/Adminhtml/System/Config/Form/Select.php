<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @category    Blugento
 * @package     Blugento_Localizer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Localizer_Block_Adminhtml_System_Config_Form_Select extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);

        return $this->_getFieldHtml($element, null);
    }

    /**
     * Gets the html for a field
     *
     * @param $fieldset
     * @param $group
     * @return mixed
     */
    protected function _getFieldHtml($fieldset, $group)
    {
        $field = $fieldset->addField($fieldset->getId() . '_select', 'select', array(
            'name'          => $fieldset->getName(),
            'label'         => $fieldset->getLabel(),
            'value'         => $fieldset->getValue(),
            'values'        => $fieldset->getValues(),
            'can_use_default_value' => false,
            'can_use_website_value' => false,
            'after_element_html' => '<style>#blugentolocalizer_global_config > span > label {width: 210px;}</style>'
        ));

        return $field->toHtml();
    }
}
