<?php

class Blugento_Review_Helper_Data extends Mage_Review_Helper_Data
{
    public function getReviewStatuses()
    {
        if (Mage::getStoreConfig('catalog/review/enhanced_reviews')){
            return array(
                Mage_Review_Model_Review::STATUS_APPROVED                  => $this->__('Approved'),
                Mage_Review_Model_Review::STATUS_PENDING                   => $this->__('Pending'),
                Mage_Review_Model_Review::STATUS_NOT_APPROVED              => $this->__('Not Approved'),
                Blugento_Review_Model_Review::STATUS_APPROVED_AND_VERIFIED => $this->__('Approved And Verified')
            );
        } else {
            return array(
                Mage_Review_Model_Review::STATUS_APPROVED                  => $this->__('Approved'),
                Mage_Review_Model_Review::STATUS_PENDING                   => $this->__('Pending'),
                Mage_Review_Model_Review::STATUS_NOT_APPROVED              => $this->__('Not Approved')
            );
        }
    }

    public function isEnabled()
    {
        return (int) Mage::getStoreConfig('catalog/review/enhanced_reviews');
    }
}
