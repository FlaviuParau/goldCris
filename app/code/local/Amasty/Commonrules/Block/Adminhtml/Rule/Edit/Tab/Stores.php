<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Commonrules
 */ 
class Amasty_Commonrules_Block_Adminhtml_Rule_Edit_Tab_Stores extends Amasty_Commonrules_Block_Adminhtml_Rule_Edit_Tab_Abstract
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        /* @var $hlp Amasty_Commonrules_Helper_Data */
        $hlp = Mage::helper('amcommonrules');
        $fldStore = $form->addFieldset('apply_in', array('legend' => $hlp->__('Apply In')));

        $fldStore->addField('for_admin', 'select', array(
            'label' => $hlp->__('Admin Area'),
            'name' => 'for_admin',
            'values' => array(
                array('value' => 0, 'label' => $hlp->__('No')),
                array('value' => 1, 'label' => $hlp->__('Yes'))),
        ));
        
        $fldStore->addField('stores', 'multiselect', array(
            'label'     => $hlp->__('Stores'),
            'name'      => 'stores[]',
            'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
            'note'      => $hlp->__('Leave empty or select all to apply the rule to any store'), 
        ));  

        $fldCust = $form->addFieldset('apply_for', array('legend'=> $hlp->__('Apply For')));
        $fldCust->addField('cust_groups', 'multiselect', array(
            'name'      => 'cust_groups[]',
            'label'     => $hlp->__('Customer Groups'),
            'values'    => $hlp->getAllGroups(),
            'note'      => $hlp->__('Leave empty or select all to apply the rule to any group'),
        ));

        $model = $this->_getRule();
        $this->getForm()->setValues($model->getData());

        return parent::_prepareForm();
    }
}