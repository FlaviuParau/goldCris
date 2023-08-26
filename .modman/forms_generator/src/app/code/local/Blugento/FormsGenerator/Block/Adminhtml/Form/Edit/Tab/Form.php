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

class Blugento_FormsGenerator_Block_Adminhtml_Form_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('formsgenerator_form', array('legend' => Mage::helper('blugento_formsgenerator')->__('Form Information')));
        $this->_addElementTypes($fieldset);

        $fieldset->addField(
            'form_name',
            'text',
            array(
                'label' => Mage::helper('blugento_formsgenerator')->__('Form Name'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'form_name'
            )
        );

        $fieldset->addField(
            'form_status',
            'select',
            array(
                'label' => Mage::helper('blugento_formsgenerator')->__('Status'),
                'name' => 'form_status',
                'values' => Mage::getModel('blugento_formsgenerator/system_config_source_status')->getOptionArray()
            )
        );

        $recipientMessage = Mage::helper('blugento_formsgenerator')->__('Insert the recipient\'s name.');

        $fieldset->addField(
            'recipient',
            'text',
            array(
                'label' => Mage::helper('blugento_formsgenerator')->__('Recipient'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'recipient',
                'after_element_html' => $recipientMessage
            )
        );

        $recipientEmailMessage = Mage::helper('blugento_formsgenerator')->__('Insert the recipient\'s e-mail.');

        $fieldset->addField(
            'recipient_email',
            'text',
            array(
                'label' => Mage::helper('blugento_formsgenerator')->__('Recipient Email'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'recipient_email',
                'after_element_html' => $recipientEmailMessage
            )
        );

        $fieldset->addField(
            'email_template_id',
            'select',
            array(
                'label' => Mage::helper('blugento_formsgenerator')->__('Email Template'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'email_template',
                'values' => Mage::getSingleton('adminhtml/system_config_source_email_template')->toOptionArray()
            )
        );

        $fieldset->addField(
            'success_page',
            'select',
            array(
                'label' => Mage::helper('blugento_formsgenerator')->__('Success Page'),
                'name' => 'success_page',
                'values' => Mage::getSingleton('blugento_formsgenerator/system_config_source_cms_page')->getOptionArray()
            )
        );

        $fieldset->addField(
            'store_id',
            'select',
            array(
                'label' => Mage::helper('blugento_formsgenerator')->__('Store'),
                'name' => 'store_id',
                'class' => 'required-entry',
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(true, false)
            )
        );


        if (Mage::getSingleton('adminhtml/session')->getFormsGeneratorData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getFormsGeneratorData());
            Mage::getSingleton('adminhtml/session')->setFormsGeneratorData(null);
        } elseif (Mage::registry('formsgenerator_data')) {
            $form->setValues(Mage::registry('formsgenerator_data')->getData());
        }
        return parent::_prepareForm();
    }
}


