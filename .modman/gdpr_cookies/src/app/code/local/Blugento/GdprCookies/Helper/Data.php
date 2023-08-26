<?php

class Blugento_GdprCookies_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getDefaultCookiesCategories()
    {

        $defaultCategories = array();

        $defaultCategories['analytics'] = Mage::getStoreConfig('gdpr_cookies/default_categories/analytics');
        $defaultCategories['marketing'] = Mage::getStoreConfig('gdpr_cookies/default_categories/marketing');

        return $defaultCategories;
    }

    public function getCustomCookiesCategories()
    {
        return unserialize(Mage::getStoreConfig('gdpr_cookies/custom_categories/categories'));
    }

    public function getGoogleApiClass()
    {
        if (Mage::getStoreConfig('google/analytics/category') == 1) {
            return 'gdpr-necessary';
        } else if (Mage::getStoreConfig('google/analytics/category') == 2) {
            return 'gdpr-analytics';
        } else {
            return 'gdpr-marketing';
        }
    }
}
	 