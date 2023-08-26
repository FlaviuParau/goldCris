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

class Blugento_DesignCustomiser_Model_Scss_Variable_Font_Weight
    extends Blugento_DesignCustomiser_Model_Scss_Variable_Abstract
{
    /**
     * Font_Weight values
     * @var array
     */
    private $_values = array(
        "inherit"   => "inherit",
        "100"       => "100",
        "200"       => "200",
        "300"       => "300",
        "400"       => "400",
        "500"       => "500",
        "600"       => "600",
        "700"       => "700",
        "800"       => "800",
        "900"       => "900"
    );

    /**
     * Get types
     * @return string
     */
    public function getType()
    {
        return Mage::helper('blugento_designcustomiser')->getAllowedVariableType('font_weight');
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
