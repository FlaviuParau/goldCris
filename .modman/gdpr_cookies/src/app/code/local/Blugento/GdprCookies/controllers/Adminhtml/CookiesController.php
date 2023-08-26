<?php

class Blugento_GdprCookies_Adminhtml_CookiesController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $this->_title($this->__('Manage'))->_title($this->__('Cookies List'));
        $this->loadLayout();
        //$this->_setActiveMenu('sales/sales');
        $this->_addContent($this->getLayout()->createBlock('gdprcookies/adminhtml_cookies_list'));
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('gdprcookies/adminhtml_cookies_list_grid')->toHtml()
        );
    }

    protected function _isAllowed()
    {
        return true;
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $cookieId = $this->getRequest()->getParam('id');
        $cookiesModel = Mage::getModel('gdprcookies/list')->load($cookieId);

        if ($cookiesModel->getId() || $cookieId == 0)
        {
            Mage::register('cookies_list_data', $cookiesModel);
            $this->loadLayout();
            $this->_setActiveMenu('gdprcookies/set_time');
            $this->getLayout()->getBlock('head')
                ->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()
                ->createBlock('gdprcookies/adminhtml_cookies_list_edit'))
                ->_addLeft($this->getLayout()
                    ->createBlock('gdprcookies/adminhtml_cookies_list_edit_tabs')
                );
            $this->renderLayout();
        }
        else
        {
            Mage::getSingleton('adminhtml/session')->addError('Cookies does not exist');
            $this->_redirect('*/*/');
        }
    }

    public function saveAction()
    {
        if ($this->getRequest()->getPost()) {
            try {
                $postData = $this->getRequest()->getPost();
                //var_dump($postData);die();
                $cookiesModel = Mage::getModel('gdprcookies/list');

                //$cookiesModel->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
                $cookiesModel
                    ->addData($postData)
                    ->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate())
                    ->setId($this->getRequest()->getParam('id'))
                    ->save();

                Mage::getSingleton('adminhtml/session')->addSuccess('successfully saved');
                Mage::getSingleton('adminhtml/session')->setCookiesListData(false);
                $this->_redirect('*/*/');
                return;


            } catch (Exception $e) {

                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setCookiesListData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }

            $this->_redirect('*/*/');
        }
    }

    public function deleteAction()
    {
        if($this->getRequest()->getParam('id') > 0)
        {
            try
            {
                $cookiesModel = Mage::getModel('gdprcookies/list');
                $cookiesModel->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess('successfully deleted');
                $this->_redirect('*/*/');
            }
            catch (Exception $e)
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
}