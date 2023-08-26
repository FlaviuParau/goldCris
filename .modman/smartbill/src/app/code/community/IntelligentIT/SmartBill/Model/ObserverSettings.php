<?php

// include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'IntelligentIT_SmartBill_Model_ObserverLogin';

class IntelligentIT_SmartBill_Model_ObserverSettings
{
    public function handle_adminSystemConfigChangedSection($observer)
    {
        // clear cache
        Mage::app()->cleanCache();
        Mage::dispatchEvent('adminhtml_cache_flush_system');

        IntelligentIT_SmartBill_Model_ObserverLogin::populateSettings('');
    }
}