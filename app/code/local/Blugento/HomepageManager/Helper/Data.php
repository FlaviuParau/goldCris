<?php
/**
 * Helper class
 * Class Blugento_HomepageManager_Helper_Data
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
 * @package     Blugento_HomepageManager
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_HomepageManager_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * System config path for layout file name
     * @var string
     */
    protected $_layoutFileName = 'blugento_homepagemanager/layout/filename';

    /**
     * System config path for layout directory
     * @var string
     */
    protected $_layoutDirectory = 'blugento_homepagemanager/layout/directory';

    /**
     * System config path for layout definition store
     * @var string
     */
    protected $_layoutStore = 'blugento_homepagemanager/layout/store';

    /**
     * System config path for user generated files directory
     * @var string
     */
    protected $_userDirectory = 'blugento_homepagemanager/user_directory';

    /**
     * Get Store theme ID where layout file is located
     * @return int
     */
    public function getLayoutDefinitionStore()
    {
        if (Mage::getStoreConfig($this->_layoutStore)) {
            return Mage::getStoreConfig($this->_layoutStore);
        }

        return $this->_getDefaultDefinitionStore();
    }

    /**
     * Get Store ID
     * @return int
     */
    protected function _getDefaultDefinitionStore()
    {
        $defaultWebsite = Mage::getModel('core/website')->load('1', 'is_default');

        $storeId = Mage_Core_Model_App::DISTRO_STORE_ID;

        if ($defaultWebsite->getId()) {
            $storeGroup = Mage::getModel('core/store_group')->load($defaultWebsite->getId(), 'website_id');
        }

        if ($storeGroup->getId()) {
            $storeId = $storeGroup->getDefaultStoreId();
        }

        return $storeId;
    }

    /**
     * Get filename of scss defintion variables
     * @return string
     */
    public function getUserDirectoryName()
    {
        $directory = 'blugento';

        if (Mage::getStoreConfig($this->_userDirectory)) {
            $directory = Mage::getStoreConfig($this->_userDirectory);
        }

        return $directory;
    }

    /**
     * Get layout XML file with values
     * @return Blugento_HomepageManagerr_Model_Layout_Save_Interface
     */
    public function getLayoutXMLFileValues()
    {
        return Mage::getSingleton('blugento_homepagemanager/layout_save_xml');
    }

    /**
     * Get Scss XML file with values
     * @return Blugento_HomepageManagerr_Model_Layout_Definition_Interface
     */
    public function getLayoutXMLFileDefinition()
    {
        return Mage::getSingleton('blugento_homepagemanager/layout_definition_xml');
    }

    /**
     * Get Scss XML file with values
     * @param mixed $storeId
     * @return Blugento_HomepageManagerr_Model_Layout_Definition_Interface
     */
    public function getLayoutNodes($storeId = 0)
    {
        try {
            $model = Mage::getSingleton('blugento_homepagemanager/layout_definition_xml');
            return $model->getItems($storeId);
        } catch (Exception $e) {
            Mage::logException($e);
            return array();
        }
    }

    public function saveLayout($items, $formValues)
    {
        if (!$items) {
            return false;
        }

        $items = @json_decode($items, true);
        if (!$items) {
            return false;
        }

        try {
            $xmlSaveValues = $this->getLayoutXMLFileValues();
            $xmlSaveValues->save($items, $formValues);

            Mage::getSingleton('adminhtml/session')->addSuccess('Homepage layout saved successfully!');
            return true;
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError('Homepage layout could not be saved!' . $e->getMessage());
            Mage::logException($e);
        }

        return false;
    }
}
