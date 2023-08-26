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
 * @package     Blugento_Campaign
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Campaign_Adminhtml_CampaignController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Campaign'))->_title($this->__('Landing Page'));
        $this->loadLayout();
        $this->_setActiveMenu('blugento_adminmenu/blugento_campaign');
        $this->_addContent($this->getLayout()->createBlock('blugento_campaign/adminhtml_campaign'));
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('blugento_campaign/adminhtml_campaign_grid')->toHtml()
        );
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');

        /** @var Blugento_Campaign_Model_Campaign $model */
        $model = Mage::getModel('blugento_campaign/campaign')->load($id);

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('campaign_data', $model);

        $this->loadLayout();
        $this->_setActiveMenu('blugento_adminmenu/blugento_campaign');
        $this->_title($this->__('Campaign'))->_title($this->__('Landing Page'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this
            ->_addContent($this->getLayout()->createBlock('blugento_campaign/adminhtml_campaign_edit'))
        ;

        $this->renderLayout();
    }

    public function saveAction()
    {
        if ($this->getRequest()->getPost()) {
            /** @var Blugento_Campaign_Model_Campaign $model */
            $model = Mage::getModel('blugento_campaign/campaign');

            /** @var Blugento_Campaign_Helper_Data $helper */
            $helper = Mage::helper('blugento_campaign');

            try {
                $id = $this->getRequest()->getParam('id');
                $model = $model->load($id);

                $params = $this->getRequest()->getParams();
                $params['code'] = $helper->processCode($params['code'], $params['name']);

                if ($model->alreadyExist($params['code'])) {
                    Mage::getSingleton('adminhtml/session')
                        ->addError($this->__('A campaign with %s code already exist.', '<i>' . $params["code"] . '</i>'));
                    $this->_redirectReferer();
                    return;
                }

                if ($params['status'] == 1 && $model->isAnotherCampaignActive()) {
                    $params['status'] = 0;
                    Mage::getSingleton('adminhtml/session')
                        ->addNotice($this->__('The campaign was successfuly saved but is disabled because you cannot have two active campaigns at the same time!'));
                }

                $model->setData($params);
                $model->save();

                $model->setShortcode($helper->createShortcode($model->getId(), $model->getLayout()));
                $model->save();

                $this->_setPageLayout($params['cms_page'], $params['layout']);

                Mage::getSingleton('adminhtml/session')
                    ->addSuccess($this->__('The campaign was successfuly saved!'));

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

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            try {
                /** @var Blugento_Campaign_Model_Campaign $model */
                $model = Mage::getModel('blugento_campaign/campaign')->load($id);

                $model->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('The campaign was successfully deleted!')
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
     * Set CMS Page layout
     *
     * @param string $identifier
     * @param string $layout
     */
    private function _setPageLayout($identifier, $layout)
    {
        try {
            $cmsPage = Mage::getModel('cms/page')->load($identifier, 'identifier');

            $cmsPage->setRootTemplate($layout);
            $cmsPage->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }
}