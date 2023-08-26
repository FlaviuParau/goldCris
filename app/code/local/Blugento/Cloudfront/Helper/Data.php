<?php


class Blugento_Cloudfront_Helper_Data extends Mage_Core_Helper_Abstract
{
    static public function isEnabled()
    {
        return Mage::getModel('blugento_cloudfront/cloudfront')->isEnabled();
    }
}