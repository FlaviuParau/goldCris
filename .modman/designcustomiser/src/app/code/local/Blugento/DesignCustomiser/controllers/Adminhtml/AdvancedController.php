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

class Blugento_DesignCustomiser_Adminhtml_AdvancedController extends Mage_Adminhtml_Controller_Action
{
    protected $_adminSession = null;

    /**
     * Index Action
     */
    public function indexAction()
    {
	    Mage::getSingleton('admin/session')->setDesignCustomiserMode('advanced');
        $this->loadLayout();
        $this->_setActiveMenu('blugento_adminmenu/blugento_designcustomiser/blugento_designcustomiser');
        $this->_addBreadcrumb(
            Mage::helper('blugento_designcustomiser')->__('Advanced Customise Design'),
            Mage::helper('blugento_designcustomiser')->__('Advanced Customise Design')
        );
        $this->_title('Customise Design');

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

        $params = $this->getRequest()->getParams();
        if (isset($params['key'])) {
            $params['key'] = null;
            unset($params['key']);
        }
        if (empty($params)) {
            Mage::getSingleton('adminhtml/session')->getMessages(true);
            $this->_redirect('*/*/index', array('_query' => array('a' => 1)));
            return;
        }

        $this->_saveStyleing();

        $this->_saveImages();

        $this->_saveLayout();

        $this->_saveFinalCss();

        $this->_saveTemplate();

        $this->clearCache();

        $result = $this->_runGruntRelease();
        if ($result['code'] == 'success') {
            $this->_adminSession->addSuccess($result['message']);
        } else {
            $this->_adminSession->addError($result['message']);
        }

        if ($activeTabId = $this->getRequest()->getParam('setup_active_tab_id')) {
            Mage::getSingleton('admin/session')->setActiveTabId($activeTabId);
        }

        if ($activeTabTopId = $this->getRequest()->getParam('setup_active_tab_top_id')) {
            Mage::getSingleton('admin/session')->setActiveTabTopId($activeTabTopId);
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Upload image file if valid
     * @return Blugento_DesignCustomiser_Adminhtml_AdvancedController
     */
    private function _saveImages()
    {
        $collectionImages = Mage::getModel('blugento_designcustomiser/scss_variable_image_collection')->load();
        $helper = Mage::helper('blugento_designcustomiser/scss_image');

        $xmlSaveValues = $helper->getImageXMLSaveFileValues();
        $scssSaveValues = $helper->getImageScssFileValues();

        /**
         * @var $imageSave Blugento_DesignCustomiser_Model_Scss_Save_Image
         */
        $imageSave = $helper->getImageXMLFileValues();

        $collectionImagesScss = Mage::getModel('blugento_designcustomiser/scss_variable_image_collection')->load();
        foreach ($collectionImages as $variable) {
            $imageType = $variable->getType();
            $variableTitle = $this->__($variable->getTranslationPrefix() . '/title/' . $variable->getId());

            if (!$variable->getDefault()) {
                $collectionImages->removeItemByKey($variable->getId());
                $collectionImagesScss->removeItemByKey($variable->getId());
                continue;
            }

            $reset = $this->getRequest()->getParam('reset-image-' . $variable->getId());
            if (!empty($reset) && $variable->getDefault() == $reset) {
                $collectionImages->removeItemByKey($variable->getId());
                $imageSave->resetImage($variable);
                $variable->setSaveValue(array('name' => $variable->getDefault()));
                $variableScss = $collectionImagesScss->getVariableById($variable->getId());
                $variableScss->setSaveValue(array('name' => $variable->getDefault()));
                continue;
            }

            if (!isset($_FILES[$variable->getId()]) || $_FILES[$variable->getId()]['error'] == 4) {
                $variableOldValue = $xmlSaveValues->getVariableValue($variable);
                if (!$variableOldValue) {
                    $variable->setSaveValue(array('name' => $variable->getDefault()));
                    $variableScss = $collectionImagesScss->getVariableById($variable->getId());
                    $variableScss->setSaveValue(array('name' => $variable->getDefault()));
                } else {
                    $collectionImagesScss->removeItemByKey($variable->getId());
                }
                $collectionImages->removeItemByKey($variable->getId());
                continue;
            }

            if ($_FILES[$variable->getId()]['error'] != 0) {
                $this->_adminSession->addError($this->__('%s file is not uploaded ok. Please try again!', $variableTitle));
                $collectionImages->removeItemByKey($variable->getId());
                $collectionImagesScss->removeItemByKey($variable->getId());
                continue;
            }

            if ($imageType == 'default') {
                $arr = explode('.', $_FILES[$variable->getId()]['name']);
                $uploadType = strtolower(array_pop($arr));
                $uploadType = $helper->getAllowedVariableType($uploadType);

                if (!$variable->validateType($uploadType)) {
                    // uploaded file type not allowed
                    $this->_adminSession->addError($this->__('%s file type is not allowed. Allowed file types: %s.', $variableTitle, $variable->getXMLTypeString()));
                    $collectionImages->removeItemByKey($variable->getId());
                    $collectionImagesScss->removeItemByKey($variable->getId());
                    continue;
                }

                $imageType = $uploadType;
                $variable->uploadType = $imageType;
            }

            $saveValue = $_FILES[$variable->getId()];
            $variable->setSaveValue($saveValue);
            $variable->setUploadSizes();

            $nameArray = array_filter(explode('.', $saveValue['name']));
            $uploadType = strtolower(array_pop($nameArray));
            $nameArray = array_filter(explode('.', $variable->getDefault()));
            $saveValue['name'] = $nameArray[0] . '.' . $uploadType;
            $variableScss = $collectionImagesScss->getVariableById($variable->getId());
            $variableScss->setSaveValue($saveValue);
            $variableScss->setUploadSizes();

            $imageSize = explode('x', $variable->getSize());

            if ($imageType != 'svg' && (!is_array($imageSize) || !count($imageSize) == 2 || !$variable->validateRatio($imageSize))) {
                $collectionImages->removeItemByKey($variable->getId());
                $collectionImagesScss->removeItemByKey($variable->getId());
                continue;
            }
        }

        $result = $imageSave->save($collectionImages);
        foreach ($result['success'] as $variableId => $variableTitle) {
            $this->_adminSession->addSuccess($this->__('%s upload successfully.', $variableTitle));
        }
        foreach ($result['error'] as $variableId => $variableTitle) {
            $this->_adminSession->addError($this->__('%s upload error!', $variableTitle));
        }

        $xmlSaveValues->save($collectionImagesScss);
        $scssSaveValues->save($collectionImagesScss);

        return $this;
    }

    /**
     * Save data from Style Tab
     * @return Blugento_DesignCustomiser_Adminhtml_AdvancedController
     */
    private function _saveStyleing()
    {
        $collectionScss = Mage::getModel('blugento_designcustomiser/scss_variable_collection')->load();

        $newValues = array();

        $autoValue = Mage::helper('blugento_designcustomiser')->getVariableAutoValue();
        foreach ($collectionScss as $variable) {
            $value = $this->getRequest()->getParam('set-inherit-' . $variable->getId());
            if ($variable->getAllowInherit()) {
                if ($value == 'yes') {
                    $newValues[$variable->getId()] = $this->getRequest()->getParam($variable->getId() . '-inherit');
                } elseif ($value == $autoValue) {
                    $newValues[$variable->getId()] = $autoValue;
                } else {
                    $newValues[$variable->getId()] = $this->getRequest()->getParam($variable->getId());
                }
            } elseif ($value == $autoValue) {
                $newValues[$variable->getId()] = $autoValue;
            } else {
                $newValues[$variable->getId()] = $this->getRequest()->getParam($variable->getId());
            }
        }

        return $this->_saveVariables($newValues);
    }

    /**
     * Save data from Final CSS Tab
     * @return Blugento_DesignCustomiser_Adminhtml_AdvancedController
     */
    private function _saveFinalCss()
    {
        $helper = Mage::helper('blugento_designcustomiser');

        try {
            $contentOld = $helper->getFinalCssDefinitionModel()->loadContent();
            $content = $this->getRequest()->getParam('final_css');
            if ($contentOld == $content || trim($content) == '') {
                return $this;
            }

            $model = Mage::getModel('blugento_designcustomiser/finalcss');
            $model->setData('css', $content);
            $model->save();

            $this->_adminSession->addSuccess($this->__('Final CSS saved successfully.'));
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_adminSession->addError($this->__('Final CSS cannot be saved. ERROR: ') . $e->getMessage());
        }

        return $this;
    }

    public function deleteAction()
    {
        $versionId = $this->getRequest()->getParam('version_id');

        try {
            $model = Mage::getModel('blugento_designcustomiser/finalcss')->load($versionId);
            $model->delete();

            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Final CSS Version deleted.'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Final CSS Version cannot be deleted. ERROR:: ') . $e->getMessage());
        }

        $refUrl = Mage::helper('adminhtml')->getUrl('adminhtml/adminhtml_advanced/index');
        $this->_redirectUrl($refUrl);
    }

    /**
     * Save style and images data from Template Tab
     * @return Blugento_DesignCustomiser_Adminhtml_AdvancedController
     */
    private function _saveTemplate()
    {
        // Get preset values
        $template = $this->getRequest()->getParam('template');
        if (!$template) {
            return null;
        }

        $this->_saveTemplateStyleing($template);
        $this->_saveTemplateImages($template);

        return $this;
    }

    /**
     * Save style data from Template Tab
     * @param string $template
     * @return Blugento_DesignCustomiser_Adminhtml_AdvancedController
     */
    private function _saveTemplateStyleing($template)
    {
        $helper = Mage::helper('blugento_designcustomiser');
        $xmlSaveValues = $helper->getScssXMLFileValues();
        $scssSaveValues = $helper->getScssFileValues();

        $xmlSaveValues->setApplyTemplate($template);
        $collectionScss = Mage::getModel('blugento_designcustomiser/scss_variable_collection')->load();
        $presetValues = $collectionScss->getVariableValues();
        $xmlSaveValues->unsetApplyTemplate();

        return $this->_saveVariables($presetValues, true, $this->__('It was restored to %s.', $template));
    }

    /**
     * Save images data from Template Tab
     * @param string $template
     * @return Blugento_DesignCustomiser_Adminhtml_AdvancedController
     */
    private function _saveTemplateImages($template)
    {
        $collectionImages = Mage::getModel('blugento_designcustomiser/scss_variable_image_collection')->load();
        $helper = Mage::helper('blugento_designcustomiser/scss_image');

        /**
         * @var $imageSave Blugento_DesignCustomiser_Model_Scss_Save_Image
         */
        $imageSave = $helper->getImageXMLFileValues();

        foreach ($collectionImages as $variable) {
            $imageSave->resetImage($variable, $template);
        }

        return $this;
    }

    /**
     * Save variable values
     *
     * @param array $newValues
     * @param bool $fromTemplate
     * @param string $messageAppend
     * @return Blugento_DesignCustomiser_Adminhtml_AdvancedController
     */
    private function _saveVariables($newValues, $fromTemplate = false, $messageAppend = '')
    {
        $helper = Mage::helper('blugento_designcustomiser');
        $xmlSaveValues = $helper->getScssXMLFileValues();
        $scssSaveValues = $helper->getScssFileValues();

        $collectionScss = Mage::getModel('blugento_designcustomiser/scss_variable_collection')->load();

        $alreadySendMessage = array();

        $titleParents = array();

        foreach ($collectionScss as $variable) {

            $variableId = $variable->getId();
            $variableTitle = $this->__($variable->getTranslationPrefix() . '/title/' . $variable->getId());

            $variableParentId = $variable->getParentId();
            if (!$variableParentId) {
                $titleParents[$variableId] = $variableTitle;
            }

            if (!$variable->getDefault()) {
                $collectionScss->removeItemByKey($variableId);
                continue;
            }

            $variableOldValue = $xmlSaveValues->getVariableValue($variable);

            if ($fromTemplate) {
                if (isset($newValues[$variableId])) {
                    $variableValue = $newValues[$variableId];
                } else {
                    $variableValue = $variableOldValue ? $variableOldValue : $variable->getDefault();
                }
            } else {
                $variableValue = isset($newValues[$variableId]) ? $newValues[$variableId] : null;
            }

            $variable->setSaveValue($variableValue);

            if (!$variable->validate()) {
                $collectionScss->removeItemByKey($variableId);
                $this->_adminSession->addError($this->__('%s is not valid.', $variableTitle));
                continue;
            }

            $changed = false;
            if (is_array($variableValue)) {
                $variableOldValueArr = explode('**', $variableOldValue);
                if (count($variableValue) != count($variableOldValueArr)) {
                    $changed = true;
                } else {
                    foreach ($variableValue as $value) {
                        if (!in_array($value, $variableOldValueArr)) {
                            $changed = true;
                            break;
                        }
                    }
                }
            } else {
                if ($variableValue != $variableOldValue) {
                    $changed = true;
                }
            }
            if ($changed) {
                if (!$variableParentId || !isset($alreadySendMessage[$variableParentId])) {

                    $title = (!$variableParentId) ?
                        $titleParents[$variableId] :
                        $titleParents[$variableParentId];

                    $alreadySendMessage[$variableParentId] = true;
                    $this->_adminSession->addSuccess($this->__('%s saved successfully. %s', $title, $this->__($messageAppend)));
                }
            }
        }

        $xmlSaveValues->save($collectionScss);

        // Remove auto values from collection
        $autoValue = Mage::helper('blugento_designcustomiser')->getVariableAutoValue();
        foreach ($collectionScss as &$variable) {
            if ($variable->getSaveValue() == $autoValue) {
                $variable->setSkipScss(true);
                if (!$variable->getAllowInherit()) {
                    $variableId = '$' . $variable->getId();
                    foreach ($collectionScss as &$variableChild) {
                        if ($variableChild->getSaveValue() == $variableId) {
                            $variableChild->setSkipScss(true);
                        }
                    }
                }
            }
        }

        $scssSaveValues->save($collectionScss);

        return $this;
    }

    /**
     * Save data from Layout Tab
     * @return Blugento_DesignCustomiser_Adminhtml_DesignController
     */
    private function _saveLayout()
    {
        $collectionLayout = Mage::getModel('blugento_designcustomiser/layout_variable_collection')->load();

        $newValues = array();

        foreach ($collectionLayout as $variable) {
            $newValues[$variable->getId()] = $this->getRequest()->getParam($variable->getId());
        }

        return $this->_saveVariablesLayout($newValues);
    }

    /**
     * Save variable values
     *
     * @param array $newValues
     * @param bool $forceUpdate
     * @param string $messageAppend
     * @return Blugento_DesignCustomiser_Adminhtml_AdvancedController
     */
    private function _saveVariablesLayout($newValues, $forceUpdate = false, $messageAppend = '')
    {
        $helper = Mage::helper('blugento_designcustomiser');
        $xmlSaveValues = $helper->getLayoutXMLFileValues();
        $xmlSaveUpdateValues = $helper->getLayoutUpdateXMLFileValues();
        $scssSaveValues = $helper->getLayoutScssFileValues();
        $disableSaveValues = $helper->getLayoutXMLDisableModuleFileValues();

        $collectionLayout = Mage::getModel('blugento_designcustomiser/layout_variable_collection')->load();

        $alreadySendMessage = array();

        $titleParents = array();

        foreach ($collectionLayout as $variable) {

            $variableId = $variable->getId();
            $variableTitle = $this->__($variable->getTranslationPrefix() . '/title/' . $variable->getId());

            $titleParents[$variableId] = $variableTitle;

            $variableValue = isset($newValues[$variableId]) ? $newValues[$variableId] : null;

            if (!$variable->getDefault()) {
                $collectionLayout->removeItemByKey($variableId);
                continue;
            }

            $variable->setSaveValue($variableValue);

            if (!$variable->validate()) {
                $collectionLayout->removeItemByKey($variableId);
                $this->_adminSession->addError($this->__('%s is not valid.', $variableTitle));
                continue;
            }

            if ($variableValue != $xmlSaveValues->getVariableValue($variable)) {
                if (!isset($alreadySendMessage[$variableId])) {

                    $title = $titleParents[$variableId];

                    $alreadySendMessage[$variableId] = true;
                    $this->_adminSession->addSuccess($this->__('%s saved successfully. %s', $title, $this->__($messageAppend)));
                }
            }
        }

        $xmlSaveValues->save($collectionLayout);

        $scssSaveValues->save($collectionLayout);

        $xmlSaveUpdateValues->save($collectionLayout);

        $disableSaveValues->save($collectionLayout);

        return $this;
    }

    protected function clearCache()
    {
        $types = array('config', 'layout', 'block_html');
        $instance = Mage::app()->getCacheInstance();
        foreach ($types as $type) {
            $instance->cleanType($type);
            Mage::dispatchEvent('adminhtml_cache_refresh_type', array('type' => $type));
        }

        if (!Mage::helper('core')->isModuleEnabled('MindMagnet_PageSpeed')) {
            return;
        }

        try {
            /* @var MindMagnet_PageSpeed_Helper_Data $helper */
            $helper = Mage::helper('mindmagnet_pagespeed');
            $helper->clearCache();
        } catch (Exception $e) {}
    }

    /**
     * Run grunt release trough Api call when designcustomiser is saved
     *
     * @return array
     */
    private function _runGruntRelease()
    {
        $helper = Mage::helper('blugento_designcustomiser');

        $token = $helper->getwAuthToken();

        if ($token) {
            $response = $helper->runGruntRelease($token);

            if ($response == 200) {
                $result['message'] = $helper->__('Grunt: Success!');
                $result['code'] = 'success';
            } elseif ($response == 401) {
                $result['message'] = $helper->__('Grunt: Authorization failed!');
                $result['code'] = 'error';
            } else {
                $releaseUrl = Mage::helper('adminhtml')->getUrl('*/*/grunt');
                $result['message'] = $helper->__('Grunt: Process error! Please try again later or') . ' <a href="' . $releaseUrl . '">Force Grunt</a>';
                $result['code'] = 'error';
            }
        } else {
            $result['message'] = $helper->__('Grunt: There is no token or "wAuthToken" file!');
            $result['code'] = 'error';
        }

        return $result;
    }

    /**
     * Force run grunt release trough Api call
     */
    public function gruntAction()
    {
        if (empty($this->_adminSession)) {
            $this->_adminSession = Mage::getSingleton('adminhtml/session');
        }

        $result = $this->_runGruntRelease();

        if ($result['code'] == 'success') {
            $this->_adminSession->addSuccess($result['message']);
        } else {
            $this->_adminSession->addError($result['message']);
        }

        $this->_redirect('*/*/index');
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('blugento_adminmenu/blugento_designcustomiser');
    }
}
