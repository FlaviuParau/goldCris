<?php
/**
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
 * @package     Blugento_Swatches
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Swatches_Model_Adminhtml_System_Config_Source_Mode
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => '1', 'label'=>Mage::helper('blugento_swatches')->__('Hex Color')),
            array('value' => '2', 'label'=>Mage::helper('blugento_swatches')->__('Image File')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function getOptionArray()
    {
        return array(
            1 => Mage::helper('blugento_swatches')->__('Hex Color'),
            2 => Mage::helper('blugento_swatches')->__('Image File'),
        );
    }
}
