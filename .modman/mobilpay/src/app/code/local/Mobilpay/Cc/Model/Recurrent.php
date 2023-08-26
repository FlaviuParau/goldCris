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

class Mobilpay_Cc_Model_Recurrent extends Mage_Payment_Model_Method_Abstract implements Mage_Payment_Model_Recurring_Profile_MethodInterface
{
    protected $_code = 'mobilpay_recurrent';

    /**
     * Availability options
     */
    protected $_isGateway                   = false;
    protected $_canOrder                    = false;
    protected $_canAuthorize                = false;
    protected $_canCapture                  = false;
    protected $_canCapturePartial           = false;
    protected $_canRefund                   = false;
    protected $_canRefundInvoicePartial     = false;
    protected $_canVoid                     = false;
    protected $_canUseInternal              = false;
    protected $_canUseCheckout              = true;
    protected $_canUseForMultishipping      = false;
    protected $_canFetchTransactionInfo     = true;
    protected $_canCreateBillingAgreement   = false;
    protected $_canReviewPayment            = true;
    protected $_canManageRecurringProfiles  = true;

    /**
     * This method "hides" payment method for simple checkout (without recurring products)
     *
     * @return boolean
     */
    public function canUseCheckout(){
        $cart = Mage::getModel('checkout/cart')->getQuote();
        foreach ($cart->getAllItems() as $item) {
            if (!$item->getProduct()->getIsRecurring())
                return false;
        }
        return true;
    }

    /**
     * Return order place redirect URL
     *
     * @return string
     * @throws Mage_Core_Exception
     */
    public function getOrderPlaceRedirectUrl()
    {
        $e = null;
        $shipping = $this->getQuote()->getShippingAddress();
        $billing = $this->getQuote()->getBillingAddress();
        $quote = $this->getQuote();
        $order = Mage::registry('mobilpay_recurring_order');

        Mage::unregister('mobilpay_recurring_order');

        try
        {
            $objPmReqCard = new Mobilpay_Payment_Request_Card();

            $objPmReqCard->signature = $this->getConfigData('signature');

            if ($this->getConfigData('debug') == 1) {
                $x509FilePath = Mage::getModuleDir('local', 'Mobilpay_Cc') . DS . "etc/certificates" . DS . "sandbox.".$this->getConfigData('signature').".public.cer";
            }
            else {
                $x509FilePath = Mage::getModuleDir('local', 'Mobilpay_Cc') . DS . "etc/certificates" . DS . "live.".$this->getConfigData('signature').".public.cer";
            }

            $objPmReqCard->orderId = $order->getIncrementId();

            $objPmReqCard->returnUrl = Mage::getUrl('cc/cc/success');
            $objPmReqCard->confirmUrl = Mage::getUrl('cc/cc/ipnrecurrent/');
            $objPmReqCard->cancelUrl = Mage::getUrl('cc/cc/cancel');

            $objPmReqCard->invoice = new Mobilpay_Payment_Invoice();

            $objPmReqCard->invoice->currency = $quote->getBaseCurrencyCode();
            $objPmReqCard->invoice->amount = $order->getBaseGrandTotal();
            $cart_description = $this->getConfigData('description');
            if ($cart_description != '') $objPmReqCard->invoice->details = $cart_description;

            $billingAddress = new Mobilpay_Payment_Address();

            $company = $billing->getCompany();
            if (! empty($company))
            {
                $billingAddress->type = 'company';
            } else
            {
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
            if (! empty($company))
            {
                $shippingAddress->type = 'company';
            } else
            {
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
        }

        catch (Exception $e) {
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
        return Mage::getUrl('cc/cc/redirectrecurrent', array(
            '_secure' => true));
    }

    /**
     * Validate data
     *
     * @param Mage_Payment_Model_Recurring_Profile $profile
     * @throws Mage_Core_Exception
     */
    public function validateRecurringProfile(Mage_Payment_Model_Recurring_Profile $profile)
    {

    }

    /**
     * Submit to the gateway
     *§
     * @param Mage_Payment_Model_Recurring_Profile $profile
     * @param Mage_Payment_Model_Info $paymentInfo
     */
    public function submitRecurringProfile(Mage_Payment_Model_Recurring_Profile $profile, Mage_Payment_Model_Info $payment)
    {
        $productItemInfo = new Varien_Object;
        $productItemInfo->setPaymentType(Mage_Sales_Model_Recurring_Profile::PAYMENT_TYPE_REGULAR);
        $productItemInfo->setPrice($profile->getBillingAmount());
        $productItemInfo->setTaxAmount($profile->getTaxAmount());
        $productItemInfo->setShippingAmount($profile->getShippingAmount());

        $order = $profile->createOrder($productItemInfo);
        $order->setStatus('pending');
        $order->save();

        Mage::register('mobilpay_recurring_order', $order);

        $profile->addOrderRelation($order->getId());
        $profile->setState(Mage_Sales_Model_Recurring_Profile::STATE_ACTIVE);
    }

    /**
     * Fetch details
     *
     * @param string $referenceId
     * @param Varien_Object $result
     */
    public function getRecurringProfileDetails($referenceId, Varien_Object $result)
    {

    }

    /**
     * Check whether can get recurring profile details
     *
     * @return bool
     */
    public function canGetRecurringProfileDetails()
    {

    }

    /**
     * Update data
     *
     * @param Mage_Payment_Model_Recurring_Profile $profile
     */
    public function updateRecurringProfile(Mage_Payment_Model_Recurring_Profile $profile)
    {

    }

    /**
     * Manage status
     *
     * @param Mage_Payment_Model_Recurring_Profile $profile
     */
    public function updateRecurringProfileStatus(Mage_Payment_Model_Recurring_Profile $profile)
    {
        $emailTemplate = '';
        if ($profile->getNewState() == 'suspended') {
            $emailTemplate = $this->getConfigData('suspended_email');
        } elseif ($profile->getNewState() == 'canceled') {
            $emailTemplate = $this->getConfigData('canceled_email');
        }

        if ($emailTemplate) {
            $this->_sendStatusChangedEmail($emailTemplate, $profile);
        }
    }

    /**
     * Cron will call this method for profiles that should be charged
     * This is not default recurring payment method
     *
     * @param Mage_Payment_Model_Recurring_Profile $profile
     * @return bool
     * @throws Exception
     */
    public function chargeRecurringProfile(Mage_Payment_Model_Recurring_Profile $profile)
    {
        $productItemInfo = new Varien_Object;
        
        if ($profile->getReferenceId()) {
            $productItemInfo->setPaymentType(Mage_Sales_Model_Recurring_Profile::PAYMENT_TYPE_REGULAR);
            $productItemInfo->setPrice($profile->getBillingAmount());
            $productItemInfo->setTaxAmount($profile->getTaxAmount());
            $productItemInfo->setShippingAmount($profile->getShippingAmount());

            $order = $profile->createOrder($productItemInfo);
            $order->setStatus('pending');
            $order->save();

            $profile->addOrderRelation($order->getId());
            $profile->setState(Mage_Sales_Model_Recurring_Profile::STATE_ACTIVE);

            /** @var Mobilpay_Cc_Model_Recurrent_Dopayt $dopayt */
            $dopayt = Mage::getModel('cc/recurrent_dopayt');

            return $dopayt->call($profile, $order);
        }
        return null;
    }

    /**
     * Process recurring profile after the payment is done
     *
     * @param string $token
     * @param string $increment
     */
    public function processRecurringProfile($token, $increment)
    {
        $profileId = $this->getProfileByOrderIncrement($increment);

        try {
            if ($profileId) {
                $profile = Mage::getModel('sales/recurring_profile')->load($profileId);
                $profile->setReferenceId($token);
                $profile->save();

                // change updated_at to one cycle ahead
                $this->_setUpdateDateToNextPeriod($profile->getId());
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Return profile id by order increment id
     *
     * @param string $increment
     * @return int|null
     */
    protected function getProfileByOrderIncrement($increment)
    {
        $sql = 'SELECT srpo.profile_id 
                FROM sales_flat_order sfo
                INNER JOIN sales_recurring_profile_order srpo
                ON sfo.entity_id = srpo.order_id
                WHERE sfo.increment_id = "' . $increment . '"';

        try {
            return $this->_getConnection()->fetchOne($sql);
        } catch (Exception $e) {
            Mage::logException($e);
            return null;
        }
    }

    /**
     * Set update date to next period
     *
     * @param int $profile_id
     * @return mixed
     */
    protected function _setUpdateDateToNextPeriod($profile_id)
    {
        $_resource = Mage::getSingleton('core/resource');
        $sql = '
			UPDATE '.$_resource->getTableName('sales_recurring_profile').'
			SET updated_at = CASE period_unit
				WHEN "day" 			THEN DATE_ADD(updated_at, INTERVAL period_frequency DAY)
				WHEN "week" 		THEN DATE_ADD(updated_at, INTERVAL (period_frequency*7) DAY)
				WHEN "semi_month" 	THEN DATE_ADD(updated_at, INTERVAL (period_frequency*14) DAY)
				WHEN "month" 		THEN DATE_ADD(updated_at, INTERVAL period_frequency MONTH)
				WHEN "year" 		THEN DATE_ADD(updated_at, INTERVAL period_frequency YEAR)
			END
			WHERE profile_id = :pid';

        $connection = $_resource->getConnection('core_write');
        $pdoStatement = $connection->prepare($sql);
        $pdoStatement->bindValue(':pid', $profile_id);
        return $pdoStatement->execute();
    }

    /**
     * Send email tp customer when a profile is suspended/canceled
     *
     * @param string $template
     * @param mixed $profile
     */
    private function _sendStatusChangedEmail($template, $profile)
    {
        $info = $profile->getOrderInfo();
        $referenceId = $profile->getReferenceId();

        if (strlen($referenceId) > 25) {
            $start = substr($referenceId, 0, 11);
            $end = substr($referenceId, -11);

            $referenceId = $start . '...' . $end;
        }

        $vars['reference_id'] = $referenceId;

        $recipientName = $info['customer_firstname'] . $info['customer_lastname'];
        $recipientEmail = $info['customer_email'];

        $sender['name'] = Mage::getStoreConfig('trans_email/ident_general/name');
        $sender['email'] = Mage::getStoreConfig('trans_email/ident_general/email');

        if (!$sender['name']) {
            $sender['name'] = 'Owner';
        }

        if (!$sender['email']) {
            $sender['email'] = 'owner@example.com';
        }
        if ($template) {
            try {
                $storeId = Mage::app()->getStore()->getId();
                $translate = Mage::getSingleton('core/translate');
                $translate->setTranslateInline(true);

                $transactionalEmail = Mage::getModel('core/email_template')
                    ->setDesignConfig(array('area' => 'frontend', 'store' => $storeId));

                $transactionalEmail->sendTransactional($template, $sender, $recipientEmail, $recipientName, $vars, $storeId);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
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

    /**
     * Retrieve connection
     *
     * @param null|string $type
     * @return mixed
     */
    private function _getConnection($type = null)
    {
        if ($type == 'write') {
            return $this->_getResourceConnection()->getConnection('core_write');
        } else {
            return $this->_getResourceConnection()->getConnection('core_read');
        }
    }

    /**
     * Get the resource model
     *
     * @return Mage_Core_Model_Abstract
     */
    private function _getResourceConnection()
    {
        return Mage::getSingleton('core/resource');
    }
}