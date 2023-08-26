<?php

class Magecomp_Recaptcha_Model_Observer extends Mage_Core_Model_Abstract
{
    public function saveCustomerRegNo($observer)
    {
        if (!Mage::getStoreConfig('customer/address/regno_show', Mage::app()->getStore()->getStoreId())) {
            return false;
        }
        $regNo = Mage::app()->getRequest()->getPost('blugento_customer_reg_no');
        if(isset($regNo) && !empty(trim($regNo))){
            $customer = $observer->getEvent()->getCustomer();
            $address = Mage::getModel("customer/address");
            $address->setCustomerId($customer->getId())
                ->setFirstname($customer->getFirstname())
                ->setLastname($customer->getLastname());
            $address->setData('blugento_customer_reg_no', $regNo);
            $address->save();
        }
    }
}
