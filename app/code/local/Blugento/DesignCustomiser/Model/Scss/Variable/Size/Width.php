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

class Blugento_DesignCustomiser_Model_Scss_Variable_Size_Width
    extends Blugento_DesignCustomiser_Model_Scss_Variable_Abstract
{
    /**
     * Size_Width values
     * @var array
     */
    private $_values = array(
        "auto"      => "auto",
        "1130px"    => "1130px",
        "1170px"    => "1170px",
        "1200px"    => "1200px",
        "1210px"    => "1210px",
        "1240px"    => "1240px",
        "1280px"    => "1280px",
        "1320px"    => "1320px",
        "100%"      => "100%"
    );

    /**
     * Get type
     * @return string
     */
    public function getType()
    {
        return Mage::helper('blugento_designcustomiser')->getAllowedVariableType('size_width');
    }

    /**
     * Validate save value
     * @return boolean
     */
    public function validate()
    {
        $value = $this->getSaveValue();
        if (in_array($value, array_keys($this->_values))) {
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
