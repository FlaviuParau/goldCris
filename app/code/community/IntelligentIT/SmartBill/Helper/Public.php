<?php

class IntelligentIT_SmartBill_Helper_Public extends Mage_Core_Helper_Abstract
{
    const VERSION               = '1.0.0';
    const CACHE_KEY_SETTINGS    = 'sbc_settings';
    const CACHE_LIFE_SETTINGS   = 10; // seconds

    const DOMAIN_VALID          = '/.+(smartbill.ro)$/i';
    const SSL_CERT_URL          = 'ssl://ws.smartbill.ro';
    const LOGIN_URL             = 'https://ws.smartbill.ro/SBORO/api/company/ecs/login?version=';
    const SETTINGS_URL          = 'https://ws.smartbill.ro/SBORO/api/company/ecs/info?version=';
    const INVOICE_URL           = 'https://ws.smartbill.ro/SBORO/api/invoice/ecs?version=';
    const PROFORMA_URL          = 'https://ws.smartbill.ro/SBORO/api/estimate/ecs?version=';

    const DOCUMENT_DATE_NOW     = 0;
    const DOCUMENT_DATE_ORDER   = 1;

    // const DEBUG_EMAIL_FROM      = 'support@smartbill.ro';
    const DEBUG_EMAIL_SUBJECT   = 'DEBUG %s (magento Smart Bill)';
    const SESSION_NAME          = 'IntelligentIT_SmartBill_SessionName';
    const PUBLIC_KEY_FILENAME   = 'sbc_public_key.pem';


    const XML_PATH_SMARTBILL_USERNAME                = 'connect/logindata/username';
    const XML_PATH_SMARTBILL_PASSWORD                = 'connect/logindata/password';
    const XML_PATH_SMARTBILL_TOKEN                   = 'connect/logindata/token';
    const XML_PATH_SMARTBILL_SUPPORT_EMAIL           = 'connect/logindata/support_email';

    const XML_PATH_SMARTBILL_COMPANY_NAME            = 'settings/extrasettingsdata/company_name';
    const XML_PATH_SMARTBILL_COMPANY_VAT_CODE        = 'settings/extrasettingsdata/vat_code';
    const XML_PATH_SMARTBILL_COMPANY_IS_TAX_PAYER    = 'settings/vatsettingsdata/is_tax_payer';
    const XML_PATH_SMARTBILL_COMPANY_USE_PAYMENT_TAX = 'settings/extrasettingsdata/use_payment_tax';
    const XML_PATH_SMARTBILL_COMPANY_SAVE_PRODUCT    = 'settings/extrasettingsdata/save_product';
    const XML_PATH_SMARTBILL_COMPANY_ENABLE_STOCK    = 'settings/extrasettingsdata/enable_stock';
    const XML_PATH_SMARTBILL_COMPANY_SAVE_CLIENT     = 'settings/docssettingsdata/save_client';

    const XML_PATH_SMARTBILL_COMPANY                 = 'settings/companydata/company';
    const XML_PATH_SMARTBILL_INVOICE_SERIES          = 'settings/docssettingsdata/invoice_series';
    const XML_PATH_SMARTBILL_PROFORMA_SERIES         = 'settings/docssettingsdata/proforma_series';
    const XML_PATH_SMARTBILL_DOCUMENT_CURRENCY       = 'settings/docssettingsdata/currency_product';
    const XML_PATH_SMARTBILL_DOCUMENT_CURRENCY_DOC   = 'settings/docssettingsdata/currency';
    const XML_PATH_SMARTBILL_DOCUMENT_DATE           = 'settings/docssettingsdata/document_date';
    const XML_PATH_SMARTBILL_ORDER_QTY_SOURCE        = 'settings/docssettingsdata/order_qty_source';
    const XML_PATH_SMARTBILL_ORDER_UNIT_TYPE         = 'settings/docssettingsdata/order_unit_type';
    const XML_PATH_SMARTBILL_UNIT_ATTRIBUTE         = 'settings/docssettingsdata/unit_attribute';
    const XML_PATH_SMARTBILL_ORDER_INCLUDE_TRANSPORT = 'settings/docssettingsdata/add_transport';
    const XML_PATH_SMARTBILL_DOCUMENT_TYPE           = 'settings/docssettingsdata/export_document';
    const XML_PATH_SMARTBILL_WAREHOUSE               = 'settings/docssettingsdata/warehouse';
    const XML_PATH_SMARTBILL_USE_STOCK           	 = 'settings/docssettingsdata/use_stock';
    const XML_PATH_SMARTBILL_EMAIL_INVOICE           = 'settings/docssettingsdata/invoice_email_client';

    const XML_PATH_SMARTBILL_PRICE_INCLUDE_VAT       = 'settings/vatsettingsdata/price_include_vat';
    const XML_PATH_SMARTBILL_PRODUCTS_VAT            = 'settings/vatsettingsdata/vat';
    const XML_PATH_SMARTBILL_TRANSPORT_INCLUDE_VAT   = 'settings/vatsettingsdata/transport_include_vat';
    const XML_PATH_SMARTBILL_TRANSPORT_VAT           = 'settings/vatsettingsdata/vat_transport';

    // const XML_PATH_SMARTBILL_INVOICE_URL              = 'samplesection4/invoicedata/link';
    
    const XML_PATH_MAGENTO_DISCOUNT_INCLUDE_TAX      = 'tax/calculation/discount_tax';
    const XML_PATH_MAGENTO_CALCULATION_ALGORITHM     = 'tax/calculation/algorithm';
    const XML_PATH_MAGENTO_CALCULATION_BASED_ON      = 'tax/calculation/based_on';
    const XML_PATH_MAGENTO_PRICE_INCLUDE_TAX         = 'tax/calculation/price_includes_tax';
    const XML_PATH_MAGENTO_SHIPPING_INCLUDE_TAX      = 'tax/calculation/shipping_includes_tax';
    const XML_PATH_MAGENTO_APPLY_AFTER_DISCOUNT      = 'tax/calculation/apply_after_discount';
    const XML_PATH_MAGENTO_APPLY_TAX_ON              = 'tax/calculation/apply_tax_on';
    const XML_PATH_MAGENTO_DISPLAY_TYPE              = 'tax/display/type';
    const XML_PATH_MAGENTO_DISPLAY_SHIPPING          = 'tax/display/shipping';
    const XML_PATH_MAGENTO_CART_DISPLAY_PRICE        = 'tax/cart_display/price';
    const XML_PATH_MAGENTO_CART_DISPLAY_SUBTOTAL     = 'tax/cart_display/subtotal';
    const XML_PATH_MAGENTO_CART_DISPLAY_SHIPPING     = 'tax/cart_display/shipping';
    const XML_PATH_MAGENTO_CART_DISPLAY_GRANDTOTAL   = 'tax/cart_display/grandtotal';
    const XML_PATH_MAGENTO_SALES_DISPLAY_PRICE       = 'tax/sales_display/price';
    const XML_PATH_MAGENTO_SALES_DISPLAY_SUBTOTAL    = 'tax/sales_display/subtotal';
    const XML_PATH_MAGENTO_SALES_DISPLAY_SHIPPING    = 'tax/sales_display/shipping';
    const XML_PATH_MAGENTO_SALES_DISPLAY_GRANDTOTAL  = 'tax/sales_display/grandtotal';

    const DEFAULT_LOGIN_ERROR   = 'Autentificare esuata. Va rugam verificati datele si incercati din nou.';
    const CERT_URL_ERROR        = 'Accesul catre serviciul Smart Bill Cloud este restrictionat. Va rugam verificati configuratia serverului si incercati din nou.';
    const SERVER_ERROR          = 'A intervenit o eroare la comunicarea cu Smart Bill Cloud. Va rugam verificati datele de conectare / reincercati o noua autentificare cu datele existente.';

    static private function getSSLCertificate() {
        $g = stream_context_create(array("ssl" => array("capture_peer_cert" => true)));
        $r = stream_socket_client(self::SSL_CERT_URL, $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $g);
        $cont = stream_context_get_params($r);

        $sss = "_";
        openssl_x509_export($cont["options"]["ssl"]["peer_certificate"], $sss);

        if ( '_' !== $sss
          && !empty($sss) ) {
            $file = fopen(dirname(__FILE__).DIRECTORY_SEPARATOR.'dwl-ws-test.crt', "w");
            fwrite($file, $sss);
            fclose($file);
        }
    }

    static public function getCompanyDetailsByVatCode($companies, $vatCode='') {
        $vatCode = empty($vatCode) ? Mage::getStoreConfig(self::XML_PATH_SMARTBILL_COMPANY) : $vatCode;
        $company = $companies[0];

        if (!empty($vatCode)
         && is_array($companies)) {
            foreach ($companies as $key => $value) {
                if (!empty($value->vatCode)
                 && $value->vatCode==$vatCode) {
                    $company = $value;
                    break;
                }
            }
        }

        return $company;
    }

    static public function curl($url, $data=null, $httpHeaders=null, $noSSL=false, $checkToken=true, $throwError=true) 
    {
        $cache = Mage::app()->getCache();
        $loginToken = Mage::getStoreConfig(self::XML_PATH_SMARTBILL_TOKEN);
        if (empty($url)
         || ($url!=self::LOGIN_URL && empty($loginToken) && $checkToken))   return FALSE;

        $urlToCall = $url;
        switch ($url) {
            case self::LOGIN_URL:
            case self::SETTINGS_URL:
            case self::INVOICE_URL:
            case self::PROFORMA_URL:
                $urlToCall .= self::VERSION;
                break;
        }

        // cached results
        if ($url==self::SETTINGS_URL) {
            $cacheData = $cache->load(self::CACHE_KEY_SETTINGS);

            if ($cacheData!==false) {
                return $cacheData;
            }
        }
        // $agent= 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
        $ch = curl_init($urlToCall);
        // curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        if (!empty($data))
        {
            // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json; charset=utf-8","Accept:application/json"));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        if (!empty($httpHeaders)
         && is_array($httpHeaders)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
        }

        /*
        if (!$noSSL) {
            $certFilePath = dirname(__FILE__).DIRECTORY_SEPARATOR.'dwl-ws-test.crt';
            // if (!file_exists($certFilePath)) {
            if ($url==self::LOGIN_URL) {
                self::getSSLCertificate();
            }

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_CAINFO, $certFilePath);
        }
        */

        $return = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_status==200) {
            // cache result
            if ($url==self::SETTINGS_URL) {
                $cache->save($return, self::CACHE_KEY_SETTINGS, array(self::CACHE_KEY_SETTINGS), self::CACHE_LIFE_SETTINGS);

            }
        } else {
            // clear cache
            Mage::app()->cleanCache();
            Mage::dispatchEvent('adminhtml_cache_flush_system');

            switch ($url) {
                case self::LOGIN_URL:
                    // reset token
                    Mage::getModel('core/config')->saveConfig(self::XML_PATH_SMARTBILL_TOKEN, '');
                    // clear password from DB
                    Mage::getModel('core/config')->saveConfig(self::XML_PATH_SMARTBILL_PASSWORD, '');

                    if (!$throwError) {
                        return false;
                    }

                    if ( empty($http_status) ) {
                        $return = self::CERT_URL_ERROR;
                    } else if (strpos($return, 'HTTP Status')!==false) {
                        $return = self::DEFAULT_LOGIN_ERROR;
                    }

                    try {
                        $bkSessionValue = $_SESSION[self::SESSION_NAME];
                    } catch (Exception $e) {}                    

                    Mage::throwException($return);

                    try {
                        $_SESSION[self::SESSION_NAME] = $bkSessionValue;
                    } catch (Exception $e) {}                    
                    break;

                case self::SETTINGS_URL:
                    $cache->save('', self::CACHE_KEY_SETTINGS, array(self::CACHE_KEY_SETTINGS), self::CACHE_LIFE_SETTINGS);
                
                default:
                    if (!$throwError) {
                        return false;
                    }

                    $error = new IntelligentIT_SmartBill_Error_Processor();
                    $errorMessage = $return;

                    try {
                        $returnJSON = json_decode($return);
                        $errorMessage = $returnJSON->errorText;
                    } catch (Exception $e) {}

                    $http_status = empty($http_status) ? '' : $http_status;
                    $errorMessage = empty($errorMessage) ? self::SERVER_ERROR : $errorMessage;
                    $error->processSmartBill('Error '.$http_status, $errorMessage);

                    // Mage::getSingleton('adminhtml/session')->addError($return);
                    break;
            }
            // throw new Exception($return);
            
            // empty response
            $return = '';
        }

        return $return;
    }

    static public function getAuthorization($token='') 
    {
        return base64_encode(Mage::getStoreConfig(self::XML_PATH_SMARTBILL_USERNAME).':'.(!empty($token) ? $token : Mage::getStoreConfig(self::XML_PATH_SMARTBILL_TOKEN)));
    }

    static public function _updateCompareValue($local, $remote, &$return) {
        $return = $local==$remote ? true : $return;
    }
    static public function _updateOptionsWithNotFound(&$options, $found, $label='') {
        if (!$found) {
            $options[""] = 'Alegeti '.$label;
            ksort($options);
        }
    }

}