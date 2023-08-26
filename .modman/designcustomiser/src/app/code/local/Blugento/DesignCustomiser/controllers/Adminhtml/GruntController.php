<?php
/**
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
 * @package     Blugento_DesignCustomiser
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_DesignCustomiser_Adminhtml_GruntController extends Mage_Adminhtml_Controller_Action
{
    private $_adminSession = null;

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('blugento_adminmenu/blugento_designcustomiser');
    }

    /**
     * Index Action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('blugento_adminmenu/blugento_designcustomiser/gruntlogs');
        $this->_addBreadcrumb(
            Mage::helper('blugento_designcustomiser')->__('Customise Design'),
            Mage::helper('blugento_designcustomiser')->__('Customise Design')
        );
        $this->_title('Customise Design - Grunt Logs');

        $this->renderLayout();
    }

    /**
     * Clear Logs Action
     */
    public function clearlogsAction()
    {
        if (empty($this->_adminSession)) {
            $this->_adminSession = Mage::getSingleton('adminhtml/session');
        }
        $this->_clearLogsFile();

        $this->_redirect('*/*/index');
    }

    /**
     * Clears Grunt logs file
     * @return bool
     */
    private function _clearLogsFile()
    {
        $allCleared = true;

        $model = Mage::helper('blugento_designcustomiser')->getGruntLogsDefinitionModel();
        if (!$model) {
            $this->_adminSession->addError($this->__("Grunt logs file doesn't exist or is not writable!"));
            $allCleared = false;
        } elseif ($model->clearContent()) {
            $this->_adminSession->addSuccess($this->__('Grunt logs file cleared!'));
        } else {
            $this->_adminSession->addError($this->__("Grunt logs file doesn't exist or is not writable!"));
            $allCleared = false;
        }

        $model = Mage::helper('blugento_designcustomiser')->getGruntLogsImageDefinitionModel();
        if (!$model) {
            $this->_adminSession->addError($this->__("Grunt images logs file doesn't exist or is not writable!"));
            $allCleared = false;
        } elseif ($model->clearContent()) {
            $this->_adminSession->addSuccess($this->__('Grunt images logs file cleared!'));
        } else {
            $this->_adminSession->addError($this->__("Grunt images logs file doesn't exist or is not writable!"));
            $allCleared = false;
        }
        
        return $allCleared;
    }
}
