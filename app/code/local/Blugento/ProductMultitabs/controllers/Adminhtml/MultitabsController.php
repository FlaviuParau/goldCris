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
 * @package     Blugento_ProductMultitabs
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_ProductMultitabs_Adminhtml_MultitabsController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Product'))->_title($this->__('Multitabs'));
        $this->loadLayout();
        $this->_setActiveMenu('blugento_adminmenu/blugento_productmultitabs');
        $this->_addContent($this->getLayout()->createBlock('blugento_productmultitabs/adminhtml_multitabs'));
        $this->renderLayout();
    }

    /**
     * Create new tab
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * View/Edit custom tabs
     *
     * @throws Mage_Core_Exception
     */
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');

        /** @var Blugento_ProductMultitabs_Model_Multitabs $model */
        $model = Mage::getModel('blugento_productmultitabs/multitabs')->load($id);

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('productmultitabs_data', $model);

        $this->loadLayout();
        $this->_setActiveMenu('blugento_adminmenu/blugento_productmultitabs');
        $this->_title($this->__('Product'))->_title($this->__('Multitabs'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this
            ->_addContent($this->getLayout()->createBlock('blugento_productmultitabs/adminhtml_multitabs_edit'))
        ;

        $this->_addLeft($this->getLayout()->createBlock('blugento_productmultitabs/adminhtml_multitabs_edit_tabs'));

        $this->renderLayout();
    }

    /**
     * View/Edit default tabs
     *
     * @throws Mage_Core_Exception
     */
    public function viewAction()
    {
        $id = $this->getRequest()->getParam('id');

        /** @var Blugento_ProductMultitabs_Model_Multitabs $model */
        $model = Mage::getModel('blugento_productmultitabs/multitabs')->load($id);

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('productmultitabs_data', $model);

        $this->loadLayout();
        $this->_setActiveMenu('blugento_adminmenu/blugento_productmultitabs');
        $this->_title($this->__('Product'))->_title($this->__('Multitabs'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this
            ->_addContent($this->getLayout()->createBlock('blugento_productmultitabs/adminhtml_multitabs_view'))
        ;

        $this->_addLeft($this->getLayout()->createBlock('blugento_productmultitabs/adminhtml_multitabs_view_tabs'));

        $this->renderLayout();
    }

    /**
     * Save tab
     */
    public function saveAction() {
        if ($this->getRequest()->getPost()) {
            /** @var Blugento_ProductMultitabs_Model_Multitabs $model */
            $model = Mage::getModel('blugento_productmultitabs/multitabs');

            /** @var Blugento_ProductMultitabs_Helper_Data $helper */
            $helper = Mage::helper('blugento_productmultitabs');

            try {
                $id = $this->getRequest()->getParam('id');
                $status = $this->getRequest()->getParam('status');
                $sortOrder = $this->getRequest()->getParam('sort_order');
                $stores = $this->_establishStore($this->getRequest()->getParam('stores'));

                $tab = $model->load($id);
                $tab->setStatus($status);
                $tab->setSortOrder(trim($sortOrder));
                $tab->setStores($stores);

                if ($tab->getType() != 'default') {
                    $name = trim($this->getRequest()->getParam('name'));

                    if ($model->nameExist($name)) {
                        Mage::getSingleton('adminhtml/session')->addError(
                            $helper->__('The name already exist!')
                        );
                        $this->_redirectReferer();
                        return;
                    }

                    $content = $this->getRequest()->getParam('content');
                    $active = $this->getRequest()->getParam('active_on_products');
                    $identifier = str_replace(' ', '_', strtolower($name));

                    if ($content == 1) {
                        $contentBlock = $this->getRequest()->getParam('content_block');
                        $tab->setContentBlock($contentBlock);
                        $tab->setContentAttribute(null);
                    } else if ($content == 2) {
                        $contentAttr = $this->getRequest()->getParam('content_attribute');
                        $tab->setContentAttribute($contentAttr);
                        $tab->setContentBlock(null);
                    }

                    $tab->setName($name);
                    $tab->setIdentifier($identifier);
                    $tab->setContent($content);
                    $tab->setActiveOnProducts($active);
                    $tab->setType('custom');

                    if ($active == 2) {
                        $codes = trim($this->getRequest()->getParam('products_codes'));
                        if (!$codes) {
                            Mage::getSingleton('adminhtml/session')->addError(
                                $helper->__('You have to complete the products codes/skus!' )
                            );
                            $this->_redirectReferer();
                            return;
                        }
                        $tab->setProductsCodes($codes);
                    }
                }
                $tab->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $helper->__('The tab was successfully saved!')
                );

                if ($method = $this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/' . $method, array('id' => $model->getId()));
                    return;
                }

                $this->_redirect('*/*');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_forward('new');
            }
        }
    }

    /**
     * Delete tab
     */
    public function deleteAction() {
        $id = $this->getRequest()->getParam('id');
        if ($id){
            try {
                /** @var Blugento_ProductMultitabs_Model_Multitabs $model */
                $model = Mage::getModel('blugento_productmultitabs/multitabs')->load($id);
                $model->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('The tab was successfully deleted!')
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Process store id
     *
     * @param string $storeId
     * @return string
     */
    private function _establishStore($storeId)
    {
        if (in_array('0', $storeId)){
            $storeId = '0';
        } else {
            $storeId = implode(",", $storeId);
        }

        if($storeId == "") {
            $storeId = '0';
        }

        return $storeId;
    }

    protected  function _isAllowed()
    {
        return true;
    }
}