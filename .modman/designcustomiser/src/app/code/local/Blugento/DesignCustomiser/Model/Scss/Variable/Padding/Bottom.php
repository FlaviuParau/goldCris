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

class Blugento_DesignCustomiser_Model_Scss_Variable_Padding_Bottom
    extends Blugento_DesignCustomiser_Model_Scss_Variable_Abstract
{
    /**
     * Padding_Bottom values
     * @var array
     */
    private $_values = array(
        "auto"  => "auto",
        "0px"   => "0px",
        "5px"   => "5px",
        "10px"  => "10px",
        "15px"  => "15px",
        "20px"  => "20px",
        "25px"  => "25px",
        "30px"  => "30px",
        "35px"  => "35px",
        "40px"  => "40px",
        "50px"  => "50px",
        "55px"  => "55px",
        "60px"  => "60px",
        "65px"  => "65px",
        "70px"  => "70px",
        "75px"  => "75px",
        "80px"  => "80px",
        "85px"  => "85px",
        "90px"  => "90px",
        "95px"  => "95px",
        "100px" => "100px"
    );

    /**
     * Get type
     * @return string
     */
    public function getType()
    {
        return Mage::helper('blugento_designcustomiser')->getAllowedVariableType('padding_bottom');
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
