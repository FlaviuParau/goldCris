<?php

class Blugento_Contacts_Model_Observer extends Mage_Captcha_Model_Observer
{
	public function checkContactPage($observer)
	{
		$formId = 'contact_page_captcha';
		$captchaModel = Mage::helper('captcha')->getCaptcha($formId);
		if ($captchaModel->isRequired()) {
			$controller = $observer->getControllerAction();
			if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
				Mage::getSingleton('customer/session')->addError(Mage::helper('captcha')->__('Incorrect CAPTCHA.'));
				$controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
				$controller->getResponse()->setRedirect(Mage::getUrl('contacts'));
			}
		}
		return $this;
	}
	
	/**
	 * Get Captcha String
	 *
	 * @param Varien_Object $request
	 * @param string $formId
	 * @return string
	 */
	protected function _getCaptchaString($request, $formId)
	{
		$captchaParams = $request->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);
		return $captchaParams[$formId];
	}
}
