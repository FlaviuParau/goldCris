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
 * @package     Blugento_ProductLabels
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_ProductLabels_Model_Adminhtml_System_Config_Source_Label_Status
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => '0', 'label'=>Mage::helper('blugento_productlabels')->__('Disabled')),
            array('value' => '1', 'label'=>Mage::helper('blugento_productlabels')->__('Enabled')),
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
            0 => Mage::helper('blugento_productlabels')->__('Disabled'),
            1 => Mage::helper('blugento_productlabels')->__('Enabled'),
        );
    }
}
