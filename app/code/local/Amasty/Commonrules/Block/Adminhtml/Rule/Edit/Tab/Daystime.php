<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Commonrules
 */
class Amasty_Commonrules_Block_Adminhtml_Rule_Edit_Tab_Daystime extends Amasty_Commonrules_Block_Adminhtml_Rule_Edit_Tab_Abstract
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        /* @var $hlp Amasty_Commonrules_Helper_Data */
        $hlp = Mage::helper('amcommonrules');

        $fldInfo = $form->addFieldset('daystime', array('legend'=> $hlp->__('Days and Time')));

        $fldInfo->addField('days', 'multiselect', array(
            'label'     => $hlp->__('Days of the Week'),
            'name'      => 'days[]',
            'values'    => $hlp->getAllDays(),
            'note'      => $hlp->__('Leave empty or select all to apply the rule every day'),
        ));

        $fldInfo->addField('time_from', 'select', array(
            'label' => $hlp->__('Time From:'),
            'name' => 'time_from',
            'options' => $hlp->getAllTimes(),
        ));
        $fldInfo->addField('time_to', 'select', array(
            'label' => $hlp->__('Time To:'),
            'name' => 'time_to',
            'options' => $hlp->getAllTimes(),
        ));

        $model = $this->_getRule();
        $this->getForm()->setValues($model->getData());
        
        return parent::_prepareForm();
    }
}
