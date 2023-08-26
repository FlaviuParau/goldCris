<?php

class Blugento_Billing_Block_Widget_Name extends Mage_Customer_Block_Widget_Name
{
    public function _construct()
    {
        parent::_construct();

        // default template location
        $this->setTemplate('blugento/billing/customer/widget/name.phtml');
    }
}