<?php

require_once(Mage::getModuleDir('controllers','Mage_Newsletter') . DS . 'ManageController.php');

class Blugento_Newsletter_ManageController extends Mage_Newsletter_ManageController
{
	/**
	 * Manage current customer newsletter subscriptions
	 *
	 * @return Mage_Core_Controller_Varien_Action
	 */
	public function saveAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('customer/account/');
        }
        
        try {
        	$isSubscribed = (int) $this->getRequest()->getParam('is_subscribed', false);
			$customerEmail = $this->_getCustomer()->getEmail();

	        if ($isSubscribed == 1) {
		        Mage::getModel('newsletter/subscriber')->subscribe($customerEmail);
	        } else {
	        	$this->unsubscribe($customerEmail);
	        }

            if ((boolean) $this->getRequest()->getParam('is_subscribed', false)) {
                Mage::getSingleton('customer/session')->addSuccess($this->__('The subscription has been saved.'));
            } else {
                Mage::getSingleton('customer/session')->addSuccess($this->__('The subscription has been removed.'));
            }
        } catch (Exception $e) {
            Mage::getSingleton('customer/session')->addError($this->__('An error occurred while saving your subscription.'));
        }
        
        $this->_redirect('customer/account/');
    }

	/**
     * Unsubscribes loaded subscription
     *
     */
    public function unsubscribe($email)
    {
		$model = Mage::getModel('newsletter/subscriber');
        $model->loadByEmail($email);

        if ($model->hasCheckCode() && $model->getCode() != $model->getCheckCode()) {
            Mage::throwException(Mage::helper('newsletter')->__('Invalid subscription confirmation code.'));
        }

        $model->setSubscriberStatus(3)->save();
		$model->sendUnsubscriptionEmail();

        return $this;
    }
	
	/**
	 * Get current customer data
	 *
	 * @return Mage_Customer_Model_Customer
	 */
	protected function _getCustomer()
	{
		return Mage::getSingleton('customer/session')->getCustomer();
	}
}
