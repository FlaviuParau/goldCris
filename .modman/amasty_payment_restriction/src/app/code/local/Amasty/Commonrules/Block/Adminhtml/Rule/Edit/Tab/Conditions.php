<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Commonrules
 */
class Amasty_Commonrules_Block_Adminhtml_Rule_Edit_Tab_Conditions
    extends Amasty_Commonrules_Block_Adminhtml_Rule_Edit_Tab_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('amcommonrules')->__('Conditions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('amcommonrules')->__('Conditions');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = $this->_getRule();

        $form = new Varien_Data_Form();
        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/*/newConditionHtml/form/rule_conditions_fieldset'));

        $fieldset = $form->addFieldset('rule_conditions_fieldset', array(
            'legend'=>Mage::helper('salesrule')->__('Apply the rule only if the following conditions are met (leave blank for all products)')
        ))->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('salesrule')->__('Conditions'),
            'title' => Mage::helper('salesrule')->__('Conditions'),
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));


        /**
         * @example of adding new field to rules/restrictions modules.
         * //checking if dependent module has new field before show it in admin interface
         */
        $newField  = 'out_of_stock';
        if($model->getResource()->hasField($newField)) {
            $hlp = Mage::helper('amcommonrules');
            $fldAdv = $form->addFieldset('advanced', array('legend' => $hlp->__('Backorders')));
            $fldAdv->addField($newField, 'select', array(
                'label' => $hlp->__('Apply the rule to'),
                'name' => $newField,
                'values' => array(
                    array('value' => $hlp::ALL_ORDERS,      'label' => $hlp->__('All orders')),
                    array('value' => $hlp::BACKORDERS_ONLY, 'label' => $hlp->__('Backorders only')),
                    array('value' => $hlp::NON_BACKORDERS,  'label' => $hlp->__('Non backorders'))),
            ));
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();  
    }
}
