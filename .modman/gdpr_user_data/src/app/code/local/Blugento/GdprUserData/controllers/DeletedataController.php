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
 * @package     Blugento_GdprUserData
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_GdprUserData_DeletedataController extends Mage_Core_Controller_Front_Action
{
    public function indexAction() {

        if (Mage::helper('blugento_gdpruserdata')->isEnabled()) {
            $this->loadLayout();
            $this->renderLayout();
        } else {
            $this->_redirect('noroute');
        }
    }

    public function saveDeleteRequestAction() {
        if ($this->getRequest()->getPost()) {
            $helper = Mage::helper('blugento_gdpruserdata');
            $email = $this->getRequest()->getParam('email_contact');

            $requestCollection = Mage::getModel('blugento_gdpruserdata/request')
                ->getCollection()
                ->addFieldToFilter('customer_email', $email);

            $validation = true;
            $invalidStatus = ['processed', 'pending'];
            $expiredStatus = ['no data available', 'completed', 'account exists rejection'];
            foreach ($requestCollection as $item) {
                if ($item->getId() && $item->getType() == 'delete') {
                    if (in_array($item->getStatus(), $invalidStatus)) {
                        Mage::getSingleton('core/session')->addError($helper->__('The request was already sent and is being processed!'));
                        $validation = false;
                        break;
                    }

                    if (in_array($item->getStatus(), $expiredStatus) && !$helper->isRequestExpired($item->getCreatedAt())) {
                        Mage::getSingleton('core/session')->addError($helper->__('You already sent a request in the last 24 hours!'));
                        $validation = false;
                        break;
                    }
                }
            }
            if ($validation) {
                Mage::getSingleton('core/session')->addSuccess($helper->__('If data exists, an email will be sent as soon as possible with more details!'));

                try {
                    $requestModel = Mage::getModel('blugento_gdpruserdata/request');
                    $requestModel->setCustomerEmail($email);
                    $requestModel->setType('delete');

                    if ($requestModel->customerHasData($email)) {
                        $websiteId = Mage::app()->getStore()->getWebsiteId();
                        if (!isset($websiteId) || (isset($websiteId) && !$websiteId)) {
                            $websiteId = 1;
                        }

                        if ($requestModel->existAccount($email, $websiteId)) {
                            $requestModel->sendAccountRejectionEmail();
                            $requestModel->setStatus('account exists rejection');
                        } else {
                            $requestModel->setStatus('pending');
                            $requestModel->setAdminConfirmation('pending');
                        }
                    } else {
                        $requestModel->setStatus('no data available');
                    }

                    $requestModel->save();
                } catch (Exception $e) {
                    Mage::getSingleton('core/session')->addError($e->getMessage());
                }
            }
            $this->_redirect('*/*/');
        }
    }
}