<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Commonrules
 */ 
class Amasty_Commonrules_Helper_Data extends Mage_Core_Helper_Abstract
{
    const ALL_ORDERS = 0;
    const BACKORDERS_ONLY = 1;
    const NON_BACKORDERS = 2;

    protected $allRules;

    public function getAllRules()
    {
        if (null === $this->allRules) {
            /** @var Mage_SalesRule_Model_Resource_Rule_Collection $rulesCollection */
            $rulesCollection = Mage::getResourceModel('salesrule/rule_collection');
            $rulesCollection
                ->addFieldToSelect('rule_id', 'value')
                ->addFieldToSelect('name', 'label');
            $rules = $rulesCollection->toArray();
            $this->allRules = $rules['items'];
        }

        return $this->allRules;
    }
    
    public function getAllDays()
    {
        return array(
            array('value'=>'7', 'label' => $this->__('Sunday')),
            array('value'=>'1', 'label' => $this->__('Monday')),
            array('value'=>'2', 'label' => $this->__('Tuesday')),
            array('value'=>'3', 'label' => $this->__('Wednesday')),
            array('value'=>'4', 'label' => $this->__('Thursday')),
            array('value'=>'5', 'label' => $this->__('Friday')),
            array('value'=>'6', 'label' => $this->__('Saturday')),
        );
    }

    public function getAllTimes()
    {
        $timeArray = array();
        $timeArray[0] = 'Please select...';

        for($i = 0 ; $i < 24 ; $i++){
            for($j = 0; $j < 60 ; $j=$j+15){
                $timeStamp = $i.':'.$j;
                $timeFormat = date ('H:i',strtotime($timeStamp));
                $timeArray[$i * 100 + $j + 1] = $timeFormat;
            }
        }
        return $timeArray;
    }

    public function getAllGroups()
    {
        $customerGroups = Mage::getResourceModel('customer/group_collection')
            ->load()
            ->toOptionArray();

        $found = false;
        foreach ($customerGroups as $group) {
            if ($group['value'] == 0) {
                $found = true;
            }
        }
        if (!$found) {
            array_unshift($customerGroups, array('value' => 0, 'label' => $this->__('NOT LOGGED IN')));
        }

        return $customerGroups;
    }

    public function isCouponValid($rule, $providedCouponCodes, $isDisable = false)
    {
        if (!$isDisable) {
            $codes = $rule->getCoupon();
            $discountIds = $rule->getDiscountId();
        } else {
            $codes = $rule->getCouponDisable();
            $discountIds = $rule->getDiscountIdDisable();
        }

        $codes              = trim(strtolower($codes));
        $actualCouponCodes  = array_map('trim', explode(',', $codes));    //trim every element
        $actualCouponCodes  = array_filter($actualCouponCodes, function($value) { return $value !== '';});  //remove empty strings

        $actualDiscountIds = array();
        $discountIds = trim($discountIds,',');
        if ($discountIds) {
            $actualDiscountIds = explode(',', $discountIds);
        }
        
        if (!$actualCouponCodes && !$actualDiscountIds) {
            if (!$isDisable) {
                return true;
            } else {
                return false;
            }
        }

        if ($actualCouponCodes){
            return (bool) (array_intersect($actualCouponCodes, $providedCouponCodes));
        }

        if ($actualDiscountIds){
            foreach ($providedCouponCodes as $code){
                $couponModel         = Mage::getModel('salesrule/coupon')->load($code, 'code');
                $providedDiscountId  = $couponModel->getRuleId();

                if (in_array($providedDiscountId, $actualDiscountIds)){
                    return true;
                }
                $couponModel = null;
            }

        }

        return false;
    }

    public function modifySubtotal($address, $includeTax, $includeDiscount)
    {
        $subtotal = $address->getSubtotal();
        $baseSubtotal = $address->getBaseSubtotal();

        if ($includeTax){
            $subtotal += $address->getTaxAmount();
            $baseSubtotal += $address->getBaseTaxAmount();
        }

        if ($includeDiscount){
            $subtotal += $address->getDiscountAmount();
            $baseSubtotal += $address->getBaseDiscountAmount();
        }

        $address->setSubtotal($subtotal);
        $address->setBaseSubtotal($baseSubtotal);

        return $this;
    }

    public function getStatuses()
    {
        return array(
            '1' => Mage::helper('salesrule')->__('Active'),
            '0' => Mage::helper('salesrule')->__('Inactive'),
        );
    }

    /**
     * Return all shipping methods grouped by careers
     *
     * @return array
     */
    public function getAllShippingMethods()
    {
        $methods = Mage::getModel('adminhtml/system_config_source_shipping_allmethods')->toOptionArray();
        $methods[0]['label'] = $this->__('(none)');

        return $methods;
    }

    /**
     * Return array of all allowed shipping methods
     *
     * @return array
     */
    public function getShippingMethodsList()
    {
        $methods = array();
        $carriers = Mage::getSingleton('shipping/config')->getAllCarriers();

        foreach ($carriers as $carrierCode => $carrierModel) {
            $carrierMethods = $carrierModel->getAllowedMethods();

            if (!$carrierMethods) {
                continue;
            }

            foreach ($carrierMethods as $methodCode => $methodTitle) {
                $methods[$carrierCode.'_'.$methodCode] = '[' . $carrierCode . '] ' . $methodTitle;
            }
        }

        return $methods;
    }

    /**
     * Format used method list for grid render
     *
     * @param string $methodsString
     *
     * @return string
     */
    public function formatShippingMethods($methodsString)
    {
        $methods = $this->getShippingMethodsList();
        $result = [];
        $currentMethods = explode(",", $methodsString);

        foreach ($currentMethods as $currentMethod) {
            if (!empty($currentMethod) && array_key_exists($currentMethod, $methods)) {
                $result[] = $methods[$currentMethod];
            }
        }

        return implode("<br>", $result);
    }
}
