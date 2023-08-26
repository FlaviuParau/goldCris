<?php

class Blugento_Newsletter_Model_Rewrite_Observer extends Mage_Newsletter_Model_Observer
{
    public function subscribeCustomer($observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $subscriber = Mage::getModel('newsletter/subscriber')->load($customer->getEmail(), 'subscriber_email');

        if (!$subscriber->getId()) {
            if (($customer instanceof Mage_Customer_Model_Customer)) {
                Mage::getModel('newsletter/subscriber')->subscribeCustomer($customer);
            }
        }
        return $this;
    }
}
