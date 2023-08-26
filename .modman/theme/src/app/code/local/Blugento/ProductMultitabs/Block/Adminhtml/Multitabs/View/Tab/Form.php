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

class Blugento_ProductMultitabs_Block_Adminhtml_Multitabs_View_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        /** @var Blugento_ProductMultitabs_Helper_Data $helper */
        $helper = Mage::helper('blugento_productmultitabs');

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('multitabs_form', array('legend' => $helper->__('General')));
        $this->_addElementTypes($fieldset);

        $identifier = Mage::getModel('blugento_productmultitabs/multitabs')->load($this->getRequest()->getParam('id'))->getIdentifier();

        $fieldset->addField(
            'name',
            'text',
            array(
                'label' => $helper->__('Tab Name'),
                'name' => 'name',
                'readonly' => true
            )
        );

        $fieldset->addField(
            'content',
            'text',
            array(
                'label' => $helper->__('Content'),
                'name' => 'content',
                'readonly' => true
            )
        );

        $message = $helper->__('Only digits. Maximum sort order allowed: 99');
        $config = array(
            'label' => $helper->__('Sort Order'),
            'class' => 'required-entry validate-number validate-length maximum-length-2',
            'required' => true,
            'name' => 'sort_order'
        );

        if ($identifier == 'product_reviews') {
            $tooltipContent = $helper->__('Product Reviews sort order is mandatory to have 99 value because of tabs design.');
            $message .= '. ';
            $message .= Mage::getSingleton('blugento_productmultitabs/adminhtml_system_config_source_multitabs_tooltip')->toHtml($tooltipContent);
            $config['readonly'] = true;
        }

        $config['after_element_html'] = $message;

        $fieldset->addField(
            'sort_order',
            'text',
            $config
        );

        $fieldset->addField(
            'status',
            'select',
            array(
                'label' => $helper->__('Status'),
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


