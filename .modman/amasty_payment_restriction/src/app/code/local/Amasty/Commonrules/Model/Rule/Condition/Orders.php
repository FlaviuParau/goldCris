<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Commonrules
 */

class Amasty_Commonrules_Model_Rule_Condition_Orders extends Mage_Rule_Model_Condition_Abstract
{
    public function loadAttributeOptions()
    {
        $hlp = Mage::helper('amcommonrules');
        $attributes = array(
            'order_num'    => $hlp->__('Number of Completed Orders'),
            'sales_amount' => $hlp->__('Total Sales Amount'),
        );

        $this->setAttributeOption($attributes);
        return $this;
    }

    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    public function getInputType()
    {
        return 'numeric';
    }

    public function getValueElementType()
    {
        return 'text';
    }

    public function getValueSelectOptions()
    {
        $options = array();

        $key = 'value_select_options';
        if (!$this->hasData($key)) {
            $this->setData($key, $options);
        }
        return $this->getData($key);
    }

    /**
     * Validate Address Rule Condition
     *
     * @param Varien_Object $object
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        $quote = $object;
        if (!$quote instanceof Mage_Sales_Model_Quote) {
            $quote = $this->_getSession()->getQuote();
        }

        $num = 0;
        if ($quote->getCustomerId()){

            $resource  = Mage::getSingleton('core/resource');
            $db        = $resource->getConnection('core_read');

            $select = $db->select()
                ->from(array('o'=>$resource->getTableName('sales/order')), array())
                ->where('o.customer_id = ?', $quote->getCustomerId())
                ->where('o.status = ?', 'complete')
            ;

            if ('order_num' == $this->getAttribute()) {
                $select->from(null, array(new Zend_Db_Expr('COUNT(*)')));
            }
            elseif ('sales_amount' == $this->getAttribute()){
                $select->from(null, array(new Zend_Db_Expr('SUM(o.base_grand_total)')));
            }

            $num = $db->fetchOne($select);
        }

        return $this->validateAttribute($num);
    }

    protected function _getSession()
    {
        $session = null;
        if (Mage::app()->getStore()->isAdmin()) {
            $session = Mage::getSingleton('adminhtml/session_quote');
        } else {
            $session = Mage::getSingleton('checkout/session');
        }

        return $session;
    }
}