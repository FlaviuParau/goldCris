<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer account controller
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */


require_once Mage::getModuleDir('controllers', 'Mage_Customer') . DS . 'AccountController.php';

class Blugento_Customer_AccountController extends Mage_Core_Controller_Front_Action
{
    public function forgotPasswordPostAction()
    {
        $email = (string) $this->getRequest()->getPost('email');
        if ($email) {
            /**
             * @var $flowPassword Mage_Customer_Model_Flowpassword
             */
            $flowPassword = Mage::getModel('customer/flowpassword');
            $flowPassword->setEmail($email)->save();

            if (!$flowPassword->checkCustomerForgotPasswordFlowEmail($email)) {
                Mage::getSingleton('core/session')
                    ->addError($this->__('You have exceeded requests to times per 24 hours from 1 e-mail.'));
                $this->_redirect('*/*/forgotpassword');
                return;
            }

            if (!$flowPassword->checkCustomerForgotPasswordFlowIp()) {
                Mage::getSingleton('core/session')->addError($this->__('You have exceeded requests to times per hour from 1 IP.'));
                $this->_redirect('*/*/forgotpassword');
                return;
            }

            if (!Zend_Validate::is($email, 'EmailAddress')) {
                Mage::getSingleton('core/session')->setForgottenEmail($email);
                Mage::getSingleton('core/session')->addError($this->__('Invalid email address.'));
                $this->_redirect('*/*/forgotpassword');
                return;
            }

            /** @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);

            $customerId = $customer->getId();
            if ($customerId) {
                try {
                    $newResetPasswordLinkToken =  Mage::helper('customer')->generateResetPasswordLinkToken();
//                    $newResetPasswordLinkCustomerId = Mage::helper('customer')
//                        ->generateResetPasswordLinkCustomerId($customerId);
//                    $customer->changeResetPasswordLinkCustomerId($newResetPasswordLinkCustomerId);
                    $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                    $customer->sendPasswordResetConfirmationEmail();
                } catch (Exception $exception) {
                    Mage::getSingleton('core/session')->addError($exception->getMessage());
                    $this->_redirect('*/*/forgotpassword');
                    return;
                }
            }
            Mage::getSingleton('core/session')
                ->addSuccess( Mage::helper('customer')
                    ->__('If there is an account associated with %s you will receive an email with a link to reset your password.',
                        Mage::helper('customer')->escapeHtml($email)));
            $this->_redirect('*/*/');
            return;
        } else {
            Mage::getSingleton('core/session')->addError($this->__('Please enter your email.'));
            $this->_redirect('*/*/forgotpassword');
            return;
        }
    }

    /**
     * Change customer password action
     */
    public function editPostAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/edit');
        }

        if ($this->getRequest()->isPost()) {
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getSingleton('customer/session')->getCustomer();

            $customer->setOldEmail($customer->getEmail());
            /** @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setFormCode('customer_account_edit')
                ->setEntity($customer);

            $customerData = $customerForm->extractData($this->getRequest());

            $errors = array();
            $customerErrors = $customerForm->validateData($customerData);
            if ($customerErrors !== true) {
                $errors = array_merge($customerErrors, $errors);
            } else {
                $customerForm->compactData($customerData);
                $errors = array();

                if (!$customer->validatePassword($this->getRequest()->getPost('current_password'))) {
                    $errors[] = $this->__('Invalid current password');
                }

                // If email change was requested then set flag
                $isChangeEmail = ($customer->getOldEmail() != $customer->getEmail()) ? true : false;
                $customer->setIsChangeEmail($isChangeEmail);

                // If password change was requested then add it to common validation scheme
                $customer->setIsChangePassword($this->getRequest()->getParam('change_password'));

                if ($customer->getIsChangePassword()) {
                    $newPass    = $this->getRequest()->getPost('password');
                    $confPass   = $this->getRequest()->getPost('confirmation');

                    if (strlen($newPass)) {
                        /**
                         * Set entered password and its confirmation - they
                         * will be validated later to match each other and be of right length
                         */
                        $customer->setPassword($newPass);
                        $customer->setPasswordConfirmation($confPass);
                    } else {
                        $errors[] = $this->__('New password field cannot be empty.');
                    }
                }

                // Validate account and compose list of errors if any
                $customerErrors = $customer->validate();
                if (is_array($customerErrors)) {
                    $errors = array_merge($errors, $customerErrors);
                }
            }

            if (!empty($errors)) {
                Mage::getSingleton('customer/session')->setCustomerFormData($this->getRequest()->getPost());
                foreach ($errors as $message) {
                    Mage::getSingleton('customer/session')->addError($message);
                }
                $this->_redirect('*/*/edit');
                return $this;
            }

            try {
                $customer->cleanPasswordsValidationData();
                $customer->setPasswordCreatedAt(time());

                // Reset all password reset tokens if all data was sufficient and correct on email change
                if ($customer->getIsChangeEmail()) {
                    $customer->setRpToken(null);
                    $customer->setRpTokenCreatedAt(null);
                }

                //sanitize firstname and lastname
                $customer->setFirstname(filter_var($customer->getFirstname(), FILTER_SANITIZE_STRING));
                $customer->setLastname(filter_var($customer->getLastname(), FILTER_SANITIZE_STRING));

                $customer->save();
                Mage::getSingleton('customer/session')->setCustomer($customer)
                    ->addSuccess($this->__('The account information has been saved.'));

                if ($customer->getIsChangeEmail() || $customer->getIsChangePassword()) {
                    $customer->sendChangedPasswordOrEmail();
                }

                $this->_redirect('customer/account');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer/session')->setCustomerFormData($this->getRequest()->getPost())
                    ->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('customer/session')->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save the customer.'));
            }
        }

        $this->_redirect('*/*/edit');
    }
}
