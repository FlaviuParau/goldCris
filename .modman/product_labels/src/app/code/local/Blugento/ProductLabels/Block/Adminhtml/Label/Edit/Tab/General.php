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
 * @package     Blugento_ProductLabels
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_ProductLabels_Block_Adminhtml_Label_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('productlabels_form', array('legend' => Mage::helper('blugento_productlabels')->__('General Informations')));
        $this->_addElementTypes($fieldset);

        $labelId = $this->getRequest()->getParam('id');
        /** @var Blugento_ProductLabels_Model_Label $label */
        $label = Mage::getModel('blugento_productlabels/label')->load($labelId);
        $createdType = $label->getCreatedType();

        $fieldset->addField(
            'name',
            'text',
            array(
                'label' => Mage::helper('blugento_productlabels')->__('Name'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'name',
            )
        );

        if (!$createdType || $createdType == 'custom') {
            $fieldset->addField(
                'type',
                'select',
                array(
                    'label' => Mage::helper('blugento_productlabels')->__('Type'),
                    'name' => 'type',
                    'values' => Mage::getSingleton('blugento_productlabels/adminhtml_system_config_source_label_type')->toOptionArray(),
                    'onchange' => 'displayCategories()'
                )
            );

            $fieldset->addField(
                'label_image',
                'image',
                array(
                    'label' => Mage::helper('blugento_productlabels')->__('Upload a Label Image'),
                    'name' => 'label_image'
                )
            );

            $message = Mage::helper('blugento_productlabels')->__('Choose the categories where to apply the labels.');

            $fieldset->addField(
                'categories',
                'multiselect',
                array(
                    'label' => Mage::helper('blugento_productlabels')->__('Categories'),
                    'name' => 'categories[]',
                    'values' => Mage::getSingleton('blugento_productlabels/adminhtml_system_config_source_label_category')->toOptionArray(),
                    'after_element_html' => $message
                )
            );
        }

        $fieldset->addField(
            'status',
            'select',
            array(
                'label' => Mage::helper('blugento_productlabels')->__('Status'),
                'name' => 'status',
                'values' => Mage::getSingleton('blugento_productlabels/adminhtml_system_config_source_label_status')->toOptionArray(),
            )
        );

        if ($label->getPath()) {
            $fieldset->addField(
                'path',
                'note',
                array(
                    'label' => Mage::helper('blugento_productlabels')->__('Label Model'),
                    'name' => 'path',
                    'text' => Mage::getSingleton('blugento_productlabels/adminhtml_system_config_source_label_model')->toOptionArray($labelId)
                ));
        }

        $fieldset->addField(
            'stores',
            'multiselect',
            array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('blugento_productlabels')->__('Select Store'),
                'title'     => Mage::helper('blugento_productlabels')->__('Select Store'),
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(true, true),
            )
        );

        if (Mage::getSingleton('adminhtml/session')->getProductLabelsData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getProductLabelsData());
            Mage::getSingleton('adminhtml/session')->setProductLabelsData(null);
        } elseif (Mage::registry('productlabels_data')) {
            $form->setValues(Mage::registry('productlabels_data')->getData());
        }
        return parent::_prepareForm();
    }
}