<?php

class Blugento_CookieNotice_Block_Html_CookieNotice extends Mage_Page_Block_Html_CookieNotice
{
    public function getCookieRestrictionBlockContent()
    {
        $blockIdentifier = Mage::helper('core/cookie')->getCookieRestrictionNoticeCmsBlockIdentifier();
        $block = Mage::getModel('cms/block')->setStoreId(Mage::app()->getStore()->getId())->load($blockIdentifier, 'identifier');

        $html = '';
        if ($block->getIsActive()) {
            /* @var $helper Mage_Cms_Helper_Data */
            $helper = Mage::helper('cms');
            $processor = $helper->getBlockTemplateProcessor();
            $html = $processor->filter($block->getContent());
        }

        return $html;
    }
}

