<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @category    Blugento
 * @package     Mobilpay_Cc
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mobilpay_Cc_Model_Recurrent_Dopayt extends Mage_Core_Model_Abstract
{
    /**
     * Make the request for recurring payment
     *
     * @param mixed $profile
     * @param mixed $order
     * @return bool
     */
    public function call($profile, $order)
    {
        /** @var Mobilpay_Cc_Model_Recurrent $recurrent */
        $recurrent = Mage::getModel('cc/recurrent');

        if ($recurrent->getConfigData('debug')) {
            $url = 'http://sandboxsecure.mobilpay.ro/api/payment2/?wsdl';
        } else {
            $url = 'https://secure.mobilpay.ro/api/payment2/?wsdl';
        }

        $soap = new SoapClient($url, Array('cache_wsdl' => WSDL_CACHE_NONE));
        $sacId = $recurrent->getConfigData('signature');

        $req = new stdClass();

        $account = new stdClass();
        $account->id = $sacId;
        $account->user_name = $recurrent->getConfigData('username'); //please ask mobilPay to upgrade the necessary access required for token payments
        $account->customer_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : ''; //the IP address of the buyer.
        $account->confirm_url = Mage::getUrl('cc/cc/ipnrecurrent/');  //this is where mobilPay will send the payment result. This has priority over the SOAP call response

        $transaction = new stdClass();
        $transaction->paymentToken = $profile->getReferenceId(); //you will receive this token together with its expiration date following a standard payment. Please store and use this token with maximum care

        $billingInfo = unserialize($profile->getBillingAddressInfo());

        $billing = new stdClass();
        $billing->country = Mage::app()->getLocale()->getCountryTranslation($billingInfo['country_id']);
        $billing->county = $billingInfo['region'];
        $billing->city = $billingInfo['city'];
        $billing->address = $billingInfo['street'];
        $billing->postal_code = $billingInfo['postcode'];
        $billing->first_name = $billingInfo['firstname'];
        $billing->last_name = $billingInfo['lastname'];
        $billing->phone = $billingInfo['telephone'];
        $billing->email = $billingInfo['email'];

        /*
            $shipping = new stdClass();
            $shipping->country = 'shipping_country';
            $shipping->county = 'shipping_county';
            $shipping->city = 'shipping_city';
            $shipping->address = 'shipping_address';
            $shipping->postal_code = 'shipping_postal_code';
            $shipping->first_name = 'shipping_first_name';
            $shipping->last_name = 'shipping_last_name';
            $shipping->phone = 'shipping_phone';
            $shipping->email = 'shipping_email';
        */

        $orderObj = new stdClass();
        $orderObj->id = $order->getIncrementId(); //your orderId. As with all mobilPay payments, it needs to be unique at seller account level
        $orderObj->description = $recurrent->getConfigData('description'); //payment descriptor
        $orderObj->amount = $order->getBaseGrandTotal(); // order amount; decimals present only when necessary, i.e. 15 not 15.00
        $orderObj->currency = $profile->getCurrencyCode(); //currency
        $orderObj->billing = $billing;
//        $orderObj->shipping = $shipping;

        $itemInfo = unserialize($profile->getOrderItemInfo());

        $params = new stdClass();
        $params->item = new stdClass();
        $params->item->name = $itemInfo['name'];
        $params->item->value = $itemInfo['base_original_price'];

        $account->hash = strtoupper(sha1(strtoupper(md5($recurrent->getConfigData('password'))) . "{$orderObj->id}{$orderObj->amount}{$orderObj->currency}{$account->id}"));

        $req->account = $account;
        $req->order = $orderObj;
        $req->params = $params;
        $req->transaction = $transaction;

        try
        {
            $response = $soap->doPayT(Array('request' => $req));

            if (isset($response->errors) && $response->errors->code != ERR_CODE_OK)
            {
                throw new SoapFault($response->code, $response->message);
            }
            return true;
        }
        catch(SoapFault $e)
        {
            Mage::logException($e);
            return false;
        }
    }
}