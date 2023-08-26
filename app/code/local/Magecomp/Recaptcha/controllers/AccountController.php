<?php

require_once Mage::getModuleDir('controllers', 'Mage_Customer') . DS . 'AccountController.php';

class Magecomp_Recaptcha_AccountController extends Mage_Customer_AccountController
{
	/**
	 * Login post action
	 */
	public function loginPostAction()
	{
		if (!Mage::helper('recaptcha')->isEnabled() || !Mage::helper('recaptcha')->showOnLogin()) {
			return parent::loginPostAction();
		}
		
		if (!$this->_validateFormKey()) {
			$this->_redirect('*/*/');
			return;
		}
		
		if ($this->_getSession()->isLoggedIn()) {
			$this->_redirect('*/*/');
			return;
		}
		$session = $this->_getSession();
		
		if ($this->getRequest()->isPost()) {
			$login = $this->getRequest()->getPost('login');
			if (!empty($login['username']) && !empty($login['password'])) {
				try {
					if ($this->_validateRecaptcha()) {
						$session->login($login['username'], $login['password']);
						if ($session->getCustomer()->getIsJustConfirmed()) {
							$this->_welcomeCustomer($session->getCustomer(), true);
						}
					}
				} catch (Mage_Core_Exception $e) {
					switch ($e->getCode()) {
						case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
							$value = $this->_getHelper('customer')->getEmailConfirmationUrl($login['username']);
							$message = $this->_getHelper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
							break;
						case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
							$message = $e->getMessage();
							break;
						default:
							$message = $e->getMessage();
					}
					$session->addError($message);
					$session->setUsername($login['username']);
				} catch (Exception $e) {
					// Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
				}
			} else {
				$session->addError($this->__('Login and password are required.'));
			}
		}
		
		$this->_loginPostRedirect();
	}
	
	/**
	 * Create customer account action
	 */
	public function createPostAction()
	{
		if (!Mage::helper('recaptcha')->isEnabled() || !Mage::helper('recaptcha')->showOnRegister()) {
			return parent::createPostAction();
		}
		
		$errUrl = $this->_getUrl('*/*/create', array('_secure' => true));
		
		if (!$this->_validateFormKey()) {
			$this->_redirectError($errUrl);
			return;
		}
		
		/** @var $session Mage_Customer_Model_Session */
		$session = $this->_getSession();
		if ($session->isLoggedIn()) {
			$this->_redirect('*/*/');
			return;
		}
		
		if (!$this->getRequest()->isPost()) {
			$this->_redirectError($errUrl);
			return;
		}
		
		$customer = $this->_getCustomer();
		
		try {
			$errors = $this->_getCustomerErrors($customer);
			
			if (empty($errors) && $this->_validateRecaptcha()) {
				$customer->cleanPasswordsValidationData();
				$customer->save();
				$this->_dispatchRegisterSuccess($customer);
				$this->_successProcessRegistration($customer);
				return;
			} else {
				$this->_addSessionError($errors);
				$session->addError($this->__('Cannot save the customer.'));
			}
		} catch (Mage_Core_Exception $e) {
			$session->setCustomerFormData($this->getRequest()->getPost());
			if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
				$url = $this->_getUrl('customer/account/forgotpassword');
				$message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
			} else {
				$message = $this->_escapeHtml($e->getMessage());
			}
			$session->addError($message);
		} catch (Exception $e) {
			$session->setCustomerFormData($this->getRequest()->getPost());
			$session->addException($e, $this->__('Cannot save the customer.'));
		}
		
		$this->_redirectError($errUrl);
	}
	
	protected function _validateRecaptcha()
	{
		$secret    =  Mage::getStoreConfig('magecomp/recaptcha_config/magecomp_recaptcha_validate');
		$recaptcha = '';
		
		if ($secret != '') {
			$remoteIp = $_SERVER['REMOTE_ADDR'];
			$url      = 'https://www.google.com/recaptcha/api/siteverify';
			$response = $this->getRequest()->getParam('g-recaptcha-response');
			
			// Curl Request
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, array(
				'secret' => $secret,
				'response' => $response,
				'remoteip' => $remoteIp
			));
			$curlData = curl_exec($curl);
			curl_close($curl);
			$recaptcha = json_decode($curlData, true);
		}
		
		return is_array($recaptcha) ? $recaptcha['success'] : false;
	}
}
