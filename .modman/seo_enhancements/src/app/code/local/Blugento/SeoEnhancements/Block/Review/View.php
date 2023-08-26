<?php

class Blugento_SeoEnhancements_Block_Review_View extends Mage_Review_Block_View
{
    protected function _prepareLayout()
    {
        if (Mage::helper('blugento_seoenhancements')->isCanonicalLinkReview()) {
            $headBlock = $this->getLayout()->getBlock('head');

            if ($headBlock) {
                $headBlock->addLinkRel('canonical', $this->getProduct()->getProductUrl());
            }
        }
        return parent::_prepareLayout();
    }
}