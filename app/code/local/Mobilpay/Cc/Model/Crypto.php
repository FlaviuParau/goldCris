<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 */

class Mobilpay_Cc_Model_Crypto extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'mobilpay_crypto';

    protected $_infoBlockType = 'cc/info';
    protected $_formBlockType = 'cc/form';

    /**
     * Availability options
     */
    protected $_isGateway = true;
    protected $_canAuthorize = true;
    protected $_canCapture = false;
    protected $_canRefund = false;
    protected $_canRefundInvoicePartial = false;
    protected $_canReviewPayment = true;

    protected $formData;
    protected $formKey;
    protected $_order = null;
    protected $_objPmReq = null;
    protected $_newOrderStatus = null;

    public function capture(Varien_Object $payment, $amount)
    {

    }

    public function getOrderPlaceRedirectUrl()
    {

        $e = null;
        $shipping = $this->getQuote()->getShippingAddress();
        $billing = $this->getQuote()->getBillingAddress();
        $quote = $this->getQuote();

        $test = array(
            'a' => 1);

        try {
            $objPmReqCard = new Mobilpay_Payment_Request_Bitcoin();

            $objPmReqCard->signature = $this->getConfigData('signature');

            if ($this->getConfigData('debug') == 1) {
                $x509FilePath = Mage::getModuleDir('local', 'Mobilpay_Cc') . DS . "etc/certificates" . DS . "sandbox." . $this->getConfigData('signature') . ".public.cer";
            } else {
                $x509FilePath = Mage::getModuleDir('local', 'Mobilpay_Cc') . DS . "etc/certificates" . DS . "live." . $this->getConfigData('signature') . ".public.cer";
            }

            $objPmReqCard->orderId = $this->getQuote()->getReservedOrderId();

            $objPmReqCard->returnUrl = Mage::getUrl('cc/cc/success');
            $objPmReqCard->confirmUrl = Mage::getUrl('cc/cc/ipncrypto/');
            $objPmReqCard->cancelUrl = Mage::getUrl('cc/cc/cancel');

            $objPmReqCard->invoice = new Mobilpay_Payment_Invoice();

            $objPmReqCard->invoice->currency = $quote->getBaseCurrencyCode();
            $objPmReqCard->invoice->amount = $quote->getBaseGrandTotal();
            $cart_description = $this->getConfigData('description');
            if ($cart_description != '') $objPmReqCard->invoice->details = $cart_description;

            $billingAddress = new Mobilpay_Payment_Address();

            $company = $billing->getCompany();
            if (!empty($company)) {
                $billingAddress->type = 'company';
            } else {
                $billingAddress->type = 'person';
            }
            $billingAddress->firstName = $billing->getFirstname();
            $billingAddress->lastName = $billing->getLastname();

            //not supported by this shopping cart $billingAddress->fiscalNumber	= $_POST['billing_fiscal_number'];
            //not supported by this shopping cart $billingAddress->identityNumber	= $_POST['billing_identity_number'];


            $billingAddress->country = $billing->getCountry();

            $billingAddress->city = $billing->getCity();
            $billingAddress->zipCode = $billing->getPostcode();
            $billingAddress->state = $billing->getRegion();
            $billingAddress->address = $billing->getStreet(1);
            $billingAddress->email = $billing->getEmail();
            $billingAddress->mobilePhone = $billing->getTelephone();

            //not supported by this shopping cart $billingAddress->bank	= $_POST['billing_bank'];
            //not supported by this shopping cart $billingAddress->iban	= $_POST['billing_iban'];


            $objPmReqCard->invoice->setBillingAddress($billingAddress);

            $shippingAddress = new Mobilpay_Payment_Address();

            $company = $shipping->getCompany();
            if (!empty($company)) {
                $shippingAddress->type = 'company';
            } else {
                $shippingAddress->type = 'person';
            }
            $shippingAddress->firstName = $shipping->getFirstname();
            $shippingAddress->lastName = $shipping->getLastname();

            //not supported by this shopping cart $shippingAddress->fiscalNumber	= $_POST['shipping_fiscal_number'];
            //not supported by this shopping cart $shippingAddress->identityNumber	= $_POST['shipping_identity_number'];


            $shippingAddress->country = $shipping->getCountry();

            $shippingAddress->city = $shipping->getCity();
            $shippingAddress->zipCode = $shipping->getPostcode();
            $shippingAddress->state = $shipping->getRegion();
            $shippingAddress->address = $shipping->getStreet(1);
            $shippingAddress->email = $shipping->getEmail();
            $shippingAddress->mobilePhone = $shipping->getTelephone();

            $objPmReqCard->invoice->setShippingAddress($shippingAddress);

            $objPmReqCard->encrypt($x509FilePath);
        } catch (Exception $e) {
            $error = $e->getMessage();
            Mage::throwException($error);
        }

        if (! ($e instanceof Exception))
        {
            Mage::getSingleton('checkout/session')->setFormData($objPmReqCard->getEncData());
            Mage::getSingleton('checkout/session')->setFormKey($objPmReqCard->getEnvKey());
        } else
        {
            $error = $e->getMessage();
            Mage::throwException($error);
        }
        return Mage::getUrl('cc/cc/redirectcrypto', array(
            '_secure' => true));
    }

    function getFormData()
    {
        return Mage::getSingleton('checkout/session')->getFormData();
    }

    function getFormKey()
    {
        return Mage::getSingleton('checkout/session')->getFormKey();
    }

    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }
}
