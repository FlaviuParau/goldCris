<?php

include_once('Mage/Newsletter/controllers/SubscriberController.php');

class Blugento_Newsletterprotection_SubscriberController extends Mage_Newsletter_SubscriberController
{
    /**
     * New subscription action
     */
    public function newAction()
    {
        if (!Mage::getStoreConfig('newsletterprotection/newsletterprotection_group/newsletterprotection_active')) {
            if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
                $session            = Mage::getSingleton('core/session');
                $customerSession    = Mage::getSingleton('customer/session');
                $email              = (string) $this->getRequest()->getPost('email');

                try {
                    if (!Zend_Validate::is($email, 'EmailAddress')) {
                        Mage::throwException($this->__('Please enter a valid email address.'));
                    }

                    if (Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1 &&
                        !$customerSession->isLoggedIn()) {
                        Mage::throwException($this->__('Sorry, but administrator denied subscription for guests. Please <a href="%s">register</a>.', Mage::helper('customer')->getRegisterUrl()));
                    }

                    $ownerId = Mage::getModel('customer/customer')
                        ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                        ->loadByEmail($email)
                        ->getId();
                    if ($ownerId !== null && $ownerId != $customerSession->getId()) {
                        $session->addSuccess($this->__('Thank you for your subscription.'));
                        $this->_redirectReferer();
                        return;
                    }

                    $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
                    if ($subscriber->getId() && $subscriber->getStoreId() == Mage::app()->getStore()->getStoreId() && $subscriber->getSubscriberStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED) {
                        Mage::throwException($this->__('The user is already subscribed to newsletter.'));
                    }

                    $status = Mage::getModel('newsletter/subscriber')->subscribe($email);
                    if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
                        $session->addSuccess($this->__('Confirmation request has been sent.'));
                    }
                    else {
                        $session->addSuccess($this->__('Thank you for your subscription.'));
                    }
                }
                catch (Mage_Core_Exception $e) {
                    $session->addException($e, $this->__('There was a problem with the subscription: %s', $e->getMessage()));
                }
                catch (Exception $e) {
                    $session->addException($e, $this->__('There was a problem with the subscription.'));
                }
            }
            $this->_redirectReferer();
        } else {
            if ($this->getRequest()->isPost() && filter_var($this->getRequest()->getPost('email'), FILTER_VALIDATE_EMAIL)) {
	            // Fetch submitted params
	            $params = $this->getRequest()->getParams();
                $storeId = Mage::app()->getStore()->getStoreId();
                $secret = Mage::getStoreConfig('magecomp/recaptcha_config/magecomp_recaptcha_validate', $storeId);
                $remoteip = $_SERVER["REMOTE_ADDR"];
                $url = "https://www.google.com/recaptcha/api/siteverify";
                $response = $_POST["g-recaptcha-response"];

                // Curl Request
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, array(
                    'secret' => $secret,
                    'response' => $response,
                    'remoteip' => $remoteip
                ));
                $curlData = curl_exec($curl);
                curl_close($curl);

                // Parse data
                $recaptcha = json_decode($curlData, true);
                $session = Mage::getSingleton('core/session');
                
                if ($recaptcha['success']) {

                    $customerSession = Mage::getSingleton('customer/session');
                    $email = (string)$this->getRequest()->getPost('email');

                    try {
                        if (!Zend_Validate::is($email, 'EmailAddress')) {
                            $session->addError($this->__('Please enter a valid email address.'));
                        }

                        if (Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1 &&
                            !$customerSession->isLoggedIn()) {
                            $session->addError($this->__('Sorry, but administrator denied subscription for guests. Please <a href="%s">register</a>.', Mage::helper('customer')->getRegisterUrl()));
                        }

                        // Check if email is already assigned
                        $ownerId = Mage::getModel('customer/customer')
                            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                            ->loadByEmail($email)
                            ->getId();

                        // Check if email already exists
                        $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
                        if ($subscriber->getId() && $subscriber->getStoreId() == Mage::app()->getStore()->getStoreId() && $subscriber->getSubscriberStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED) {
                            $session->addError($this->__('The user is already subscribed to newsletter.'));
                        } else {
                            // If all from above fails than will send confirmation email
                            $status = Mage::getModel('newsletter/subscriber')->subscribe($email);

                            if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
                                $session->addSuccess($this->__('Confirmation request has been sent.'));
                            } else {
                                $session->addSuccess($this->__('Thank you for your subscription.'));
                            }
                        }
                    } catch (Mage_Core_Model_Exception $e) {
                        $session->addError($e->getMessage());
                        $session->addException($e, $this->__('There was a problem with the subscription: %s', $e->getMessage()));
                    }
                } else {
	                $session = Mage::getSingleton('core/session');
                    $session->addError($this->__('There was a problem with the subscription.'));
                }

            } else {
                $session = Mage::getSingleton('core/session');
                $session->addError($this->__('Please enter a valid email address.'));
            }

            $this->_redirectReferer();
        }
    }

}