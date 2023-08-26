<?php

class IntelligentIT_SmartBill_Model_ObserverLogin
{
    public function handle_adminSystemConfigChangedSection($observer)
    {
        // clear cache
        Mage::app()->cleanCache();
        Mage::dispatchEvent('adminhtml_cache_flush_system');

        // reset token
        $this->saveToken('');

        // make the login call
        $loginResponse = Mage::helper('smartbill/Public')->curl(IntelligentIT_SmartBill_Helper_Public::LOGIN_URL, self::prepareLoginData());

        if (!empty($loginResponse)) {
            $token = $this->saveToken($loginResponse);
        	self::populateSettings($token);
            // clear password from DB
            Mage::getModel('core/config')->saveConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_PASSWORD, '');
        }
    }

    public function checkLogin() {
        $loginResponse = Mage::helper('smartbill/Public')->curl(IntelligentIT_SmartBill_Helper_Public::LOGIN_URL, self::prepareLoginData());

        if (empty($loginResponse)) {
            // clear cache
            Mage::app()->cleanCache();
            Mage::dispatchEvent('adminhtml_cache_flush_system');

            // reset token
            self::saveToken('');

            return false;
        }

        return true;
    }

    public function prepareLoginData() {
        Mage::log($this->getCallbackURL(), null, 'smartbill_debug1.log');
        $loginData = new stdClass;
        $loginData->version            = IntelligentIT_SmartBill_Helper_Public::VERSION;
        $loginData->username           = Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_USERNAME);
        $loginData->password           = Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_PASSWORD);
        // $loginData->domainName         = $this->getDomainFromURL(Mage::getStoreConfig('web/unsecure/base_url'));
        $loginData->domainName         = $this->getStoreURL();
        $loginData->statusCallbackUrl  = $this->getCallbackURL();
        $loginData->name               = $this->getStoreName();
        $loginData->ecsId              = 1;
        return $loginData;
    }

    private function saveToken($loginResponse)
    {
        $token = '';
        
        try {
            $loginResponse = json_decode($loginResponse);
            $token = $loginResponse->token;
        } catch(Exception $e) {}

        if (is_null($token)) {
            $token = '';
        }

        Mage::getModel('core/config')->saveConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_TOKEN, $token);
        
        return $token;
    }

    static public function populateSettings($token) {
        $settingsResponse = Mage::helper('smartbill/Public')->curl(IntelligentIT_SmartBill_Helper_Public::SETTINGS_URL, null, array("Content-Type: application/json; charset=utf-8","Accept:application/json","Authorization: ECSDigest ".Mage::helper('smartbill/Public')->getAuthorization($token)), false, false);

        if (!empty($settingsResponse)) {
            $settingsResponse = json_decode($settingsResponse);
            $company = Mage::helper('smartbill/Public')->getCompanyDetailsByVatCode($settingsResponse->companies);

            if (isset($company->name)) {
                Mage::getModel('core/config')->saveConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_COMPANY_NAME, $company->name);
            }
            if (isset($company->vatCode)) {
                Mage::getModel('core/config')->saveConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_COMPANY, $company->vatCode);
                Mage::getModel('core/config')->saveConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_COMPANY_VAT_CODE, $company->vatCode);
            }
            if (isset($company->isTaxPayer)) {
                Mage::getModel('core/config')->saveConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_COMPANY_IS_TAX_PAYER, (int)$company->isTaxPayer);

                if (empty($company->isTaxPayer)) {
                    Mage::getModel('core/config')->saveConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_PRICE_INCLUDE_VAT, 1);
                    Mage::getModel('core/config')->saveConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_PRODUCTS_VAT, 0);
                    Mage::getModel('core/config')->saveConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_TRANSPORT_VAT, 0);
                }
            }
            if (isset($company->usePaymentTax)) {
                Mage::getModel('core/config')->saveConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_COMPANY_USE_PAYMENT_TAX, (int)$company->usePaymentTax);
            }
            if (isset($company->saveProductToDb)) {
                Mage::getModel('core/config')->saveConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_COMPANY_SAVE_PRODUCT, (int)$company->saveProductToDb);
            }
            if (isset($company->isStockEnabled)) {
                Mage::getModel('core/config')->saveConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_COMPANY_ENABLE_STOCK, (int)$company->isStockEnabled);
            }
            if (isset($settingsResponse->supportEmail)) {
                Mage::getModel('core/config')->saveConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_SUPPORT_EMAIL, $settingsResponse->supportEmail);
            }
        }
    }

    // private function getDomainFromURL($url) 
    // {
    // 	return parse_url($url,  PHP_URL_HOST);
    // }

    public function getStoreURL()
    {
        $url = str_replace(array('http://', 'https://'), '', Mage::getStoreConfig('web/unsecure/base_url'));
        $url = $url[strlen($url)-1]=='/' ? substr($url, 0, -1) : $url;

        return $url;        
    }

    private function getStoreName()
    {
        $name = Mage::getStoreConfig('general/store_information/name');
        $name = empty($name) ? '' : $name;

        return $name;
    }
    private function getCallbackURL() 
    {
        // return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'smartbill/documents/status/';
        // return Mage::getBaseUrl().'smartbill/documents/status/';
        $url = Mage::getBaseUrl().'smartbill/documents/status/';
        if ( (int)Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_SECURE_IN_FRONTEND) ) {
            $url = str_replace('http://', 'https://', $url);
        }
        return $url;
    }
}