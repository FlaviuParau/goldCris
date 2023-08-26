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
 * @package     Blugento_ProductMultitabs
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_ProductMultitabs_Block_Adminhtml_Multitabs_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('multitabs_form', array('legend' => Mage::helper('blugento_productmultitabs')->__('General')));
        $this->_addElementTypes($fieldset);

        $message = Mage::helper('blugento_productmultitabs')->__('Maximum 30 characters');
        $fieldset->addField(
            'name',
            'text',
            array(
                'label' => Mage::helper('blugento_productmultitabs')->__('Tab Name'),
                'class' => 'required-entry validate-length maximum-length-30',
                'required' => true,
                'name' => 'name',
                'after_element_html' => $message
            )
        );

        $fieldset->addField(
            'content',
            'select',
            array(
                'label' => Mage::helper('blugento_productmultitabs')->__('Content Type'),
                'name' => 'content',
                'onchange' => 'displayContentField()',
                'values' => Mage::getSingleton('blugento_productmultitabs/adminhtml_system_config_source_multitabs_contenttype')->getOptionArray()
            )
        );

        $fieldset->addField(
            'content_block',
            'select',
            array(
                'label' => Mage::helper('blugento_productmultitabs')->__('Content (Static Block)'),
                'name' => 'content_block',
                'values' => Mage::getSingleton('blugento_productmultitabs/adminhtml_system_config_source_multitabs_cmsblock')->getAllOptions()
            )
        );

        $fieldset->addField(
            'content_attribute',
            'select',
            array(
                'label' => Mage::helper('blugento_productmultitabs')->__('Content (Product Attribute)'),
                'name' => 'content_attribute',
                'values' => Mage::getSingleton('blugento_productmultitabs/adminhtml_system_config_source_multitabs_attribute')->getAllOptions()
            )
        );

        $fieldset->addField(
            'active_on_products',
            'select',
            array(
                'label' => Mage::helper('blugento_productmultitabs')->__('Active on Products'),
                'name' => 'active_on_products',
                'onchange' => 'displaySpecificProductsField()',
                'values' => Mage::getSingleton('blugento_productmultitabs/adminhtml_system_config_source_multitabs_active')->getOptionArray(),
            )
        );

        $message = Mage::helper('blugento_productmultitabs')->__('Add products codes/skus separated by comma.');
        $fieldset->addField(
            'products_codes',
            'textarea',
            array(
                'label' => Mage::helper('blugento_productmultitabs')->__('Products Codes'),
                'name' => 'products_codes',
                'after_element_html' => $message
            )
        );

        $message = Mage::helper('blugento_productmultitabs')->__('Only digits. Maximum sort order allowed: 99');
        $fieldset->addField(
            'sort_order',
            'text',
            array(
                'label' => Mage::helper('blugento_productmultitabs')->__('Sort Order'),
                'class' => 'required-entry validate-number validate-length maximum-length-2',
                'required' => true,
                'name' => 'sort_order',
                'after_element_html' => $message
            )
        );

        $fieldset->addField(
            'status',
            'select',
            array(
                'label' => Mage::helper('blugento_productmultitabs')->__('Status'),
                'name' => 'status',
                'values' => Mage::getSingleton('blugento_productmultitabs/adminhtml_system_config_source_multitabs_status')->getOptionArray()
            )
        );

        if (Mage::getSingleton('adminhtml/session')->getMultitabsData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getMultitabsData());
            Mage::getSingleton('adminhtml/session')->setMultitabsData(null);
        } elseif (Mage::registry('productmultitabs_data')) {
            $form->setValues(Mage::registry('productmultitabs_data')->getData());
        }
        return parent::_prepareForm();
    }
}


