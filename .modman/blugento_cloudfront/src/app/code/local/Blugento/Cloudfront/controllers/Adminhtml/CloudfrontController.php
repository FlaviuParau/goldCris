<?php

class Blugento_Cloudfront_Adminhtml_CloudfrontController extends Mage_Adminhtml_Controller_Action
{
    public function invalidateAction()
    {
        $params = new Varien_Object($this->getRequest()->getParams());
        if ($params->getKey() && ($invalidationPath = $params->getInvalidationPath())) {
            $this->invalidation($invalidationPath);
        }
    }

    public function invalidateCssAction()
    {
        $invalidationPath = '/media/css_secure/*,/skin/frontend/*';
        $this->invalidation($invalidationPath);
    }

    public function invalidateJsAction()
    {
        $invalidationPath = '/js/*,/skin/frontend/blugento/default/js/*';
        $this->invalidation($invalidationPath);

        $this->_redirect('*/cache/index');
    }

    public function invalidateWysiwygAction()
    {
        $invalidationPath = '/media/wysiwyg/*';
        $this->invalidation($invalidationPath);
    }

    protected function invalidation($invalidationPath)
    {
        $invalidationPath = explode(',', trim($invalidationPath));

        $cloudFront = Mage::getModel('blugento_cloudfront/cloudfront');
        $isDebugMode = $cloudFront->isDebugMode();

        $result = $cloudFront->invalidate($invalidationPath);
        $response = $cloudFront->getResponse();
        $reponseData = array(
            'message' => $response->getMessage(),
            'body'    => $response->getBody()
        );

        $session = Mage::getSingleton('adminhtml/session');
        if (true === $result) {
            $successMessage = Mage::helper('blugento_cloudfront')->__('The purge is in progress and takes about to 10-15 minutes ').'<br />';
            if ($isDebugMode) {
                $successMessage .= print_r($reponseData, true);
            }
            $session->addSuccess($successMessage);
        } else {
            $errorMessage = Mage::helper('blugento_cloudfront')->__('An error has occured while purging the files').'<br />';
            if ($isDebugMode) {
                $errorMessage .= print_r($reponseData, true);
            }
            $session->addError($errorMessage);
        }
        $this->_redirect('*/cache/index');
    }
}
