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

class Blugento_GdprUserData_Adminhtml_GdpruserdataController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction() {

        $this->_title($this->__('GDPR'))->_title($this->__('Log'));
        $this->loadLayout();
        $this->_setActiveMenu('customer/gdpruserdata');
        $this->_addContent($this->getLayout()->createBlock('blugento_gdpruserdata/adminhtml_log'));
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('blugento_gdpruserdata/adminhtml_log_grid')->toHtml()
        );
    }

    public function editDeleteRequestAction(){
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('blugento_gdpruserdata/request')->load($id);

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('gdpruserdata_data', $model);

        $this->loadLayout();
        $this->_setActiveMenu('customer/gdpruserdata');
        $this->_title($this->__('Request Details'))->_title($this->__('GDPR Request'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this
            ->_addContent($this->getLayout()->createBlock('blugento_gdpruserdata/adminhtml_log_confirmdelete'))
        ;
        $this->renderLayout();
    }

    public function viewExportRequestAction(){
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('blugento_gdpruserdata/request')->load($id);

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('gdpruserdata_data', $model);

        $this->loadLayout();
        $this->_setActiveMenu('customer/gdpruserdata');
        $this->_title($this->__('Request Details'))->_title($this->__('GDPR Request'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this
            ->_addContent($this->getLayout()->createBlock('blugento_gdpruserdata/adminhtml_log_details'))
        ;
        $this->renderLayout();
    }

    public function saveAction()
    {
        if($this->getRequest()->getPost()){
            $id = $this->getRequest()->getParam('id');
            $model = Mage::getModel('blugento_gdpruserdata/request')->load($id);

            $confirmation = $this->getRequest()->getPost('admin_confirmation');
            $status = $model->getStatus();

            try {
                if ($status == 'pending') {
                    if ($confirmation == 'rejected') {
                        $message = trim($this->getRequest()->getPost('reject_delete_message'));

                        if ($message != ''){
                            $email = $model->getCustomerEmail();

                            $model->setRejectDeleteMessage($message);
                            $model->setStatus('processed');
                            $model->setAdminConfirmation($confirmation);
                            $model->save();

                            $model->sendRejectionMessageEmail($email, $message, $model->getId());

                            Mage::getSingleton('adminhtml/session')->addSuccess(
                                Mage::helper('adminhtml')->__(Mage::helper('blugento_gdpruserdata')->__('The request was updated and an email will be sent to customer!'))
                            );
                        } else {
                            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blugento_gdpruserdata')->__('If you reject the deletion you must wrote a message to the customer!'));
                        }
                    } elseif ($confirmation == 'approved') {
                        $model->setStatus('processed');
                        $model->setAdminConfirmation($confirmation);
                        $model->save();

                        Mage::getSingleton('adminhtml/session')->addSuccess(
                            Mage::helper('adminhtml')->__(Mage::helper('blugento_gdpruserdata')->__('The request was successfuly updated!'))
                        );
                    } else {
                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blugento_gdpruserdata')->__('You have to approve/reject the deletion request!'));
                    }
                } else {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blugento_gdpruserdata')->__('You cannot modify a request with status: ') . $status);
                }
                $this->_redirect('*/*');
            }catch(Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_forward('new');
            }
        }
    }

    protected function _isAllowed()
    {
        return true;
    }
}