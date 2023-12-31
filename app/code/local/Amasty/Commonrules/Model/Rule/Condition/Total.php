<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Commonrules
 */

class Amasty_Commonrules_Model_Rule_Condition_Total extends Mage_SalesRule_Model_Rule_Condition_Combine {

    private $_passedRules = array();

    public function __construct()
    {
        parent::__construct();
        $this->setType('amcommonrules/rule_condition_total')
            ->setValue(null);
        ;
    }

    public function loadArray($arr, $key = 'conditions')
    {
        $this->setAttribute($arr['attribute']);
        $this->setOperator($arr['operator']);
        parent::loadArray($arr, $key);
        return $this;
    }

    public function asXml($containerKey = 'conditions', $itemKey = 'condition')
    {
        $xml = '<attribute>' . $this->getAttribute() . '</attribute>'
            . '<operator>' . $this->getOperator() . '</operator>'
            . parent::asXml($containerKey, $itemKey);
        return $xml;
    }

    public function loadAttributeOptions()
    {
        $this->setAttributeOption(array(
            'average_order_value' => Mage::helper('amcommonrules')->__('Average Order Value'),
            'total_orders_amount' => Mage::helper('amcommonrules')->__('Total Sales Amount'),
            'of_placed_orders'    => Mage::helper('amcommonrules')->__('Number of Placed Orders'),
        ));
        return $this;
    }

    public function loadValueOptions()
    {
        return $this;
    }

    public function loadOperatorOptions() {
        $this->setOperatorOption(array(
            '=='  => Mage::helper('rule')->__('is'),
            '!='  => Mage::helper('rule')->__('is not'),
            '>='  => Mage::helper('rule')->__('equals or greater than'),
            '<='  => Mage::helper('rule')->__('equals or less than'),
            '>'   => Mage::helper('rule')->__('greater than'),
            '<'   => Mage::helper('rule')->__('less than'),
            '()'  => Mage::helper('rule')->__('is one of'),
            '!()' => Mage::helper('rule')->__('is not one of'),
        ));
        return $this;
    }

    public function getValueElementType()
    {
        return 'text';
    }

    public function getNewChildSelectOptions()
    {
        $conditions = array(
            array('label' => Mage::helper('amcommonrules')->__('Please choose condition'), 'value' => ''),
            array('label' => Mage::helper('amcommonrules')->__('Order Status'), 'value' => 'amcommonrules/rule_condition_total_status'),
            array('label' => Mage::helper('amcommonrules')->__('Period after order was placed'), 'value' => 'amcommonrules/rule_condition_total_period'),
        );
        return $conditions;
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() .
            Mage::helper('amcommonrules')->__(' If %s %s %s for a subselection of orders matching %s of these conditions:', $this->getAttributeElement()->getHtml(), $this->getOperatorElement()->getHtml(), $this->getValueElement()->getHtml(), $this->getAggregatorElement()->getHtml());

        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }
        return $html;
    }

    public function validate(Varien_Object $object)
    {
        $quote = $object;
        if (!$quote instanceof Mage_Sales_Model_Quote) {
            $quote = $object->getQuote();
        }

        // order history conditions are valid for customers only, not for visitors.
        $id = $quote->getCustomerId();
        if (!$id) {
            return false;
        }

        $condArray = array();

        foreach ($this->getConditions() as $condObj) {
            if (!in_array( $condObj->getId(),$this->_passedRules  )){
                $this->_passedRules[] = $condObj->getId();
                $condArray[] = $condObj->validate($object);
            }
        }

        $fieldName = $this->getAttributeElement()->getValue();
        $v = Mage::getSingleton('amcommonrules/calculator')
            ->getSingleTotalField($id, $fieldName, $condArray, $this->getAggregator());

        return $this->validateAttribute($v);
    }
}