<?php

class Blugento_Cloudfront_Model_Cloudfront extends Mage_Core_Model_Abstract
{
    const SERVICE_URL = 'https://cloudfront.amazonaws.com/2010-11-01/distribution/';
    const XML_PATH_CLOUDFRONT_ACTIVE = 'default/blugento_cloudfront/blugento_cloudfront/active';
    const XML_PATH_CLOUDFRONT_DISTRIBUTION_ID = 'default/blugento_cloudfront/blugento_cloudfront/aws_distribution_id';
    const XML_PATH_CLOUDFRONT_ACCESS_KEY = 'default/blugento_cloudfront/blugento_cloudfront/aws_access_key';
    const XML_PATH_CLOUDFRONT_SECRET_KEY = 'default/blugento_cloudfront/blugento_cloudfront/aws_secret_key';
    const XML_PATH_CLOUDFRONT_IS_DEBUG_MODE = 'default/blugento_cloudfront/blugento_cloudfront/debug_mode';
    const RESPONSE_CODE_SUCCESSFUL_CREATED = 201;

    protected $_httpClient;
    protected $_serviceUrl;
    protected $_signature;
    protected $_requestUrl;
    protected $_distributionId;
    protected $_accessKey;
    protected $_secretKey;
    protected $_date;
    protected $_response;
    protected $_isDebugMode;

    public function invalidate(array $keys)
    {
        $requestData = $this->_buildRequestData($keys);
        $httpClient = $this->getHttpClient();
        $httpClient->setHeaders('Content-Length', strlen($requestData));
        $httpClient->setRawData($requestData, 'text/xml');
        $response = $httpClient->request('POST');
        $this->setResponse($response);
        return (self::RESPONSE_CODE_SUCCESSFUL_CREATED == $response->getStatus());
    }
    
    public function getHttpClient()
    {
        if (!$this->_httpClient) {
            $this->_httpClient = new Zend_Http_Client();
            $this->_httpClient->setUri($this->getRequestUrl());
            $this->_httpClient->setHeaders('Date', $this->_getDate());
            $this->_httpClient->setHeaders('Authorization', $this->_getAuthorizationKey());
        }
        return $this->_httpClient;
    }

    protected function _buildRequestData(array $keys)
    {
        $requestData = '<InvalidationBatch>';
        foreach ($keys as $key) {
            $key = trim($key);
            $key = substr($key, 0, 1) == '/' ? $key : '/'.$key;
            $requestData .= '<Path>'.$key.'</Path>';
        }
        $requestData .= sprintf('<CallerReference>%s%s</CallerReference>', $this->getDistributionId(), date('U'));
        $requestData .= '</InvalidationBatch>';
        return $requestData;
    }
    
    protected function _getSignature()
    {
        if (!$this->_signature) {
            $this->_signature = base64_encode(hash_hmac('sha1', $this->_getDate(), $this->getSecretKey(), true));
        }
        return $this->_signature;
    }

    protected function _getAuthorizationKey()
    {
        $key = sprintf('AWS %s:%s', $this->getAccessKey(), $this->_getSignature());
        return $key;
    }

    protected function _getDate()
    {
        if (!$this->_date) {
            $this->_date = gmdate("D, d M Y G:i:s T");
        }
        return $this->_date;
    }
    
    public function getRequestUrl()
    {
        if (!$this->_requestUrl) {
            $this->_requestUrl = sprintf('%s%s/invalidation', self::SERVICE_URL, $this->getDistributionId());
        }
        return $this->_requestUrl;
    }
    
    public function getAccessKey()
    {
        if (!$this->_accessKey) {
            $this->_accessKey = (string)self::_getConfigurationValue(self::XML_PATH_CLOUDFRONT_ACCESS_KEY);
        }
        return $this->_accessKey;
    }
    
    public function getSecretKey()
    {
        if (!$this->_secretKey) {
            $this->_secretKey = (string)self::_getConfigurationValue(self::XML_PATH_CLOUDFRONT_SECRET_KEY);
        }
        return $this->_secretKey;
    }
    
    public function getDistributionId()
    {
        if (!$this->_distributionId) {
            $this->_distributionId = (string)self::_getConfigurationValue(self::XML_PATH_CLOUDFRONT_DISTRIBUTION_ID);
        }
        return $this->_distributionId;
    }
    
    static public function isEnabled()
    {
        $isEnabled = (int)self::_getConfigurationValue(self::XML_PATH_CLOUDFRONT_ACTIVE);
        return $isEnabled;
    }
    
    static public function isDebugMode()
    {
        $isDebugMode = (int)self::_getConfigurationValue(self::XML_PATH_CLOUDFRONT_IS_DEBUG_MODE);
        return $isDebugMode;
    }
    
    static protected function _getConfigurationValue($path)
    {
        return Mage::getConfig()->getNode($path);
    }
    
    public function setResponse(Zend_Http_Response $response)
    {
        $this->_response = $response;
    }
    
    public function getResponse()
    {
        return $this->_response;
    }
}
