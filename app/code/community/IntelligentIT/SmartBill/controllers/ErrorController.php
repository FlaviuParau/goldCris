<?php

include_once Mage::getBaseDir().DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR.'processor.php';

class IntelligentIT_SmartBill_Error_Processor extends Error_Processor 
{
    public function processSmartBill($title, $message)
    {
        $this->pageTitle = $title;
        $this->pageMessage = $message;
        $this->_sendHeaders(404);
        $this->_renderPage('smartbill.phtml');
    }
}
