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
 * @package     Blugento_FormsGenerator
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_FormsGenerator_Adminhtml_FormsController extends Mage_Adminhtml_Controller_Action
{
	public function _isAllowed()
	{
		return true;
	}
	
	public function indexAction(){

        $this->_title($this->__('Forms'))->_title($this->__('Generator'));
        $this->loadLayout();
        $this->_setActiveMenu('cms/formsgenerator');
        $this->_addContent($this->getLayout()->createBlock('blugento_formsgenerator/adminhtml_form'));
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('blugento_formsgenerator/adminhtml_form_grid')->toHtml()
        );
    }

    public function newAction(){
        $this->_forward('edit');
    }

    public function editAction(){
        $id = $this->getRequest()->getParam('id');

        /** @var Blugento_FormsGenerator_Model_Forms $model */
        $model = Mage::getModel('blugento_formsgenerator/forms')->load($id);

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('formsgenerator_data', $model);

        $this->loadLayout();
        $this->_setActiveMenu('cms/formsgenerator');
        $this->_title($this->__('Add new form'))->_title($this->__('Forms'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this
            ->_addContent($this->getLayout()->createBlock('blugento_formsgenerator/adminhtml_form_edit'))
            ->_addContent($this->getLayout()->createBlock('blugento_formsgenerator/adminhtml_form_previewform'))
            ->_addLeft($this->getLayout()->createBlock('blugento_formsgenerator/adminhtml_form_edit_tabs'))
        ;
        $this->renderLayout();
    }

    public function saveAction(){
        if($this->getRequest()->getPost()){

            /** @var Blugento_FormsGenerator_Helper_Data $helper */
            $helper = Mage::helper('blugento_formsgenerator');

            $id = $this->getRequest()->getParam('id');

            /** @var Blugento_FormsGenerator_Model_Forms $model */
            $model = Mage::getModel('blugento_formsgenerator/forms')->load($id);

            try {
                $fieldsCode = $this->getRequest()->getPost('fields_code');
                $storeId = $this->getRequest()->getPost('store_id');

                $model->setId($id);
                $model->setStoreId($storeId);
                $model->setFormName($this->getRequest()->getPost('form_name'));
                $model->setFieldsCode($fieldsCode);
                $model->setFormStatus($this->getRequest()->getPost('form_status'));
                $model->setRecipient($this->getRequest()->getPost('recipient'));
                $model->setRecipientEmail($this->getRequest()->getPost('recipient_email'));
                $model->setEmailTemplateId($this->getRequest()->getPost('email_template'));
                $model->setSuccessPage($this->getRequest()->getPost('success_page'));
                $model->save();

                $formCode = $helper->getAdditionalInput($fieldsCode, $model->getId(), $storeId);
                $model->setFormCode($formCode);

                $shortCode = $helper->createShortcode($model->getId());
                $model->setShortcode($shortCode);

                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('The form has been successfully saved.')
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

    public function massDeleteAction()
    {
        $formIds = $this->getRequest()->getParam('forms_generator');
        if (!is_array($formIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select form(s)'));
        } else {
            try {
                foreach ($formIds as $formId) {
                    /** @var Blugento_FormsGenerator_Model_Forms $model */
                    $model = Mage::getModel('blugento_formsgenerator/forms')->load($formId);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($formIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    public function deleteAction(){
        $formId = $this->getRequest()->getParam('id');
        if($formId){
            try {
                /** @var Blugento_FormsGenerator_Model_Forms $model */
                $model = Mage::getModel('blugento_formsgenerator/forms')->load($formId);
                $model->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Form was successfully deleted')
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    public function previewAction(){
        $this->loadLayout('formPreview');
        $this->renderLayout();
    }
}