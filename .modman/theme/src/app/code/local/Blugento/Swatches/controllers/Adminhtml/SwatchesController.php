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
 * @package     Blugento_Swatches
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Swatches_Adminhtml_SwatchesController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_title($this->__('Swatches'));
        $this->loadLayout();
        $this->_setActiveMenu('blugento_adminmenu/blugento_swatches');
        $this->_addContent($this->getLayout()->createBlock('blugento_swatches/adminhtml_swatches'));
        $this->renderLayout();
    }

    /**
     * Grid action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('blugento_swatches/adminhtml_swatches_grid')->toHtml()
        );
    }

    /**
     * Edit item
     *
     * @throws Mage_Core_Exception
     */
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');

        /** @var Blugento_Swatches_Model_Swatches $model */
        $model = Mage::getModel('blugento_swatches/swatches')->load($id);

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('blugentoswatches_data', $model);

        $this->loadLayout();
        $this->_setActiveMenu('blugento_adminmenu/blugento_swatches');
        $this->_title($this->__('Swatches'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this
            ->_addContent($this->getLayout()->createBlock('blugento_swatches/adminhtml_swatches_edit'))
        ;

        $this->_addLeft($this->getLayout()->createBlock('blugento_swatches/adminhtml_swatches_edit_tabs'));

        $this->renderLayout();
    }

    /**
     * Save item
     */
    public function saveAction()
    {
        if ($this->getRequest()->getPost()) {

            /** @var Blugento_Swatches_Model_Swatches $model */
            $model = Mage::getModel('blugento_swatches/swatches');

            try {
                $id = $this->getRequest()->getParam('id');
                $mode = $this->getRequest()->getParam('mode');

                $swModel = $model->load($id);
                $swModel->setMode($mode);

                if ($mode == 1) {
                    $color = $this->getRequest()->getParam('color');

                    $swModel->setColor($color);
                } elseif ($mode == 2) {
                    /** @var Blugento_Swatches_Model_Image $model */
                    $image = Mage::getModel('blugento_swatches/image');

                    $imageData = $this->getRequest()->getParam('image_name');
                    
                    if ($imageData['delete']) {
                        $oldImage = $swModel->getImageName();
                        $image->removeImage($oldImage);
                        $swModel->setImageName(null);
                    }

                    if (isset($_FILES['image_name']) && $_FILES['image_name']['size'] > 0) {
                        $validImage = $image->validateImage($_FILES['image_name']);
                        if (!$validImage['error']) {
                            $imageName = $image->uploadImage($_FILES['image_name'], 'image_name');

                            if ($imageName) {
                                $swModel->setImageName($imageName);
                            } else {
                                Mage::getSingleton('adminhtml/session')->addError(
                                    $this->__('There was an error in upload process. Please try again later!')
                                );
                                $this->_redirectReferer();
                                return;
                            }

                        } else {
                            Mage::getSingleton('adminhtml/session')->addError($validImage['message']);
                            $this->_redirectReferer();
                            return;
                        }
                    }
                }
                $swModel->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('The item was successfully saved!')
                );

                if ($method = $this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/' . $method, array('id' => $model->getId()));
                    return;
                }

                $this->_redirect('*/*');
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }

    protected  function _isAllowed()
    {
        return true;
    }
}