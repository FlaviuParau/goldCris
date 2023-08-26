<?php
/**
 * Blugento Admin Menu
 * Controller Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Adminmenu
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Adminmenu_Adminhtml_BlugentoController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return true;
    }

    /**
     * Index Action
     */
    public function configurationAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('blugento_adminmenu/adminhtml_blugento/configuration');
        $this->_title(Mage::helper('adminhtml')->__('Configuration'));

        $this->renderLayout();
    }

    /**
     * CMS Blocks Action
     */
    public function cmsblocksAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('blugento_adminmenu/adminhtml_blugento/configuration');
        $this->_title(Mage::helper('adminhtml')->__('Configuration'));

        $this->renderLayout();
    }

    /**
     * CMS Pages Action
     */
    public function cmspagesAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('blugento_adminmenu/adminhtml_blugento/configuration');
        $this->_title(Mage::helper('adminhtml')->__('Configuration'));

        $this->renderLayout();
    }

    /**
     * Redirect to CMS edit home page
     */
    public function homepageAction()
    {
        $homepage = $this->_getDefaultHomepage(0);
        $id = $homepage ? $homepage->getId() : 1;
        Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl('adminhtml/cms_page/edit/', array('page_id' => $id)));
    }

    /**
     * Retrieve default homepage for the given store identifier
     *
     * @param  int $storeId Store identifier
     * @return Mage_Cms_Model_Page Page Model
     */
    protected function _getDefaultHomepage($storeId)
    {
        /** @var $cmsPage Mage_Cms_Model_Page */
        $cmsPage = Mage::getModel('cms/page')
            ->setStoreId($storeId)
            ->load(Mage::getStoreConfig('web/default/cms_home_page', $storeId));

        if (!$cmsPage->getId()) {
            return null;
        }

        return $cmsPage;
    }

    /**
     * Save configuration
     *
     */
    public function saveAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        /* @var $session Mage_Adminhtml_Model_Session */

        $groups = $this->getRequest()->getPost('groups');

        if (isset($_FILES['groups']['name']) && is_array($_FILES['groups']['name'])) {
            /**
             * Carefully merge $_FILES and $_POST information
             * None of '+=' or 'array_merge_recursive' can do this correct
             */
            foreach($_FILES['groups']['name'] as $groupName => $group) {
                if (is_array($group)) {
                    foreach ($group['fields'] as $fieldName => $field) {
                        if (!empty($field['value'])) {
                            $groups[$groupName]['fields'][$fieldName] = array('value' => $field['value']);
                        }
                    }
                }
            }
        }

        try {
            $sectionBlugento = $this->getRequest()->getParam('section', 'configuration');
            $sections = (array) Mage::getConfig()->getNode('default/blugento_menu/sections');
            if (!isset($sections[$sectionBlugento])) {
                throw new Exception(Mage::helper('adminhtml')->__('This section is not allowed1.'));
            }
            $sectionBlugento = $sections[$sectionBlugento];
            $groupsBlugento = (array) $sectionBlugento->groups;

            $website = $this->getRequest()->getParam('website');
            $store = $this->getRequest()->getParam('store');

            foreach ($groupsBlugento as $groupBlugento) {
                $section = $groupBlugento->admin_section;

                /*if (!$this->_isSectionAllowed($section)) {
                    throw new Exception(Mage::helper('adminhtml')->__('This section is not allowed.'));
                }*/

                Mage::getSingleton('adminhtml/config_data')
                    ->setSection($section)
                    ->setWebsite($website)
                    ->setStore($store)
                    ->setGroups($groups)
                    ->save();

                // reinit configuration
                Mage::getConfig()->reinit();
                Mage::dispatchEvent('admin_system_config_section_save_after', array(
                    'website' => $website,
                    'store' => $store,
                    'section' => $section
                ));
                Mage::app()->reinitStores();

                // website and store codes can be used in event implementation, so set them as well
                Mage::dispatchEvent("admin_system_config_changed_section_{$section}",
                    array('website' => $website, 'store' => $store)
                );
            }
            $session->addSuccess(Mage::helper('adminhtml')->__('The configuration has been saved.'));
        }
        catch (Mage_Core_Exception $e) {
            foreach(explode("\n", $e->getMessage()) as $message) {
                $session->addError($message);
            }
        }
        catch (Exception $e) {
            $session->addException($e,
                Mage::helper('adminhtml')->__('An error occurred while saving this configuration:') . ' '
                . $e->getMessage());
        }

        $this->_saveState($this->getRequest()->getPost('config_state'));

        $this->_redirect('*/*/configuration', array('_current' => array('section', 'website', 'store')));
    }

    /**
     * Check if specified section allowed in ACL
     *
     * Will forward to deniedAction(), if not allowed.
     *
     * @param string $section
     * @return bool
     */
    protected function _isSectionAllowed($section)
    {
        try {
            $session = Mage::getSingleton('admin/session');
            $resourceLookup = "admin/system/config/{$section}";
            if ($session->getData('acl') instanceof Mage_Admin_Model_Acl) {
                $resourceId = $session->getData('acl')->get($resourceLookup)->getResourceId();
                if (!$session->isAllowed($resourceId)) {
                    throw new Exception('');
                }
                return true;
            }
        }
        catch (Zend_Acl_Exception $e) {
            $this->norouteAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        catch (Exception $e) {
            $this->deniedAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
    }

    /**
     * Save state of configuration field sets
     *
     * @param array $configState
     * @return bool
     */
    protected function _saveState($configState = array())
    {
        $adminUser = Mage::getSingleton('admin/session')->getUser();
        if (is_array($configState)) {
            $extra = $adminUser->getExtra();
            if (!is_array($extra)) {
                $extra = array();
            }
            if (!isset($extra['configState'])) {
                $extra['configState'] = array();
            }
            foreach ($configState as $fieldset => $state) {
                $extra['configState'][$fieldset] = $state;
            }
            $adminUser->saveExtra($extra);
        }

        return true;
    }
}
