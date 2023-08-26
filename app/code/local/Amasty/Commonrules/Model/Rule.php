<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Commonrules
 */
class Amasty_Commonrules_Model_Rule extends Mage_Rule_Model_Rule
{
    /**
     * type of a concrete rule class
     *
     * @var string $_type
     */
    protected $_type;

    public function _construct()
    {
        parent::_construct();
        $this->_init($this->_type . '/rule');
    }

    public function getConditionsInstance()
    {
        return Mage::getModel($this->_type . '/rule_condition_combine');
    }
    
    public function massChangeStatus($ids, $status)
    {
        return $this->getResource()->massChangeStatus($ids, $status);
    }

    protected function _afterSave()
    {
        //Saving attributes used in rule
        $ruleProductAttributes = array_merge(
            $this->_getUsedAttributes($this->getConditionsSerialized()),
            $this->_getUsedAttributes($this->getActionsSerialized())
        );
        if (count($ruleProductAttributes)) {
            $this->getResource()->saveAttributes($this->getId(), $ruleProductAttributes);
        }

        return parent::_afterSave();
    }

    /**
     * Return all product attributes used on serialized action or condition
     *
     * @param string $serializedString
     * @return array
     */
    protected function _getUsedAttributes($serializedString)
    {
        $result = array();

        $pattern = '~s:32:"salesrule/rule_condition_product";s:9:"attribute";s:\d+:"(.*?)"~s';
        $matches = array();
        if (preg_match_all($pattern, $serializedString, $matches)){
            foreach ($matches[1] as $attributeCode) {
                $result[] = $attributeCode;
            }
        }

        return $result;
    }
    
    protected function _setWebsiteIds()
    {
        $websites = array();

        foreach (Mage::app()->getWebsites() as $website) {
            $websites[$website->getId()] = $website->getId();
        }

        $this->setOrigData('website_ids', $websites);
    }

    protected function _beforeSave(){
        $this->_setWebsiteIds();
        return parent::_beforeSave();
    }

    protected function _beforeDelete(){
        $this->_setWebsiteIds();
        return parent::_beforeDelete();
    }

    public function match($rate)
    {
        $selectedCarriers = explode(',', $this->getCarriers());

        if (in_array($rate->getCarrier(), $selectedCarriers)) {
            return true;
        }
        $methods = $this->getMethods();

        if (!$methods) {
            return false;
        }

        $methods = array_unique(explode(',', $methods));
        $rateCode = $rate->getCarrier() . '_' . $rate->getMethod();

        /** @var string $methodName */
        foreach ($methods as $methodName) {
            if ($rateCode == $methodName) {
                return true;
            }
        }

        return false;
    }
}
