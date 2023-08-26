<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */
class Amasty_Payrestriction_Model_Rule_Condition_Address extends Amasty_Commonrules_Model_Rule_Condition_Address
{
    public function loadAttributeOptions()
    {
        parent::loadAttributeOptions();
        
        $attributes = $this->getAttributeOption();
        unset($attributes['payment_method']);
        $attributes['street'] = Mage::helper('salesrule')->__('Address Line');
        $attributes['city'] = Mage::helper('salesrule')->__('City');
        $attributes['shipping_carrier'] = Mage::helper('salesrule')->__('Shipping Carrier');

        $this->setAttributeOption($attributes);

        return $this;
    }
    
    public function getOperatorSelectOptions()
    {
        $operators = $this->getOperatorOption();
        if ($this->getAttribute() == 'street') {
             $operators = array(
                '{}'  => Mage::helper('rule')->__('contains'),
                '!{}' => Mage::helper('rule')->__('does not contain'),             
             );
        }

        return parent::_getOperatorOptions($operators);
    }


    public function getValueSelectOptions()
    {
        if ($this->getAttribute() == 'shipping_carrier'){
            $carriers = Mage::getModel('ampayrestriction/source_carriers')->toOptionArray();
            $this->setData('value_select_options', $carriers);
            return $this->getData('value_select_options');
        } else {
            return parent::getValueSelectOptions();
        }
    }

    public function getValueElementType()
    {
        if ($this->getAttribute() == 'shipping_carrier'){
            return 'select';
        }
        return parent::getValueElementType();
    }

    public function getInputType()
    {
        if ($this->getAttribute() == 'shipping_carrier'){
            return 'select';
        }
        return parent::getInputType();
    }

    public function validate(Varien_Object $object)
    {
        $address = $object;
        if ($this->getAttribute() == 'shipping_carrier'){
            if (!$address instanceof Mage_Sales_Model_Quote_Address) {
                if ($object->getQuote()->isVirtual()) {
                    $address = $object->getQuote()->getBillingAddress();
                }
                else {
                    $address = $object->getQuote()->getShippingAddress();
                }
            }

            if ('shipping_carrier' == $this->getAttribute() ) {
                $shippingMethodData = explode('_',$address->getShippingMethod());
                $address->setShippingCarrier($shippingMethodData[0]);
            }
        }
        return parent::validate($address);
    }
}