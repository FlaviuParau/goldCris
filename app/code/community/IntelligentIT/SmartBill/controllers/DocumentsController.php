<?php

include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'ErrorController.php';

class IntelligentIT_SmartBill_DocumentsController extends Mage_Core_Controller_Front_Action
{
    // public function testAction() 
    // {
    //     $error = new IntelligentIT_SmartBill_Error_Processor();
    //     $error->processSmartBill('title', 'error');
    // }

    public function invoiceAction()
    {
        $magentoOrderNumber = $this->getRequest()->getParam('order_id');
        $orderDetails = $this->getOrderDetails($magentoOrderNumber);

        // check if order already exists
        $smartBillDocumentURL = $orderDetails->getSmartbillDocumentUrl();
        if (!empty($smartBillDocumentURL)) {
            header('Location: '.$smartBillDocumentURL);
            exit;
        }

        self::saveQuoteBasePrices($orderDetails);

        $postData = new stdClass;
        $postData->companyVatCode       = Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_COMPANY);
        $postData->client               = $this->getClientData($magentoOrderNumber);
        $postData->isDraft              = true;
        // $postData->issueDate         = $this->getOrderDate($magentoOrderNumber);
        $postData->issueDate            = date('Y-m-d', time());
        $postData->seriesName           = $this->getDocumentSeriesName();
        if (Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_DOCUMENT_TYPE)==IntelligentIT_SmartBill_Model_Config_DocumentTypeSelectionOptions::INVOICE_VALUE) {
            $postData->type = 'n';
        }
        $postData->currency             = Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_DOCUMENT_CURRENCY_DOC);
        $postData->currency             = $postData->currency=='9998' ? '' : $postData->currency;
        $postData->exchangeRate         = 1;
        $postData->language             = 'RO';
        $postData->precision            = 2;
        $postData->issuerName           = '';
        $postData->issuerCnp            = '';
        $postData->aviz                 = '';
        $postData->dueDate              = '';
        $postData->mentions             = 'comanda online #'.$orderDetails->getIncrementId();
        $postData->delegateAuto         = '';
        $postData->delegateIdentityCard = '';
        $postData->delegateName         = '';
        $postData->deliveryDate         = '';
        $postData->paymentDate          = '';
        // $postData->usePaymentTax        = (bool)Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_COMPANY_USE_PAYMENT_TAX);
        $postData->usePaymentTax        = $this->getCompanyUsePaymentTax($postData->companyVatCode);
        $postData->paymentTotal         = 0;
        $postData->paymentBase          = 0;
        $postData->colectedTax          = 0;
        $postData->orderNumber          = $orderDetails->getIncrementId();
        $postData->trackingNumber       = $this->getOrderTrackingData($magentoOrderNumber);
        $postData->products             = $this->getOrderProducts($magentoOrderNumber);
        // $postData->paymentTotal      = $this->getOrderTotal($magentoOrderNumber);
        // $postData->paymentTotal         = $this->getPaymentTotal($postData);
        $postData->documentTotal        = $orderDetails->getGrandTotal();
        if (Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_DOCUMENT_TYPE)==IntelligentIT_SmartBill_Model_Config_DocumentTypeSelectionOptions::INVOICE_VALUE) {
            $postData->useStock             = (bool)Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_COMPANY_ENABLE_STOCK);
            $warehouseName = strtolower( Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_WAREHOUSE) );
            $useStock = (bool)Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_USE_STOCK);
            if ( 'fara gestiune' == $warehouseName
              || empty($useStock) ) {
                $postData->useStock         = false;
            }
        }

        if (empty($postData->currency)
         && !empty($postData->products[0])) {
            $postData->currency         = $postData->products[0]->currency;
        }
        
        // attach last JSON data to order
        $orderDetails
            ->setSmartbillDocumentJson(json_encode($postData))
            ->save();

        switch (Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_DOCUMENT_TYPE)) {
            case IntelligentIT_SmartBill_Model_Config_DocumentTypeSelectionOptions::PROFORMA_VALUE:
                $apiURL = IntelligentIT_SmartBill_Helper_Public::PROFORMA_URL;
                break;

            default:
                $apiURL = IntelligentIT_SmartBill_Helper_Public::INVOICE_URL;
                break;
        }
// var_dump(Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_COMPANY_ENABLE_STOCK));
// echo $apiURL;
// print_r($postData);
// echo json_encode($postData);
// die();
        $invoiceResponse = Mage::helper('smartbill/Public')->curl($apiURL, $postData, array("Content-Type: application/json; charset=utf-8","Accept:application/json","Authorization: ECSDigest ".Mage::helper('smartbill/Public')->getAuthorization()), false, false);
        $invoiceResponse = json_decode($invoiceResponse);
        if (!empty($invoiceResponse->url)) {
            header('Location: '.$invoiceResponse->url);
            exit;
        }
    }

    public function saveQuoteBasePrices($orderDetails)
    {
        $itemsPrices = array();
        $orderItems = $orderDetails
                        ->getItemsCollection()
                        ->addAttributeToSelect('*')
                            ->addAttributeToFilter('product_type', array('eq'=>'simple'))
                            ->addAttributeToFilter('product_type', array('eq'=>'grouped'))
                            ->addAttributeToFilter('product_type', array('eq'=>'virtual'))
                            ->addAttributeToFilter('product_type', array('eq'=>'downloadable'))
                            ->addAttributeToFilter('product_type', array('eq'=>'configurable'))
                        ->load();
        foreach($orderItems as $sItem) {
            if ( !in_array($sItem->getProductType(), array('simple', 'grouped', 'virtual', 'downloadable', 'configurable')) ) continue;

            $uid      = $sItem->getProductId();
            $storeId  = $sItem->getStoreId();
            $_product = self::getProductByID($uid, $storeId);
            // $itemsPrices[$sku] = self::getProductPriceWithTaxesBySKU($sku);
            // $itemsPrices[$sku] = $_product->getPrice();
            $itemsPrices[$_product->getSku()] = $_product->getParentItem() ? $_product->getParentItem()->getPrice() : $_product->getPrice();
        }

        // $taxSettings = array(
        //     'XML_PATH_MAGENTO_PRICE_INCLUDE_TAX'    => Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_MAGENTO_PRICE_INCLUDE_TAX),
        //     'XML_PATH_MAGENTO_DISCOUNT_INCLUDE_TAX' => Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_MAGENTO_DISCOUNT_INCLUDE_TAX),
        //     'XML_PATH_MAGENTO_SHIPPING_INCLUDE_TAX' => Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_MAGENTO_SHIPPING_INCLUDE_TAX),
        // );
        $taxSettings = array(
            'XML_PATH_MAGENTO_DISCOUNT_INCLUDE_TAX'     => Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_MAGENTO_DISCOUNT_INCLUDE_TAX),
            'XML_PATH_MAGENTO_CALCULATION_ALGORITHM'    => Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_MAGENTO_CALCULATION_ALGORITHM),
            'XML_PATH_MAGENTO_CALCULATION_BASED_ON'     => Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_MAGENTO_CALCULATION_BASED_ON),
            'XML_PATH_MAGENTO_PRICE_INCLUDE_TAX'        => Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_MAGENTO_PRICE_INCLUDE_TAX),
            'XML_PATH_MAGENTO_SHIPPING_INCLUDE_TAX'     => Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_MAGENTO_SHIPPING_INCLUDE_TAX),
            'XML_PATH_MAGENTO_APPLY_AFTER_DISCOUNT'     => Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_MAGENTO_APPLY_AFTER_DISCOUNT),
            'XML_PATH_MAGENTO_APPLY_TAX_ON'             => Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_MAGENTO_APPLY_TAX_ON),
            'XML_PATH_MAGENTO_DISPLAY_TYPE'             => Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_MAGENTO_DISPLAY_TYPE),
            'XML_PATH_MAGENTO_DISPLAY_SHIPPING'         => Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_MAGENTO_DISPLAY_SHIPPING),
            'XML_PATH_MAGENTO_CART_DISPLAY_PRICE'       => Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_MAGENTO_CART_DISPLAY_PRICE),
            'XML_PATH_MAGENTO_CART_DISPLAY_SUBTOTAL'    => Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_MAGENTO_CART_DISPLAY_SUBTOTAL),
            'XML_PATH_MAGENTO_CART_DISPLAY_SHIPPING'    => Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_MAGENTO_CART_DISPLAY_SHIPPING),
            'XML_PATH_MAGENTO_CART_DISPLAY_GRANDTOTAL'  => Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_MAGENTO_CART_DISPLAY_GRANDTOTAL),
            'XML_PATH_MAGENTO_SALES_DISPLAY_PRICE'      => Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_MAGENTO_SALES_DISPLAY_PRICE),
            'XML_PATH_MAGENTO_SALES_DISPLAY_SUBTOTAL'   => Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_MAGENTO_SALES_DISPLAY_SUBTOTAL),
            'XML_PATH_MAGENTO_SALES_DISPLAY_SHIPPING'   => Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_MAGENTO_SALES_DISPLAY_SHIPPING),
            'XML_PATH_MAGENTO_SALES_DISPLAY_GRANDTOTAL' => Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_MAGENTO_SALES_DISPLAY_GRANDTOTAL),
        );

        // attach base items prices to order
        $orderDetails
            ->setSmartbillOrderItemsPrices(json_encode($itemsPrices))
            ->setSmartbillTaxSettings(json_encode($taxSettings))
            ->save();        
    }    

    public function statusAction()
    {
        // @file_put_contents(dirname(__FILE__).'/statusPOST.txt', print_r($_POST, true), FILE_APPEND);
        // @file_put_contents(dirname(__FILE__).'/statusGET.txt', print_r($_GET, true), FILE_APPEND);
        // @file_put_contents(dirname(__FILE__).'/statusINPUT.txt', file_get_contents("php://input") . "\r\n\r\n", FILE_APPEND);

        $inputData = file_get_contents("php://input");
        $inputData = self::decryptData($inputData);
	    Mage::log($inputData, null, 'inputData2.log');
        // @file_put_contents(dirname(__FILE__).'/statusINPUTdecr.txt', "\r\n".$inputData, FILE_APPEND);
// $inputData = '{"documentNumber":"0032","ecsId":"1","orderNumber":"100000225","seriesName":"FACT","url":"http://cloud.smartbill.ro/documente/vizualizare/factura/1444533"}';
        $dataJSON = json_decode($inputData);

        if (empty($inputData)
         || empty($dataJSON->orderNumber))  return;

        if (!empty($dataJSON->url)) {
            $urlDetails = parse_url($dataJSON->url);

            if (!empty($urlDetails)
             && !preg_match(IntelligentIT_SmartBill_Helper_Public::DOMAIN_VALID, $urlDetails['host']) ) return;
        }

        // $orderDetails = Mage::getModel('sales/order')->load($dataJSON->orderNumber);
        $orderDetails = Mage::getModel('sales/order')->loadByIncrementId($dataJSON->orderNumber);
        // update data only if the order exists
        if ( $orderDetails->getEntityId() ) {
            $orderDetails
                ->setSmartbillDocumentUrl($dataJSON->url)
                ->setSmartbillDocumentSeries($dataJSON->seriesName)
                ->setSmartbillDocumentNumber($dataJSON->documentNumber)
                ->setSmartbillExternalDocumentUrl($dataJSON->externalViewUrl);
            if ($orderDetails->getState() == Mage_Sales_Model_Order::STATE_NEW  && $orderDetails->getStatus() == 'pending') {
                $orderDetails
                    ->setState(Mage_Sales_Model_Order::STATE_PROCESSING)
                    ->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING);
            }

            if (Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_EMAIL_INVOICE) && !$orderDetails->getSmartbillInvoiceEmailSent()) {
                $this->sendEmail($orderDetails);
                $orderDetails->setSmartbillInvoiceEmailSent(1);
            }

            $orderDetails->save();
        }
    }

    private function sendEmail($orderDetails)
    {
        $templateId = Mage::getStoreConfig('settings/docssettingsdata/template');

        $sender['name'] = Mage::getStoreConfig('trans_email/ident_general/name');
        $sender['email'] = Mage::getStoreConfig('trans_email/ident_general/email');

        $emailTemplateVariables = array();
        $emailTemplateVariables['external_url'] = $orderDetails->getSmartbillExternalDocumentUrl();
        $emailTemplateVariables['orderNumber'] = $orderDetails->getIncrementId();

        $storeId = Mage::app()->getStore()->getId();
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(true);
        $transactionalEmail = Mage::getModel('core/email_template')
            ->setDesignConfig(array('area' => 'frontend', 'store' => $storeId));

        $recipientEmail = $orderDetails->getCustomerEmail();

        $transactionalEmail->sendTransactional($templateId, $sender, $recipientEmail,  $sender['name'], $emailTemplateVariables, $storeId);
    }

    private function decryptData($data) {
        $decrypted = '';

        try {
            $publicKey = self::getPublicKey();
            $res = openssl_get_publickey($publicKey);
            openssl_public_decrypt(self::hex2bin($data), $decrypted, $res);
        } catch(Exception $e) {}
        
        return $decrypted;
    }

    private function getPublicKey() {
        $publicKey = @file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.IntelligentIT_SmartBill_Helper_Public::PUBLIC_KEY_FILENAME);

        return $publicKey;        
    }

    private function hex2bin($hexdata) {
        $bindata = "";

        for ($i = 0; $i < strlen($hexdata); $i += 2) {
            $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
        }

        return $bindata;
    }

    public function debugAction()
    {
        $requestOrderIDs = Mage::app()->getRequest()->getParam('order_ids');

        if (!empty($requestOrderIDs)) {
            $orderIDs = explode(',', $requestOrderIDs);
            foreach ($orderIDs as $key => $orderID) {
                $orderDetails = $this->getOrderDetails($orderID);
                $documentJson = $orderDetails->getSmartbillDocumentJson();
                $taxSettings = $orderDetails->getSmartbillTaxSettings();

                if (!empty($documentJson)) {
                    $this->emailDebug($documentJson, $taxSettings, $orderDetails->getIncrementId());
                }
            }

            Mage::getSingleton('adminhtml/session')->addSuccess('Datele au fost trimise ptr debugging');
        } else {
            Mage::getSingleton('adminhtml/session')->addError('Selectati comenzile care contin erori');
            // Mage::throwException('Selectati comenzile care contin erori');
        }

        session_write_close();
        Mage::app()->getResponse()
            ->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order"))
            ->sendResponse();
    }

    private function emailDebug($dataJSON, $taxesJSON, $orderID) {
        $body  = 'LOGIN EMAIL: '.Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_USERNAME)."\n\r";
        $body .= 'URL: '.IntelligentIT_SmartBill_Model_ObserverLogin::getStoreURL()."\n\r";
        $body .= 'ORDER: '.$orderID."\n\r";
        $body .= 'JSON (taxes): '.$taxesJSON."\n\r";
        $body .= 'JSON: '.$dataJSON;
        $toEmails = explode(',', Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_SUPPORT_EMAIL));

        if (empty($toEmails)) {
            Mage::getSingleton('adminhtml/session')->addError('Lipsa adresa email ptr debug');
            // Mage::throwException('Lipsa adresa email ptr debug');
        }

        foreach ($toEmails as $key => $emalTo) {
            $mail = Mage::getModel('core/email');
            // $mail->setToName('John Customer');
            $mail->setToEmail($emalTo);
            $mail->setBody($body);
            $mail->setSubject(sprintf(IntelligentIT_SmartBill_Helper_Public::DEBUG_EMAIL_SUBJECT, '#'.$orderID));
            // $mail->setFromEmail(IntelligentIT_SmartBill_Helper_Public::DEBUG_EMAIL_FROM);
            // $mail->setFromName("Your Name");
            $mail->setType('text');// You can use 'html' or 'text'

            try {
                $mail->send();
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError('Probleme la trimiterea datelor');
                // Mage::throwException('Probleme la trimiterea datelor');
            }        
        }
    }

    private function getCompanyUsePaymentTax($vatCode) {
        $usePaymentTax = null;
        $settingsResponse = Mage::helper('smartbill/Public')->curl(IntelligentIT_SmartBill_Helper_Public::SETTINGS_URL, null, array("Content-Type: application/json; charset=utf-8","Accept:application/json","Authorization: ECSDigest ".Mage::helper('smartbill/Public')->getAuthorization()), false, true, false);

        if (!empty($settingsResponse)) {
            $settingsResponse = json_decode($settingsResponse);
            $company = Mage::helper('smartbill/Public')->getCompanyDetailsByVatCode($settingsResponse->companies);

            if (isset($company->usePaymentTax)) {
                $usePaymentTax = $company->usePaymentTax;
            }
        }

        return $usePaymentTax;        
    }

    private function getPaymentTotal($postData) {
        $paymentTotal = 0;

        foreach ($postData->products as $product) {
            $paymentTotal += $product->price*$product->quantity;
            $paymentTotal += isset($product->discountValue) ? $product->discountValue : 0;
        }

        return $paymentTotal;
    }

    // private function getOrderDate($orderNo) {
    //     $order = $this->getOrderDetails($orderNo);

    //     switch (Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_DOCUMENT_DATE)) {
    //         case IntelligentIT_SmartBill_Helper_Public::DOCUMENT_DATE_ORDER:
    //             $orderDate = strtotime($order->getCreatedAt());
    //             break;
            
    //         default:
    //             $orderDate = time();
    //             break;
    //     }

    //     return date('Y-m-d', $orderDate);
    // }

    // private function getOrderTotal($orderNo) 
    // {
    //     $order = $this->getOrderDetails($orderNo);
    //     $total = $order->getGrandTotal();

    //     if (!Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_ORDER_INCLUDE_TRANSPORT)) {
    //         $total -= $this->getOrderShipping($orderNo);
    //     }

    //     return floatval($total);
    // }

    private function getOrderShipping($orderNo) 
    {
        $order = $this->getOrderDetails($orderNo);

        return $order->getShippingAmount();
    }

    private function getOrderTrackingData($orderNo) 
    {
        $awbs = array();

        $order = $this->getOrderDetails($orderNo);
        $shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
                                ->setOrderFilter($order)
                                ->load();
        foreach ($shipmentCollection as $shipment){
            $tracking = $shipment->getAllTracks();

            if (!empty($tracking[0])) {
                $awbs[] = $tracking[0]->getTitle().': '.$tracking[0]->getTrackNumber();
            }
        }
        // http://stackoverflow.com/questions/5737276/magento-tracking-number-from-order-object

        return implode('; ', $awbs);
    }

    private function getClientData($orderNo) 
    {
        $order = $this->getOrderDetails($orderNo);
        $customerData = $order->getBillingAddress();

        $client = new stdClass;
        $client->vatCode    = self::_getVatCode($customerData);
        $client->name       = $customerData->getCompany();
        $client->name       = trim($client->name);
        $clientClean        = preg_replace('/[^a-z0-9]+/i', '',$client->name);
        $client->name       = empty($client->name) || empty($clientClean) ? $customerData->getName() : $client->name;
        $client->code       = ''; // TODO (doesn't exist)
        $client->address    = implode(', ', $customerData->getStreet());
        $client->regCom     = ''; // TODO (doesn't exist)
        $client->isTaxPayer = false; // TODO (doesn't exist/might be based on VIES check but not 100% correct)
        $client->contact    = $customerData->getName();
        $client->phone      = $customerData->getTelephone();
        $client->city       = $customerData->getCity();
        $client->county     = $customerData->getRegion();
        $client->county     = empty($client->county) ? '-' : $client->county;
        $client->country    = self::getCountryLabel($customerData->getCountry());
        $client->email      = $customerData->getEmail();
        $client->bank       = '';
        $client->iban       = '';
        $client->saveToDb   = (bool)Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_COMPANY_SAVE_CLIENT); // TODO (cateodata daca este trimis se primeste measj "Clientul exista deja pe server.")

        return $client;
    }
    private function _getVatCode($customerData) {
        $vatCode = trim($customerData->getVatId());
        $vatCodeClean = preg_replace('/[^0-9]+/i', '', $vatCode);

        if (empty($vatCodeClean)) {
            $vatCode = '-';
        }

        return $vatCode;
    }

    private function getCountryLabel($countryValue)
    {
        $countryLabel = $countryValue;
        $countries = self::getAllCountries();

        foreach ($countries as $key => $item) {
            if ($item['value']==$countryValue) {
                $countryLabel = $item['label'];
                break;
            }
        }

        $countryLabel = strtr(utf8_decode($countryLabel), utf8_decode('ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ'), 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
        return $countryLabel;
    }

    private function getAllCountries() 
    {
        return Mage::getModel('directory/country')->getResourceCollection()->loadByStore()->toOptionArray(true);
    }

    private function getDocumentSeriesName() 
    {
        $seriesName = Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_INVOICE_SERIES);

        switch (Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_DOCUMENT_TYPE)) {
            case IntelligentIT_SmartBill_Model_Config_DocumentTypeSelectionOptions::PROFORMA_VALUE:
                $seriesName = Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_PROFORMA_SERIES);
                break;
        }

        return $seriesName;
    }

    private function getOrderProducts($orderNo) 
    {
        $products = array();
        $order = $this->getOrderDetails($orderNo);

        // order items
        $orderItems = $order->getItemsCollection()
                            ->addAttributeToSelect('*')
                            ->addAttributeToFilter('product_type', array('eq'=>'simple'))
                            ->addAttributeToFilter('product_type', array('eq'=>'grouped'))
                            ->addAttributeToFilter('product_type', array('eq'=>'virtual'))
                            ->addAttributeToFilter('product_type', array('eq'=>'downloadable'))
                            ->addAttributeToFilter('product_type', array('eq'=>'configurable'))
                            ->load();
        foreach($orderItems as $sItem) {
            $productType = $sItem->getProductType();
            $parentItemID= $sItem->getParentItemId();
            if ( !in_array($productType, array('simple', 'grouped', 'virtual', 'downloadable', 'configurable'))
              || !empty($parentItemID) ) continue;

            $item = Mage::getModel('sales/order_item')->load($sItem->getId());

            // based on initial price of the product (if happens that in magento is configured tier price per product)
            $productDiscountBasePrice = $this->createOrderProductDiscountFromBasePrice($sItem, $this->getItemQuantity($item), $order);
            if (!empty($productDiscountBasePrice)) {
                $products[] = $this->createOrderProduct($sItem, $this->getItemQuantity($item), $order);
                $products[] = $productDiscountBasePrice;
            } else {
                $products[] = $this->createOrderProduct($sItem, $this->getItemQuantity($item), $order, false);
            }

            // from magento
            $productDiscount = $this->createOrderProductDiscount($sItem, $this->getItemQuantity($item), !empty($productDiscountBasePrice) ? 1 : 0, $order->getSmartbillTaxSettings(), $order);
            if (!empty($productDiscount)) {
                $products[] = $productDiscount;
            }
        }

        // shipping
        if (Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_ORDER_INCLUDE_TRANSPORT)) {
            $shipping = $this->createOrderTransport('shipping', 'Transport', 1, $order);

            if  ( !empty($shipping->price) ) {
                $products[] = $shipping;
            }
        }
        
        return $products;
    }

    private function getItemQuantity($item) {
        switch (Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_ORDER_QTY_SOURCE)) {
            case 1:
                $quantity = $item->getQtyInvoiced();
                break;
            
            case 2:
                $quantity = $item->getQtyShipped();
                break;
            
            default:
                $quantity = $item->getQtyOrdered();
                break;
        }

        return $quantity;
    }

    private function getProductByID($id, $storeId=0) {
        return Mage::getModel('catalog/product')
                ->setStoreId($storeId)
                ->load($id);
    }
    // private function getProductPriceWithTaxes($_product) {
    //     return Mage::helper('tax')->getPrice($_product, $_product->getFinalPrice());
    // }
    // private function getProductPriceWithoutTaxes($_product) {
    //     return Mage::helper('tax')->getPrice($_product, $_product->getPrice());
    // }
    // private function getProductPriceWithTaxesBySKU($sku) {
    //     $_product = $this->getProductBySKU($sku);
    //     return $this->getProductPriceWithTaxes($_product);
    // }

    private function _productPrice($price, $orderItem) {
        $taxPercentage = floatval(Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_PRODUCTS_VAT));
        if ( !empty($taxPercentage)
          && (int)Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_COMPANY_IS_TAX_PAYER) ) {
            $price = $orderItem->original_price;
        }

        return $price;
    }

    private function _productName($orderItem) {
        $productName = $orderItem->getName();

        if ( "configurable" == $orderItem->getProductType() ) {
            $options = $orderItem->getProductOptions();

            if ( !empty($options['attributes_info'])
              && is_array($options['attributes_info']) ) {
                $extraAttributes = array();

                foreach ($options['attributes_info'] as $option) {
                    $extraAttributes[] = $option['label'] . ': ' . $option['value'];
                }

                $productName .= ' (' . implode('; ', $extraAttributes) . ')';
            }
        }

        if ( "simple" == $orderItem->getProductType() ) {
            $options = $orderItem->getProductOptions();

            if ( !empty($options['options'])
              && is_array($options['options']) ) {
                $extraAttributes = array();

                foreach ($options['options'] as $option) {
                    $extraAttributes[] = $option['label'] . ': ' . $option['value'];
                }

                $productName .= ' (' . implode('; ', $extraAttributes) . ')';
            }
        }

        return $productName;
    }

    private function createOrderProduct($orderItem, $quantity, $order, $basePrice=true) {
        $productSKU = $orderItem->getSku();
        // $itemsPrices = json_decode($order->getSmartbillOrderItemsPrices());
        // $baseItemPrice = !empty($itemsPrices->$productSKU) ? $itemsPrices->$productSKU : ( $orderItem->getParentItem() ? $orderItem->getParentItem()->getPrice() : $orderItem->getPrice() );
        if ( !empty($basePrice) ) {
            $baseItemPrice = $orderItem->getParentItem() ? $orderItem->getParentItem()->getBaseOriginalPrice() : $orderItem->getBaseOriginalPrice();
        } else {
            $baseItemPrice = $orderItem->getParentItem() ? $orderItem->getParentItem()->getPrice() : $orderItem->getPrice();

            if ( (bool)Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_PRICE_INCLUDE_VAT) ) {
                $baseItemPrice = $orderItem->getParentItem() ? $orderItem->getParentItem()->getPriceInclTax() : $orderItem->getPriceInclTax();
            }
        }

        $measuringUnit = Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_ORDER_UNIT_TYPE);
        if ($measuringUnit == '@@@@@') {
            $attributeCode = Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_UNIT_ATTRIBUTE);

            if ($attributeCode) {
                $mageProduct = Mage::getModel('catalog/product')->load($orderItem->getProductId());
                $measuringUnit = $mageProduct->getData($attributeCode);
            }
        }
        $measuringUnit = trim($measuringUnit) == '' ? 'buc' : $measuringUnit;

        $product = new stdClass;
        $product->code                      = $productSKU;
        $product->currency                  = Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_DOCUMENT_CURRENCY);
        $product->exchangeRate              = 1;
        $product->isDiscount                = false;
        $product->isTaxIncluded             = (bool)Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_PRICE_INCLUDE_VAT);
        $product->measuringUnitName         = $measuringUnit;
        $product->name                      = $this->_productName($orderItem);
        // $product->price = floatval($orderItem->getPrice());
        $product->price                     = floatval($baseItemPrice);
        // $product->price                     = $this->_productPrice($product->price, $orderItem);
        // $product->price = $this->getProductPriceWithTaxesBySKU($orderItem->getSku());
        $product->quantity                  = $quantity;
        $product->saveToDb                  = (bool)Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_COMPANY_SAVE_PRODUCT);
        // $product->taxName = Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_PRODUCTS_VAT);
        $product->taxName                   = ''; // TODO (daca este trimisa se primeste mesaj "Cota tva a produsului XXX nu a fost gasita pe server!")
        $product->taxPercentage             = floatval(Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_PRODUCTS_VAT));
        if ($product->taxPercentage=='' && (int)Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_COMPANY_IS_TAX_PAYER)) {
            $product->taxPercentage = '9999';
        }
        $product->translatedMeasuringUnit   = '';
        $product->translatedName            = '';
        $product->warehouseName             = Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_WAREHOUSE);

        switch ($product->taxPercentage) {
            // din magento pe produse
            case 9998:
                $product->taxPercentage = floatval($orderItem->getData('tax_percent'));
                // $product->taxName = $orderItem->getTaxClassId();
                // $product->taxName = empty($product->taxName) ? '' : $product->taxName;
                break;

            // din smartbill pe produse
            case 9999:
                $product->taxName = '@@@@@';
                $product->taxPercentage = 0;
                break;
        }

        return $product;
    }

    private function createOrderProductDiscountFromBasePrice($orderItem, $quantity, $order) {
        $product = null;
        // $productSKU = $orderItem->getSku();
        // $itemsPrices = json_decode($order->getSmartbillOrderItemsPrices());
        // $baseItemPrice = !empty($itemsPrices->$productSKU) ? floatval($itemsPrices->$productSKU) : null;
        // if ( is_null($baseItemPrice) ) {
        //     $baseItemPrice = $orderItem->getBasePrice();
        // }

        $baseItemPrice = $orderItem->getBaseOriginalPrice();

        $thisItemPrice = $orderItem->getPrice();
        if ( (bool)Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_PRICE_INCLUDE_VAT) ) {
            $thisItemPrice = $orderItem->getPriceInclTax();
        }

        if (!empty($baseItemPrice)
         && round($baseItemPrice, 2) != round($thisItemPrice, 2)
         && round($baseItemPrice, 2) > round($thisItemPrice, 2)) {
            $product = new stdClass;
            $product->code                      = $orderItem->getSku();
            $product->currency                  = Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_DOCUMENT_CURRENCY);
            $product->exchangeRate              = 1;
            $product->isDiscount                = true;
            $product->discountPercentage        = 0;
            $product->discountValue             = $quantity*($thisItemPrice-$baseItemPrice);
            $product->discountType              = 1;
            $product->isTaxIncluded             = (bool)Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_PRICE_INCLUDE_VAT);
            $product->measuringUnitName         = 'buc';
            $product->name                      = 'Discount (pret special): ' . $this->_productName($orderItem);
            $product->price                     = 0;
            $product->quantity                  = 1;
            $product->numberOfItems             = 1;
            $product->saveToDb                  = false;
            // $product->taxName = Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_PRODUCTS_VAT);
            $product->taxName                   = ''; // TODO (daca este trimisa se primeste mesaj "Cota tva a produsului XXX nu a fost gasita pe server!")
            $product->taxPercentage             = floatval(Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_PRODUCTS_VAT));
            if ($product->taxPercentage=='' && (int)Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_COMPANY_IS_TAX_PAYER)) {
                $product->taxPercentage = '9999';
            }
            $product->discountTaxValue          = $product->discountValue*$product->taxPercentage/100; // TODO: why needed to be calculated on the client side? as long as we have isTaxIncluded :)
            $product->translatedMeasuringUnit   = '';
            $product->translatedName            = '';
            $product->warehouseName             = Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_WAREHOUSE);

            if ($product->isTaxIncluded) {
                $product->discountTaxValue = $product->discountValue-$product->discountValue/(1+$product->taxPercentage/100);
            }

            switch ($product->taxPercentage) {
                // din magento pe produse
                case 9998:
                    $product->taxPercentage = floatval($orderItem->getData('tax_percent'));
                    if ($product->isTaxIncluded) {
                        $product->discountTaxValue = $product->discountValue-$product->discountValue/(1+$orderItem->getData('tax_percent')/100);
                    } else {
                        $product->discountTaxValue = $product->discountValue*$orderItem->getData('tax_percent')/100;
                    }
                    // $product->taxName = $orderItem->getTaxClassId();
                    // $product->taxName = empty($product->taxName) ? '' : $product->taxName;
                    break;

                // din smartbill pe produse
                case 9999:
                    $product->taxName = '@@@@@';
                    $product->taxPercentage = 0;
                    $product->discountTaxValue = 0;
                    break;
            }
        }

        return $product;
    }

    private function createOrderProductDiscount($orderItem, $quantity, $extraProducts=0, $orderTaxSettings = null, $order) {
        if (!floatval($orderItem->getData('discount_percent'))
         && !floatval($orderItem->getData('discount_amount')))    return false;

        $couponCode = $order->getCouponCode();
    
        $product = new stdClass;
        $product->code                      = '';
        $product->currency                  = Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_DOCUMENT_CURRENCY);
        $product->exchangeRate              = 1;
        $product->isDiscount                = true;
        $product->discountPercentage        = floatval($orderItem->getData('discount_percent'));
        $product->discountValue             = -floatval($orderItem->getData('discount_amount'));
        $product->discountType              = !empty($product->discountValue) ? 1 : 2;
        //$product->isTaxIncluded             = true;
         $product->isTaxIncluded             = (bool)Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_PRICE_INCLUDE_VAT);
        // if ((bool)Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_MAGENTO_PRICE_INCLUDE_TAX)!=(bool)Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_MAGENTO_DISCOUNT_INCLUDE_TAX)) {
        //     $product->isTaxIncluded = (bool)Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_MAGENTO_DISCOUNT_INCLUDE_TAX);
        // }
        $product->measuringUnitName         = 'buc';
        $product->name                      = 'Discount' . (!empty($couponCode) ? ' (' . $order->getCouponCode() . ')' : ': '. $this->_productName($orderItem));
        $product->price                     = 0;
        $product->quantity                  = $quantity;
        $product->numberOfItems             = 1+$extraProducts;
        $product->saveToDb                  = false;
        // $product->taxName = Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_PRODUCTS_VAT);
        $product->taxName                   = ''; // TODO (daca este trimisa se primeste mesaj "Cota tva a produsului XXX nu a fost gasita pe server!")
        $product->taxPercentage             = floatval(Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_PRODUCTS_VAT));
        if ($product->taxPercentage=='') {
            $product->taxPercentage = '9999';
        }        
        $product->discountTaxValue          = $product->discountValue*$product->taxPercentage/100; // TODO: why needed to be calculated on the client side? as long as we have isTaxIncluded :)
        $product->translatedMeasuringUnit   = '';
        $product->translatedName            = '';
        $product->warehouseName             = Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_WAREHOUSE);

        if ($product->isTaxIncluded) {
            $product->discountTaxValue = $product->discountValue-$product->discountValue/(1+$product->taxPercentage/100);
        }

        switch ($product->taxPercentage) {
            // din magento pe produse
            case 9998:
                $product->taxPercentage = floatval($orderItem->getData('tax_percent'));
                if ($product->isTaxIncluded) {
                    $product->discountTaxValue = $product->discountValue-$product->discountValue/(1+$orderItem->getData('tax_percent')/100);
                } else {
                    $product->discountTaxValue = $product->discountValue*$orderItem->getData('tax_percent')/100;
                }
                // $product->taxName = $orderItem->getTaxClassId();
                // $product->taxName = empty($product->taxName) ? '' : $product->taxName;
                break;

            // din smartbill pe produse
            case 9999:
                $product->taxName = '@@@@@';
                $product->taxPercentage = 0;
                $product->discountTaxValue = 0;
                break;
        }

        if (!empty($orderTaxSettings)) {
            $orderTaxSettings = json_decode($orderTaxSettings);

            if (isset($orderTaxSettings->XML_PATH_MAGENTO_PRICE_INCLUDE_TAX)
             && isset($orderTaxSettings->XML_PATH_MAGENTO_DISCOUNT_INCLUDE_TAX)
             && $orderTaxSettings->XML_PATH_MAGENTO_PRICE_INCLUDE_TAX!=$orderTaxSettings->XML_PATH_MAGENTO_DISCOUNT_INCLUDE_TAX) {
                if ($orderTaxSettings->XML_PATH_MAGENTO_DISCOUNT_INCLUDE_TAX) {
                    $product->discountValue /= (1+$product->taxPercentage/100);
                    $product->discountTaxValue /= (1+$product->taxPercentage/100);
                } else {
                    $product->discountValue *= (1+$product->taxPercentage/100);
                    $product->discountTaxValue *= (1+$product->taxPercentage/100);
                }
            }
        }

        // if (!$product->isTaxIncluded
        //  && !empty($product->discountValue)) {
        //     $product->discountValue += $product->discountTaxValue;
        // }

        return $product;
    }

    private function createOrderTransport($code, $name, $quantity, $order) {
        $transportTaxDetails = $this->getTransportTaxDetails();

        $surchargeAmount = 0;
        if ($order->getData('fooman_surcharge_amount')) {
            $surchargeAmount = $order->getData('fooman_surcharge_amount');
        }

        $product = new stdClass;
        $product->code                      = $code;
        $product->currency                  = Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_DOCUMENT_CURRENCY);
        $product->exchangeRate              = 1;
        $product->isDiscount                = false;
        $product->isTaxIncluded             = (bool)Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_TRANSPORT_INCLUDE_VAT);
        $product->measuringUnitName         = 'buc';
        $product->name                      = $name;
        $product->price                     = floatval($order->getShippingAmount() + $surchargeAmount);
        if ((bool)Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_MAGENTO_SHIPPING_INCLUDE_TAX)) {
            // $product->price                 += floatval($order->getShippingTaxAmount());
            $product->price                 = floatval($order->getShippingInclTax() + $surchargeAmount);
        }
        $product->quantity                  = $quantity;
        $product->saveToDb                  = false;
        $product->taxName                   = $transportTaxDetails[0];
        $product->taxPercentage             = floatval($transportTaxDetails[1]);
        $product->translatedMeasuringUnit   = '';
        $product->translatedName            = '';
        $product->warehouseName             = Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_WAREHOUSE);
        $product->isService                 = true;

        return $product;
    }

    private function getTransportTaxDetails() 
    {  
        $details = array('', null);

        $taxes = $this->getTransportTaxes();
        if (!empty($taxes[Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_TRANSPORT_VAT)])) {
            $details = $taxes[Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_TRANSPORT_VAT)];

            if ($details[1]===null || $details[1]==='') {
                $details[1] = '9999';
            }
        }

        return $details;
    }
    private function getTransportTaxes() 
    {
        $taxes = array();
        $settingsResponse = Mage::helper('smartbill/Public')->curl(IntelligentIT_SmartBill_Helper_Public::SETTINGS_URL, null, array("Content-Type: application/json; charset=utf-8","Accept:application/json","Authorization: ECSDigest ".Mage::helper('smartbill/Public')->getAuthorization()), false, true, false);

        if (!empty($settingsResponse)) {
            $settingsResponse = json_decode($settingsResponse);
            $company = Mage::helper('smartbill/Public')->getCompanyDetailsByVatCode($settingsResponse->companies);

            if (!empty($company->taxes)
             && is_array($company->taxes)) {
                foreach ($company->taxes as $key => $value) {
                    $taxes[$value->id] = array($value->name, $value->percentage);
                }
            }
        }

        return $taxes;
    }

    private function getProductWithAndWithoutTaxes() {

    }

    private function getProductTaxIncluded($product) {


    }

    private function getOrderDetails($orderNo) 
    {
        // return Mage::getModel('sales/order')->loadByIncrementId($orderNo);
        return Mage::getModel('sales/order')->load($orderNo);
    }

}