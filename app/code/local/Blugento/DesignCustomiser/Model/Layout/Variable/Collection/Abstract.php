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

abstract class Blugento_DesignCustomiser_Model_Layout_Variable_Collection_Abstract
    extends Varien_Data_Collection
{
    /**
     * Helper class to get info about definition file and variable models
     * @var Mage_Core_Helper_Abstract 
     */
    protected $_helper = null;

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->_setHelper();
    }
    
    /**
     * Set helper
     * @return Blugento_DesignCustomiser_Model_Layout_Variable_Collection_Abstract
     */
    abstract protected function _setHelper();

    /**
     * Load data
     * @param boolean $printQuery
     * @param boolean $logQuery
     * @return Blugento_DesignCustomiser_Model_Layout_Variable_Collection
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        $data = $this->_helper->getLayoutDefinitionModel()->getVariables();

        if (count($data)) {
            foreach ($data as $variable) {
                $item = $this->_helper->getLayoutVariableModel($variable);
                if (!is_null($item) && $item instanceof Blugento_DesignCustomiser_Model_Layout_Variable_Abstract) {
                    $this->addItem($item);
                }
            }
        }

        $this->_setIsLoaded();

        // added for future development on printing messages based on bool operations with this 2 paramaters
        if ($printQuery && $logQuery) { }

        return $this;
    }

    /**
     * Get variables values as array (id => default)
     * @param string|null $templateName
     * @return array
     */
    public function getVariableValues($templateName = null)
    {
        $this->load();

        if (!count($this->getItems())) {
            return array();
        }

        $data = array();

        $xmlSaveValues =  $this->_helper->getLayoutXMLFileValues();

        if ($templateName !== null) {
            $xmlSaveValues->setApplyTemplate($templateName);
        }

        foreach ($this->getItems() as $variable) {
            /**
             * @var $variable Blugento_DesignCustomiser_Model_Layout_Variable_Abstract
             */
            $value = $xmlSaveValues->getVariableValue($variable);
            $data[$variable->getId()] = $value ? $value : $variable->getDefault();
            $variable->setDefaultValue($data[$variable->getId()]);
        }

        return $data;
    }

    /**
     * Get variable by id
     * @param string $parentId
     * @return null|Blugento_DesignCustomiser_Model_Layout_Variable_Abstract
     */
    public function getVariableById($parentId)
    {
        foreach ($this->getItems() as $variable) {
            if ($variable->getId() == $parentId) {
                return $variable;
            }
        }

        return null;
    }
}
