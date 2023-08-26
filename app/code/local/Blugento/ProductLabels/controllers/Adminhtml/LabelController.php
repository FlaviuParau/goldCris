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
 * @package     Blugento_ProductLabels
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_ProductLabels_Adminhtml_LabelController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Product'))->_title($this->__('Labels'));
        $this->loadLayout();
        $this->_setActiveMenu('blugento_adminmenu/blugento_productlabels');
        $this->_addContent($this->getLayout()->createBlock('blugento_productlabels/adminhtml_label'));
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('blugento_productlabels/adminhtml_label_grid')->toHtml()
        );
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');

        /** @var Blugento_ProductLabels_Model_Label $model */
        $model = Mage::getModel('blugento_productlabels/label')->load($id);

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('productlabels_data', $model);

        $this->loadLayout();
        $this->_setActiveMenu('blugento_adminmenu/blugento_productlabels');
        $this->_title($this->__('Product'))->_title($this->__('Labels'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this
            ->_addContent($this->getLayout()->createBlock('blugento_productlabels/adminhtml_label_edit'))
        ;

        $this->_addLeft($this->getLayout()->createBlock('blugento_productlabels/adminhtml_label_edit_tabs'));

        $this->renderLayout();
    }

    public function saveAction() {
        if ($this->getRequest()->getPost()) {
            /** @var Blugento_ProductLabels_Model_Label $model */
            $model = Mage::getModel('blugento_productlabels/label');

            /** @var Blugento_ProductLabels_Helper_Data $model */
            $helper = Mage::helper('blugento_productlabels');

            try {
                $id = $this->getRequest()->getParam('id');
                $categories = $this->getRequest()->getParam('categories');
                $stores = $this->_getStoreInfo($this->getRequest()->getParam('stores'));
                $storesArr = explode(',', $stores);
                $model = $model->load($id);

                $name = trim($this->getRequest()->getParam('name'));
                if (!$model->isUnique($name, $storesArr)) {
                    Mage::getSingleton('adminhtml/session')->addError(
                        $helper->__('A label with this name already exist! You must choose another name.' )
                    );
                    $this->_redirectReferer();
                    return;
                }

                $type = $this->getRequest()->getParam('type');
                if (!$type) {
                    $type = $model->getType();
                }

                if ($type == 'promo' || $type == 'new') {
                    if ($this->getRequest()->getParam('status') == 1 && !$model->canActivate($type, $storesArr)) {
                        Mage::getSingleton('adminhtml/session')->addError(
                            $helper->__('There is another label enabled of type ' ) . '"' . $type . '". ' .
                            $helper->__('Please disable the other label before activate this one.')
                        );
                        $this->_redirectReferer();
                        return;
                    }
                    $categories = '';
                }

                if (isset($_FILES['label_image']) && $_FILES['label_image']['size'] > 0) {
                    /** @var Blugento_ProductLabels_Model_Image $image */
                    $image = Mage::getModel('blugento_productlabels/image');

                    $validateImage = $image->validateImage($_FILES['label_image']);
                    if (!$validateImage['error']) {
                        $path = $image->uploadImage($_FILES['label_image'], 'label_image', $name);

                        if ($path) {
                            if ($model->getPath()) {
                                $image->deleteOldImage($model->getPath());
                            }

                            $model->setPath($path);
                        } else {
                            Mage::getSingleton('adminhtml/session')->addError(
                                $helper->__('The image file is invalid! Please upload another image.')
                            );
                            $this->_redirectReferer();
                            return;
                        }
                    } else {
                        Mage::getSingleton('adminhtml/session')->addError($validateImage['message']);
                        $this->_redirectReferer();
                        return;
                    }
                }

                $createdType = $model->getCreatedType();
                if (!$createdType) {
                    $createdType = 'custom';
                }

                $model->setName($name);
                $model->setType($type);
                $model->setCreatedType($createdType);
                $model->setCategories($categories);
                $model->setStatus($this->getRequest()->getParam('status'));
                $model->setEnabledOnProduct($this->getRequest()->getParam('enabled_on_product'));
                $model->setPositionOnProduct($this->getRequest()->getParam('position_on_product'));
                $model->setEnabledOnCategory($this->getRequest()->getParam('enabled_on_category'));
                $model->setPositionOnCategory($this->getRequest()->getParam('position_on_category'));
                $model->setStores($stores);
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $helper->__('The label was successfully saved!')
                );

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }

                $this->_redirect('*/*');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_forward('new');
            }
        }
    }

    public function deleteAction() {
        $id = $this->getRequest()->getParam('id');
        if($id){
            try {
                /** @var Blugento_ProductLabels_Model_Label $model */
                $model = Mage::getModel('blugento_productlabels/label')->load($id);

                /** @var Blugento_ProductLabels_Model_Image $image */
                $image = Mage::getModel('blugento_productlabels/image');
                $image->removeImageDirectory($model->getName());

                $model->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Label was successfully deleted!')
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    protected function _isAllowed()
    {
        return true;
    }

    /**
     * Process store id
     *
     * @param array $storeId
     * @return string
     */
    private function _getStoreInfo($storeId)
    {
        if (in_array('0', $storeId)){
            $storeId = '0';
        } else {
            $storeId = implode(',', $storeId);
        }

        if ($storeId == '') {
            $storeId = '0';
        }

        return $storeId;
    }
}