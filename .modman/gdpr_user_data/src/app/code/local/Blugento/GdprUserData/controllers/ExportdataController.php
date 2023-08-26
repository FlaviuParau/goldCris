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

class Blugento_GdprUserData_ExportdataController extends Mage_Core_Controller_Front_Action
{
    public function indexAction() {

        if (Mage::helper('blugento_gdpruserdata')->isEnabled()) {
            $this->loadLayout();
            $this->renderLayout();
        } else {
            $this->_redirect('noroute');
        }
    }

    public function saveExportRequestAction() {

        if ($this->getRequest()->getPost()) {
            $helper = Mage::helper('blugento_gdpruserdata');
            $email = $this->getRequest()->getParam('email_contact');
            $requestCollection = Mage::getModel('blugento_gdpruserdata/request')
                ->getCollection()
                ->addFieldToFilter('customer_email', $email);

            $validation = true;
            foreach ($requestCollection as $item) {
                if ($item->getId() && $item->getType() == 'export') {
                    if (!$helper->isRequestExpired($item->getCreatedAt())) {
                        Mage::getSingleton('core/session')->addError($helper->__('You already sent a request in the last 24 hours!'));
                        $validation = false;
                        break;
                    }
                }
            }

            if ($validation) {
                Mage::getSingleton('core/session')->addSuccess($helper->__('If data exists, an email will be sent as soon as possible with more details!'));

                $secretKey = $helper->generateSecretKey($email);

                try {
                    $requestModel = Mage::getModel('blugento_gdpruserdata/request');
                    $requestModel->setCustomerEmail($email);
                    $requestModel->setType('export');
                    $requestModel->setSecretKey($secretKey);

                    if ($requestModel->customerHasData($email)) {
                        $requestModel->setStatus('pending');
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

    public function downloadDataAction() {
        $helper = Mage::helper('blugento_gdpruserdata');
        $key = Mage::app()->getRequest()->getParam('key');

        if ($key) {
            $request = Mage::getModel('blugento_gdpruserdata/request')->load($key, 'secret_key');

            if ($request->getType() == 'export' && $request->getStatus() == 'processed') {
                $filepath = $helper->getGdprPath($request->getArchiveName());

                if (file_exists($filepath)) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($filepath));
                    readfile($filepath);

                    unlink($filepath);

                    try {
                        $request->setStatus('completed');
                        $request->save();
                    } catch (Exception $e) {
                        Mage::log('The status of the request with id ' . $request->getId() . 'cannot be set completed!', null, 'GDPR_user_data.log');
                    }
                    exit;
                } else {
                    $validation = false;
                }
            } else {
                $validation = false;
            }
        } else {
            $validation = false;
        }

        if (!$validation) {
            Mage::getSingleton('core/session')->addError($helper->__('This link is expired! Please make another request!'));
            $this->_redirect('/');
        }
    }
}