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

interface Blugento_DesignCustomiser_Model_Layout_Save_Interface
{
    /**
     * Save the collection data in file
     * @param Varien_Data_Collection $collection
     * @return array Success and errors
     */
    public function save(Varien_Data_Collection $collection);
    
    /**
     * Get Variable Value
     * @param Blugento_DesignCustomiser_Model_Scss_Variable_Abstract $variable
     * @return string
     */
    public function getVariableValue(Blugento_DesignCustomiser_Model_Layout_Variable_Abstract $variable);
}
