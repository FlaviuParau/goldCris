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
 * @package     Blugento_Localizer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Localizer_Model_Setup_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * @var Mage_Eav_Model_Entity_Setup
     */
    protected $_setup;

    /**
     * @var Varien_Db_Adapter_Interface
     */
    protected $_connection;

    /**
     * Setup setup class and connection
     */
    public function __construct()
    {
        $this->_setup = Mage::getModel('eav/entity_setup', 'core_setup');
        $this->_connection = $this->_setup->getConnection();
    }

    /**
     * Get config.xml data
     *
     * @return Varien_Simplexml_Element Config data
     */
    public function getConfigData()
    {
        return Mage::getSingleton('blugento_localizer/config')->getNode('default/localizer');
    }

    /**
     * Saves a system config value for the given config path and the given store id
     *
     * @param string   $configPath Config Path
     * @param string   $value      Value
     * @param int|null $storeId    Store ID
     */
    public function setConfigData($configPath, $value, $storeId = null)
    {
        $setup = $this->_getSetup();
        if (is_null($storeId) || $storeId == 0) {
            $setup->setConfigData($configPath, $value);
        } else {
            $setup->setConfigData($configPath, $value, 'stores', $storeId);
        }
    }

    /**
     * Get config.xml data
     *
     * @param  string      $node      xml node
     * @param  string|null $childNode if set, child node of the first node
     * @return array Config Node
     */
    protected function _getConfigNode($node, $childNode = null)
    {
        $configData = $this->getConfigData()->asArray();
        if ($childNode) {
            return $configData[$node][$childNode];
        } else {
            return $configData[$node];
        }
    }

    /**
     * Get template content
     *
     * @param  string $filename Template file name
     * @return string Template content
     */
    public function getTemplateContent($filename)
    {
        return @file_get_contents($filename);
    }

    /**
     * Load a model by attribute code
     *
     * @param  Mage_Core_Model_Abstract $model         Collection
     * @param  string                   $attributeCode Attribute code
     * @param  string                   $value         Value
     * @return Mage_Core_Model_Abstract
     */
    protected function _loadExistingModel($model, $attributeCode, $value)
    {
        foreach ($model->getCollection() as $singleModel) {
            if ($singleModel->getData($attributeCode) == $value) {
                $model->load($singleModel->getId());

                return $model;
            }
        }

        return $model;
    }

    /**
     * Retrieve the database connection
     *
     * @return Varien_Db_Adapter_Interface Database connection
     */
    protected function _getConnection()
    {
        return $this->_connection;
    }

    /**
     * Retrieve the Magento setup model class
     *
     * @return Mage_Eav_Model_Entity_Setup Setup Model
     */
    protected function _getSetup()
    {
        return $this->_setup;
    }

    /**
     * Get setup country ID
     *
     * @return string
     */
    public function getCountryId()
    {
        if (!$this->_getData('country_id')) {
            $this->setData('country_id', Mage::registry('setup_country'));
        }

        return $this->_getData('country_id');
    }
}
