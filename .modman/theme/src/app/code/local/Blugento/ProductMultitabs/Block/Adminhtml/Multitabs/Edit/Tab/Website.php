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

class Blugento_ProductMultitabs_Block_Adminhtml_Multitabs_Edit_Tab_Website extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('multitabs_form', array('legend' => Mage::helper('blugento_productmultitabs')->__('General')));
        $this->_addElementTypes($fieldset);

        $fieldset->addField('stores', 'multiselect', array(
            'name'      => 'stores[]',
            'label'     => Mage::helper('blugento_productmultitabs')->__('Select Store'),
            'title'     => Mage::helper('blugento_productmultitabs')->__('Select Store'),
            'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(true, true),
        ));

        if (Mage::getSingleton('adminhtml/session')->getMultitabsData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getMultitabsData());
            Mage::getSingleton('adminhtml/session')->setMultitabsData(null);
        } elseif (Mage::registry('productmultitabs_data')) {
            $form->setValues(Mage::registry('productmultitabs_data')->getData());
        }
        return parent::_prepareForm();
    }
}


