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

class Blugento_DesignCustomiser_Model_Scss_Variable_Border_Width
    extends Blugento_DesignCustomiser_Model_Scss_Variable_Abstract
{
    /**
     * Padding_Left values
     * @var array
     */
    private $_values = array(
        "auto"  => "auto",
        "1px"   => "1px",
        "2px"   => "2px",
        "3px"   => "3px",
        "4px"   => "4px",
        "5px"   => "5px",
        "6px"   => "7px",
        "8px"   => "8px",
        "9px"   => "9px",
        "10px"  => "10px",
        "11px"  => "11px",
        "12px"  => "12px",
        "13px"  => "13px",
        "14px"  => "14px",
        "15px"  => "15px",
        "16px"  => "16px",
        "17px"  => "17px",
        "18px"  => "18px",
        "19px"  => "19px",
        "20px"  => "20px",
    );

    /**
     * Get type
     * @return string
     */
    public function getType()
    {
        return Mage::helper('blugento_designcustomiser')->getAllowedVariableType('border_width');
    }

    /**
     * Validate save value
     * @return boolean
     */
    public function validate()
    {
        $value = $this->getSaveValue();
        if ($value && ($value == Mage::helper('blugento_designcustomiser')->getVariableAutoValue() || in_array($value, array_keys($this->_values)) || ($value && $value[0] == '$'))) {
            return true;
        }
        return false;
    }

    /**
     * Get options
     * @return array
     */
    public function getOptions()
    {
        return $this->_values;
    }
}
