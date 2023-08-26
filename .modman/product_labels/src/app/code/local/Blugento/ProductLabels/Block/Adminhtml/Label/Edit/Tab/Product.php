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

class Blugento_ProductLabels_Block_Adminhtml_Label_Edit_Tab_Product extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('productlabels_form', array('legend' => Mage::helper('blugento_productlabels')->__('Product Page')));
        $this->_addElementTypes($fieldset);

        $fieldset->addField(
            'enabled_on_product',
            'select',
            array(
                'label' => Mage::helper('blugento_productlabels')->__('Enable on Product Page'),
                'name' => 'enabled_on_product',
                'values' => Mage::getSingleton('blugento_productlabels/adminhtml_system_config_source_label_status')->toOptionArray(),
            )
        );

        $fieldset->addField(
            'position_on_product',
            'select',
            array(
                'label' => Mage::helper('blugento_productlabels')->__('Position on Product Image'),
                'name' => 'position_on_product',
                'values' => Mage::getSingleton('blugento_productlabels/adminhtml_system_config_source_label_position')->toOptionArray(),
                'after_element_html' => Mage::getSingleton('blugento_productlabels/adminhtml_system_config_source_label_tooltip')->getPositionTooltip()
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