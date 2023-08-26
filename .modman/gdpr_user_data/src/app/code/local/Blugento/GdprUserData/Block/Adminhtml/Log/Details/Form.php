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

class Blugento_GdprUserData_Block_Adminhtml_Log_Details_Form extends Mage_Adminhtml_Block_Widget_Form
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
                     <p><strong>'.$status["pending"].': </strong><span>'.$description["pending"]["export"].'</span></p>
                     <p><strong>'.$status["no data available"].': </strong><span>'.$description["no data available"]["export"].'</span></p>
                     <p><strong>'.$status["processed"].': </strong><span>'.$description["processed"]["export"].'</span></p>
                     <p><strong>'.$status["completed"].': </strong><span>'.$description["completed"]["export"].'</span></p>
                     <p><strong>'.$status["deleted"].': </strong><span>'.$description["deleted"]["export"].'</span></p>
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

        if (Mage::getSingleton('adminhtml/session')->getGdpruserdataData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getGdpruserdataData());
            Mage::getSingleton('adminhtml/session')->setGdpruserdataData(null);
        } elseif (Mage::registry('gdpruserdata_data')) {
            $form->setValues(Mage::registry('gdpruserdata_data')->getData());
        }
        return parent::_prepareForm();
    }
}