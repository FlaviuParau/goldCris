<?php

class Blugento_SeoEnhancements_Block_Page_Html_Head_Cms_Canonical extends Mage_Core_Block_Template
{
    public function cmsCanonical()
    {
        if (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'cms') {
            $currentUrl = Mage::helper('core/url')->getCurrentUrl();
            return rtrim($currentUrl, '/');
        }
        return null;
    }
}
