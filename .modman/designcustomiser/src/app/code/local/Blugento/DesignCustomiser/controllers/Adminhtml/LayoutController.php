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

class Blugento_DesignCustomiser_Adminhtml_LayoutController extends Mage_Adminhtml_Controller_Action
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
        $this->_setActiveMenu('blugento_adminmenu/blugento_designcustomiser/blugento_designcustomiser');
        $this->_addBreadcrumb(
            Mage::helper('blugento_designcustomiser')->__('Customise Layout'),
            Mage::helper('blugento_designcustomiser')->__('Customise Layout')
        );
        $this->_title('Customise Layout');

        $this->renderLayout();
    }

    /**
     * Save Action
     */
    public function saveAction()
    {
        if (empty($this->_adminSession)) {
            $this->_adminSession = Mage::getSingleton('adminhtml/session');
        }

        $this->_saveLayout();

        $this->_redirect('*/*/index');
    }

    /**
     * Save data from Layout Tab
     * @return Blugento_DesignCustomiser_Adminhtml_LayoutController
     */
    private function _saveLayout()
    {
        $collectionLayout = Mage::getModel('blugento_designcustomiser/layout_variable_collection')->load();

        $newValues = array();

        foreach ($collectionLayout as $variable) {
            $newValues[$variable->getId()] = $this->getRequest()->getParam($variable->getId());
        }

        return $this->_saveVariables($newValues);
    }

    /**
     * Save variable values
     *
     * @param array $newValues
     * @param bool $forceUpdate
     * @param string $messageAppend
     * @return Blugento_DesignCustomiser_Adminhtml_LayoutController
     */
    private function _saveVariables($newValues, $forceUpdate = false, $messageAppend = '')
    {
        $helper = Mage::helper('blugento_designcustomiser');
        $xmlSaveValues = $helper->getLayoutXMLFileValues();
        $scssSaveValues = $helper->getScssFileValues();
        $layoutSaveValues = $helper->getLayoutFileValues();
        $disableSaveValues = $helper->getLayoutXMLDisableModuleFileValues();

        $collectionLayout = Mage::getModel('blugento_designcustomiser/layout_variable_collection')->load();

        $alreadySendMessage = array();

        $titleParents = array();

        foreach ($collectionLayout as $variable) {

            $variableId = $variable->getId();
            $titleParents[$variableId] = $variable->getTitle();

            $variableValue = isset($newValues[$variableId]) ? $newValues[$variableId] : null;

            if (!$variable->getDefault()) {
                $collectionLayout->removeItemByKey($variableId);
                continue;
            }

            $variable->setSaveValue($variableValue);

            if (!$variable->validate()) {
                $collectionLayout->removeItemByKey($variableId);
                $this->_adminSession->addError($variable->getTitle() . ' is not valid');
                continue;
            }

            if ($variableValue != $xmlSaveValues->getVariableValue($variable)) {
                if (!isset($alreadySendMessage[$variableId])) {

                    $title = $titleParents[$variableId];

                    $alreadySendMessage[$variableId] = true;
                    $this->_adminSession->addSuccess($title . ' saved successfully' . $messageAppend . '!');
                }
            }
        }

        $xmlSaveValues->save($collectionLayout);

        $layoutSaveValues->save($collectionLayout);

        $scssSaveValues->save($collectionLayout);

        $disableSaveValues->save($collectionLayout);

        if (!empty($alreadySendMessage)) {
            $this->clearCache();
        }

        return $this;
    }

    protected function clearCache()
    {
        $types = array('config', 'layout');
        $instance = Mage::app()->getCacheInstance();
        foreach ($types as $type) {
            $instance->cleanType($type);
            Mage::dispatchEvent('adminhtml_cache_refresh_type', array('type' => $type));
        }
    }
}
