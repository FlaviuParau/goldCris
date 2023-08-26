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
 * @package     Blugento_Popup
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Popup_Adminhtml_PopupController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction(){

        $this->_title($this->__('Popup'));
        $this->loadLayout();
        $this->_setActiveMenu('cms/popup');
        $this->_addContent($this->getLayout()->createBlock('blugento_popup/adminhtml_popup'));
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('blugento_popup/adminhtml_popup_grid')->toHtml()
        );
    }

    /**
     * New popup
     */
    public function newAction(){
        $this->_forward('edit');
    }

    /**
     * Edit popup
     */
    public function editAction(){
        $id = $this->getRequest()->getParam('id');

        /** @var Blugento_Popup_Model_Popup $model */
        $model = Mage::getModel('blugento_popup/popup')->load($id);

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('popup_data', $model);

        $this->loadLayout();
        $this->_setActiveMenu('cms/popup');
        $this->_title($this->__('Add new popup'))->_title($this->__('Popup'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this
            ->_addContent($this->getLayout()->createBlock('blugento_popup/adminhtml_popup_edit'))
            ->_addLeft($this->getLayout()->createBlock('blugento_popup/adminhtml_popup_edit_tabs'))
        ;
        $this->renderLayout();
    }

    /**
     * Save popup
     */
    public function saveAction() {
        if($this->getRequest()->getPost()){
            $data = $this->getRequest()->getParams();

            /** @var Blugento_Popup_Model_Popup $model */
            $model = Mage::getModel('blugento_popup/popup')->load($this->getRequest()->getParam('id'));
	        $stores = $this->getStoreInfo($this->getRequest()->getParam('stores'));

            try {
                $model->addData($data);
	            $model->setStores($stores);
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('blugento_popup')->__('The popup has been successfully saved.')
                );

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*');
            }catch(Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::logException($e);
                $this->_forward('new');
            }
        }
    }

    /**
     * Mass delete popup action
     */
    public function massDeleteAction()
    {
        $popupIds = $this->getRequest()->getParam('delete_popup');
        if (!is_array($popupIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blugento_popup')->__('Please select popup(s)'));
        } else {
            try {
                foreach ($popupIds as $popupId) {
                    /** @var Blugento_Popup_Model_Popup $model */
                    $model = Mage::getModel('blugento_popup/popup')->load($popupId);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('blugento_popup')->__(
                        'Total of %d record(s) were successfully deleted', count($popupIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Delete popup
     */
    public function deleteAction(){
        $popupId = $this->getRequest()->getParam('id');
        if($popupId){
            try {
                /** @var Blugento_Popup_Model_Popup $model */
                $model = Mage::getModel('blugento_popup/popup')->load($popupId);
                $model->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Popup was successfully deleted')
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed() {
        return true;
    }
	
	/**
	 * Process store id
	 *
	 * @param array $storeId
	 * @return string
	 */
	private function getStoreInfo($storeId)
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