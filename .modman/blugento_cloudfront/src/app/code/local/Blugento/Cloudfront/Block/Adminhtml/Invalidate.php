<?php

class Blugento_Cloudfront_Block_Adminhtml_Invalidate extends Mage_Adminhtml_Block_Template
{
    public function getInvalidationUrl($action)
    {
        return $this->getUrl('*/cloudfront/'.$action);
    }

    public function isEnabled()
    {
        return Mage::helper('blugento_cloudfront')->isEnabled();
    }
}