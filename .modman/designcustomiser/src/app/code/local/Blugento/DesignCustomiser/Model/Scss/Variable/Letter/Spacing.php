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

class Blugento_DesignCustomiser_Model_Scss_Variable_Letter_Spacing
    extends Blugento_DesignCustomiser_Model_Scss_Variable_Abstract
{
    /**
     * Variable input type form
     */
    protected $_inputTypeForm = 'select';

    /**
     * Display values
     * @var array
     */
    private $_values = array (
        "inherit"   => "inherit",
        "-2px"      => "-2px",
        "-1.75px"      => "-1.75px",
        "-1.5px"      => "-1.5px",
        "-1.25px"      => "-1.25px",
        "-1px"      => "-1px",
        "-0.75px"      => "-0.75px",
        "-0.5px"      => "-0.5",
        "-0.25px"      => "-0.25px",
        "0px"       => "0px",
        "0.25px"      => "0.25px",
        "0.5px"      => "0.5",
        "0.75px"      => "0.75px",
        "1px"      => "1px",
        "1.25px"      => "1.25px",
        "1.5px"      => "1.5px",
        "1.75px"      => "1.75px",
        "2px"      => "2px",
    );

    /**
     * Get type
     * @return string
     */
    public function getType()
    {
        return Mage::helper('blugento_designcustomiser')->getAllowedVariableType('letter_spacing');
    }

    /**
     * Validate save value
     * @return boolean
     */
    public function validate()
    {
        $value = $this->getSaveValue();
        if (in_array($value, array_keys($this->_values)) || ($value == 'auto') || ($value && $value[0] == '$')) {
            return true;
        }
        return true;
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
