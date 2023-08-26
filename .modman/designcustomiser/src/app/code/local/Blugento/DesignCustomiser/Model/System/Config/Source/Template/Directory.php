<?php
/**
 * Helper class
 * Class Blugento_DesignCustomiser_Helper_Data
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
 * @package     Blugento_DesignCustomiser
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_DesignCustomiser_Model_System_Config_Source_Template_Directory
{
    /**
     * Directory theme places for template file
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'presets',
                'label' => 'presets'
            )
        );
    }
}
