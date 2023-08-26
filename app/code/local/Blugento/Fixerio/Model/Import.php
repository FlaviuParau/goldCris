<?php
class Blugento_Fixerio_Model_Import extends Mage_Directory_Model_Currency_Import_Abstract
{
    protected $_url = 'http://data.fixer.io/api/latest?access_key=%1$s&symbols=%2$s,%3$s';
    protected $_messages = array();

    /**
     * HTTP client
     *
     * @var Varien_Http_Client
     */
    protected $_httpClient;

    public function __construct()
    {
        $this->_httpClient = new Varien_Http_Client();
    }

    protected function _convert($currencyFrom, $currencyTo, $retry = 0)
    {
        $accessKey = Mage::helper('fixerio')->getAccessKey();
        $url = sprintf($this->_url, $accessKey, $currencyFrom, $currencyTo);

        try {
            $response = $this->_httpClient
                ->setUri($url)
                ->setConfig(array('timeout' => Mage::getStoreConfig('currency/fixerio/timeout')))
                ->request('GET')
                ->getBody();

            $converted = json_decode($response);

            if ($converted->success) {
                $base = 1 / $converted->rates->$currencyFrom;
                unset($converted->rates->$currencyFrom);
                $rate = $converted->rates->$currencyTo * $base;
            } else {
                $this->_messages[] = Mage::helper('directory')->__('Cannot retrieve rate from %s. The error type was "%s" and error message was "%s".', $url, $converted->error->type, $converted->error->info);
                return null;
            }

            // test for bcmath to retain precision
            if (function_exists('bcadd')) {
                return bcadd($rate, '0', 6);
            }
            return (float) $rate;
        } catch (Exception $e) {
            if ($retry == 0) {
                return $this->_convert($currencyFrom, $currencyTo, 1);
            } else {
                $this->_messages[] = Mage::helper('directory')->__('Cannot retrieve rate from %s.', $url);
                return null;
            }
        }
    }
}