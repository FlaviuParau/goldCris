<?php
/**
 * Blugento Sliders
 * Controller Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Sliders
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @author Daniel Gheoltan <daniel.gheoltan@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Sliders_Adminhtml_Blugento_Sliders_GroupController extends Blugento_Sliders_Controller_Adminhtml_Abstract
{
    /**
     * Redirect to Sliders dashboard
     *
     * @return $this
     */
    public function indexAction()
    {
        return $this->_redirect('*/blugento_sliders');
    }

    /**
     * Forward to the edit action so the user can add a new group
     *
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Display the edit/add form
     *
     */
    public function editAction()
    {
        Mage::getSingleton('admin/session')->setActiveTabId('group');

        $this->loadLayout();

        if ($headBlock = $this->getLayout()->getBlock('head')) {
            if ($group = $this->_initGroupModel()) {
                $this->_title('Edit Group');
                $this->_title($group->getTitle());
            } else {
                $this->_title('Create a Group');
            }
        }

        $this->renderLayout();
    }

    /**
     * Save the group
     *
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('group')) {
            $group = Mage::getModel('blugento_sliders/group')
                ->setData($data)
                ->setId($this->getRequest()->getParam('id'));

            try {
                $group->save();
                $this->_getSession()->addSuccess($this->__('Banner Group was saved'));
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                Mage::logException($e);
            }

            if ($this->getRequest()->getParam('back') && $group->getId()) {
                $this->_redirect('*/*/edit', array('id' => $group->getId()));
                return;
            }
        } else {
            $this->_getSession()->addError($this->__('There was no data to save'));
        }

        $this->_redirect('*/blugento_sliders');
    }

    /**
     * Delete a group
     *
     */
    public function deleteAction()
    {
        if ($groupId = $this->getRequest()->getParam('id')) {
            $group = Mage::getModel('blugento_sliders/group')->load($groupId);

            if ($group->getId()) {
                try {
                    $group->delete();
                    $this->_getSession()->addSuccess($this->__('The banner group was deleted.'));
                }
                catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }

        $this->_redirect('*/blugento_sliders');
    }

    /**
     * Delete multiple groups in one go
     *
     */
    public function massDeleteAction()
    {
        $groupIds = $this->getRequest()->getParam('group');

        if (!is_array($groupIds)) {
            $this->_getSession()->addError($this->__('Please select some groups.'));
        } else {
            if (!empty($groupIds)) {
                try {
                    foreach ($groupIds as $groupId) {
                        $group = Mage::getSingleton('blugento_sliders/group')->load($groupId);

                        Mage::dispatchEvent('blugento_sliders_controller_group_delete', array('blugento-sliders-group' => $group));

                        $group->delete();
                    }

                    $this->_getSession()->addSuccess($this->__('Total of %d record(s) have been deleted.', count($groupIds)));
                }
                catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }

        $this->_redirect('*/blugento_sliders');
    }

    /**
     * Initialise the group model
     *
     * @return null|Blugento_Sliders_Model_Group
     */
    protected function _initGroupModel()
    {
        if ($groupId = $this->getRequest()->getParam('id')) {
            $group = Mage::getModel('blugento_sliders/group')->load($groupId);

            if ($group->getId()) {
                Mage::register('blugento-sliders-group', $group);
                Mage::getSingleton('admin/session')->setActiveTabId('group');
            }
        }

        return Mage::registry('blugento-sliders-group');
    }

    /**
     * Determine ACL permissions
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('blugento_adminmenu/blugento_sliders');
    }

    public function bannerAction()
    {
        $this->_initGroupModel();
        $this->getResponse()
            ->setBody($this->getLayout()->createBlock('blugento_sliders/adminhtml_group_edit_tab_banners')->toHtml());
    }
}
