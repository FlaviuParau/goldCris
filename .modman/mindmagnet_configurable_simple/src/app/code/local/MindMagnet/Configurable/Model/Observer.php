<?php

class MindMagnet_Configurable_Model_Observer
{
    public function simpleProductPrice(Varien_Event_Observer $observer)
    {
        $event   = $observer->getEvent();
        $product = $event->getProduct();

        if ($product->getCustomOption('simple_product')) {
            $selectedProductId = $product->getCustomOption('simple_product')->getValue();
            $price = Mage::getModel('catalog/product')->load($selectedProductId)->getFinalPrice();
            $product->setFinalPrice($price);
            return $price;
        }

        return $this;
    }
}
