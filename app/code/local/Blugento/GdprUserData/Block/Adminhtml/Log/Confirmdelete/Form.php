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
 * @package     Blugento_GdprUserData
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_GdprUserData_Block_Adminhtml_Log_Confirmdelete_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post'
        ));
        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset('gdpruserdata_form', array('legend' => Mage::helper('blugento_gdpruserdata')->__('Request Details')));
        $this->_addElementTypes($fieldset);

        $fieldset->addField(
            'customer_email',
            'text',
            array(
                'label' => Mage::helper('blugento_gdpruserdata')->__('Customer Email'),
                'name' => 'customer_email',
                'disabled' => true
            )
        );

        $fieldset->addField(
            'type',
            'text',
            array(
                'label' => Mage::helper('blugento_gdpruserdata')->__('Type'),
                'name' => 'type',
                'disabled' => true
            )
        );

        $status = Mage::getSingleton('blugento_gdpruserdata/config_status')->toOptionArray();
        $description = Mage::getSingleton('blugento_gdpruserdata/config_status_description')->toOptionArray();
        $tooltip = '
            <div class="field-tooltip toggle">
                <span class="field-tooltip-action action-help" tabindex="0" hidden="hidden"></span>
                <div class="field-tooltip-content">
                     <p><strong>'.$status["pending"].': </strong><span>'.$description["pending"]["delete"].'</span></p>
                     <p><strong>'.$status["no data available"].': </strong><span>'.$description["no data available"]["delete"].'</span></p>
                     <p><strong>'.$status["processed"].': </strong><span>'.$description["processed"]["delete"].'</span></p>
                     <p><strong>'.$status["account exists rejection"].': </strong><span>'.$description["account exists rejection"]["delete"].'</span></p>
                     <p><strong>'.$status["completed"].': </strong><span>'.$description["completed"]["delete"].'</span></p>
                     <p><strong>'.$status["deleted"].': </strong><span>'.$description["deleted"]["delete"].'</span></p>
                </div>
            </div>
        ';
        $fieldset->addField(
            'status',
            'select',
            array(
                'label' => Mage::helper('blugento_gdpruserdata')->__('Status'),
                'name' => 'status',
                'disabled' => true,
                'values' => Mage::getSingleton('blugento_gdpruserdata/config_status')->toOptionArray(),
                'after_element_html' => $tooltip
            )
        );

        $fieldset->addField(
            'created_at',
            'text',
            array(
                'label' => Mage::helper('blugento_gdpruserdata')->__('Created Date'),
                'name' => 'created_at',
                'disabled' => true
            )
        );

        $fieldset->addField(
            'admin_confirmation',
            'select',
            array(
                'label' => Mage::helper('blugento_gdpruserdata')->__('Confirmation'),
                'name' => 'admin_confirmation',
                'values' => Mage::getSingleton('blugento_gdpruserdata/config_confirmation')->toOptionArray()
            )
        );

        $rejectMsg = Mage::helper('blugento_gdpruserdata')->__('This message will be sent to the customer email if you do not approve the deletion request.');
        $fieldset->addField(
            'reject_delete_message',
            'textarea',
            array(
                'label' => Mage::helper('blugento_gdpruserdata')->__('Rejection Message'),
                'name' => 'reject_delete_message',
                'after_element_html' => $rejectMsg
            )
        );

        if (Mage::getSingleton('adminhtml/session')->getGdpruserdataData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getGdpruserdataData());
            Mage::getSingleton('adminhtml/session')->setGdpruserdataData(null);
        } elseif (Mage::registry('gdpruserdata_data')) {
            $form->setValues(Mage::registry('gdpruserdata_data')->getData());
        }
        return parent::_prepareForm();
    }
}