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

class Blugento_DesignCustomiser_Model_Scss_Variable_Image_Collection
    extends Blugento_DesignCustomiser_Model_Scss_Variable_Collection_Abstract
{
    /**
     * Helper class to get info about definition file and variable models
     * Set this in $this->_helper
     * @var Mage_Core_Helper_Abstract 
     */
    protected function _setHelper() 
    {
        $this->_helper = Mage::helper('blugento_designcustomiser/scss_image');
        return $this;
    }

    /**
     * Load data
     * @param boolean $printQuery
     * @param boolean $logQuery
     * @return Blugento_DesignCustomiser_Model_Scss_Image_Collection
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        $data = $this->_helper->getDefinitionModel()->getVariables();
        if (count($data)) {
            foreach ($data as $variable) {
                $item = $this->_helper->getVariableModel($variable);
                if (!is_null($item) && $item instanceof Blugento_DesignCustomiser_Model_Scss_Variable_Abstract) {
                    $this->addItem($item);
                }
            }
        }

        $this->_setIsLoaded();

        // added for future development on printing messages based on bool operations with this 2 parameters
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

        $xmlSaveValues =  $this->_helper->getScssXMLFileValues();

        if ($templateName !== null) {
            $xmlSaveValues->setApplyTemplate($templateName);
        }

        foreach ($this->getItems() as $variable) {
            /**
             * @var $variable Blugento_DesignCustomiser_Model_Scss_Variable_Abstract
             */
            $value = $xmlSaveValues->getVariableValue($variable);
            $data[$variable->getId()] = $value ? $value : $variable->getDefault();
            $variable->setDefaultValue($data[$variable->getId()]);
        }

        return $data;
    }
}
