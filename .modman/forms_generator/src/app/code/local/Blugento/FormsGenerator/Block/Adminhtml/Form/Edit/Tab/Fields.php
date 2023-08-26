<?php
/**
 *
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
 * @package     Blugento_FormsGenerator
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_FormsGenerator_Block_Adminhtml_Form_Edit_Tab_Fields extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('formsgenerator_form', array('legend' => Mage::helper('blugento_formsgenerator')->__('Field Information')));
        $this->_addElementTypes($fieldset);

        $labelMessage = Mage::helper('blugento_formsgenerator')->__('Insert the label field that will be displayed on form.');

        $fieldset->addField(
            'field_label',
            'text',
            array(
                'label' => Mage::helper('blugento_formsgenerator')->__('Field Label'),
                'name' => 'field_label',
                'after_element_html' => $labelMessage
            )
        );

        $typeMessage = Mage::helper('blugento_formsgenerator')->__('Choose field type.');

        $fieldset->addField(
            'field_type',
            'select',
            array(
                'label' => Mage::helper('blugento_formsgenerator')->__('Field Type'),
                'name' => 'field_type',
                'values' => Mage::getSingleton('blugento_formsgenerator/system_config_source_inputs')->getOptionArray(),
                'onchange' => "displayAdminFields()",
                'after_element_html' => $typeMessage
            )
        );

        if(Mage::getStoreConfig('formsgenerator/general/comment')) {
            $commentMessage = Mage::helper('blugento_formsgenerator')->__('Insert a note/comment for the field.');

            $fieldset->addField(
                'field_comment',
                'text',
                array(
                    'label' => Mage::helper('blugento_formsgenerator')->__('Comment'),
                    'name' => 'field_comment',
                    'after_element_html' => $commentMessage
                )
            );
        }

        $defaultValueMessage = Mage::helper('blugento_formsgenerator')->__('Optional: Insert a value to be completed by default.');

        $fieldset->addField(
            'default_value',
            'text',
            array(
                'label' => Mage::helper('blugento_formsgenerator')->__('Default Value'),
                'name' => 'default_value',
                'after_element_html' => $defaultValueMessage
            )
        );

        $multipleValuesMessage = Mage::helper('blugento_formsgenerator')->__('Insert all values for this type of field separated by comma.');

        $fieldset->addField(
            'multiple_values',
            'textarea',
            array(
                'label' => Mage::helper('blugento_formsgenerator')->__('Values'),
                'name' => 'multiple_values',
                'after_element_html' => $multipleValuesMessage
            )
        );

        $selectedValueMessage = Mage::helper('blugento_formsgenerator')->__('Optional: Insert a default value to be selected. Must be in values list.');

        $fieldset->addField(
            'selected_value',
            'text',
            array(
                'label' => Mage::helper('blugento_formsgenerator')->__('Selected Value'),
                'name' => 'selected_value',
                'after_element_html' => $selectedValueMessage
            )
        );

        $placeholderMessage = Mage::helper('blugento_formsgenerator')->__('Optional: Insert a placeholder.');

        $fieldset->addField(
            'field_placeholder',
            'text',
            array(
                'label' => Mage::helper('blugento_formsgenerator')->__('Placeholder'),
                'name' => 'field_placeholder',
                'after_element_html' => $placeholderMessage
            )
        );

        $requiredMessage = Mage::helper('blugento_formsgenerator')->__('Choose if required or not.');

        $fieldset->addField(
            'field_required',
            'select',
            array(
                'label' => Mage::helper('blugento_formsgenerator')->__('Required'),
                'name' => 'field_required',
                'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
                'after_element_html' => $requiredMessage
            )
        );

        $fieldset->addField(
            'field_checked',
            'select',
            array(
                'label' => Mage::helper('blugento_formsgenerator')->__('Checked by default'),
                'name' => 'field_checked',
                'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
            )
        );

        if(Mage::getStoreConfig('formsgenerator/general/multiple')) {
            $fieldset->addField(
                'multiple_file',
                'select',
                array(
                    'label' => Mage::helper('blugento_formsgenerator')->__('Allow Multiple Files Upload'),
                    'name' => 'multiple_file',
                    'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
                )
            );
        }

        $fieldset->addField(
            'insert_field_btn',
            'button',
            array(
                'value' => Mage::helper('core')->__('Add Field'),
                'name'  => 'insert_field_btn',
                'class' => 'form-button',
                'onclick' => "getFieldCode()",
        ));

        $codeMessage = Mage::helper('blugento_formsgenerator')->__('This is a code preview. Here you can edit or delete some parts of the code. Atention! All the fields are added one after another and at some point you may have to switch the position of some fields!');

        $fieldset->addField(
            'fields_code',
            'textarea',
            array(
                'name'   => 'fields_code',
                'label'  => Mage::helper('blugento_formsgenerator')->__('Form Code'),
                'title'  => Mage::helper('blugento_formsgenerator')->__('Form Code'),
                'required' => false,
                'style'  => 'width:700px; height:500px;',
                'after_element_html' => $codeMessage
            )
        );

        if (Mage::getSingleton('adminhtml/session')->getFormsGeneratorData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getFormsGeneratorData());
            Mage::getSingleton('adminhtml/session')->setFormsGeneratorData(null);
        } elseif (Mage::registry('formsgenerator_data')) {
            $form->addValues(Mage::registry('formsgenerator_data')->getData());
        }
        return parent::_prepareForm();
    }
}


