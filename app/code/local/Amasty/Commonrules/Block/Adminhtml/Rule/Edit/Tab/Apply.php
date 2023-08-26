<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Commonrules
 */
class Amasty_Commonrules_Block_Adminhtml_Rule_Edit_Tab_Apply extends Amasty_Commonrules_Block_Adminhtml_Rule_Edit_Tab_Abstract
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        /* @var $hlp Amasty_Commonrules_Helper_Data */
        $hlp = Mage::helper('amcommonrules');

        $fldInfo = $form->addFieldset('apply_restriction', array('legend'=> $hlp->__('Apply Restrictions Only With')));
        $promoShippingRulesUrl = $this->getUrl('adminhtml/promo_quote');
        $fldInfo->addField('coupon', 'text', array(
            'label'     => $hlp->__('Coupon Codes'),
            'name'      => 'coupon',
            'note'      => $hlp->__('Apply this rule with provided coupons only. Comma separated. Create coupon in <a href="%s"><span>Promotions / Shopping Cart Rules</span></a> area first.', $promoShippingRulesUrl),
        ));
        $fldInfo->addField('discount_id', 'multiselect', array(
            'label'     => $hlp->__('Shopping Cart Rules (discount)'),
            'name'      => 'discount_id[]',
            'values'    => $hlp->getAllRules(),
            'note'      => $hlp->__('Apply this rule with ANY coupon from specified discount rules. Create rule in <a href="%s"><span>Promotions / Shopping Cart Price Rules</span></a> area first. Useful when you have MULTIPLE coupons in one rule.', $promoShippingRulesUrl),
        ));

        $fldInfo = $form->addFieldset('not_apply_restriction', array('legend' => $hlp->__('Do NOT Apply Restrictions With')));
        $fldInfo->addField('coupon_disable', 'text', array(
            'label' => $hlp->__('Coupon codes'),
            'name'  => 'coupon_disable',
            'note'  => $hlp->__('Not apply this rule with provided coupons. Comma separated. Create coupon in <a href="%s"><span>Promotions / Shopping Cart Rules</span></a> area first.', $promoShippingRulesUrl),
        ));
        $fldInfo->addField('discount_id_disable', 'multiselect', array(
            'label' => $hlp->__('Shopping Cart Rules (discount)'),
            'name'  => 'discount_id_disable[]',
            'values' => $hlp->getAllRules(),
            'note'  => $hlp->__('Not apply the rule with ANY coupon from specified discount rules. Create coupon in <a href="%s"><span>Promotions / Shopping Cart Price Rules</span></a> area first. Useful when you have MULTIPLE coupons in one rule.', $promoShippingRulesUrl),
        ));

        $model = $this->_getRule();
        $this->getForm()->setValues($model->getData());
        
        return parent::_prepareForm();
    }
}