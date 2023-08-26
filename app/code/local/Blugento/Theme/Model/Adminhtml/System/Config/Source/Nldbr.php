<?php
/**
 * Class Blugento_Theme_Model_Adminhtml_System_Config_Source_Nldbr
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
 * @package     Blugento_Theme
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Theme_Model_Adminhtml_System_Config_Source_Nldbr
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Disable break on enter')),
            array('value' => 2, 'label'=>Mage::helper('adminhtml')->__('Enable break on enter if no html tags exists')),
            array('value' => 3, 'label'=>Mage::helper('adminhtml')->__('Enable break on enter'))
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            1 => Mage::helper('adminhtml')->__('Disable break on enter'),
            2 => Mage::helper('adminhtml')->__('Enable break on enter if no html tags exists'),
            3 => Mage::helper('adminhtml')->__('Enable break on enter'),
        );
    }

}
