<?php
class Blugento_Newsletter_Model_Observer extends Mage_Core_Model_Abstract
{

    public function saveNewsletterSubscriber(Varien_Event_Observer $observer)
    {
        if(!Mage::getStoreConfig('blugento_gdpruserdata/newsletter_checkout/enable')){
            return;
        }

        $order = $observer->getEvent()->getOrder();

        $isSubscribed = Mage::app()->getRequest()->getParam('is_subscribed');
        if ($isSubscribed == 1) {
            Mage::getModel('newsletter/subscriber')->subscribe($order->getCustomerEmail());
        }
    }
}