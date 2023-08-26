<?php
/**
 * Class AW_Blog_Model_System_Config_Source_LayoutsStylingList
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
 * @package     Blugento_Blog
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class AW_Blog_Model_System_Config_Source_LayoutsStylingList
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'default', 'label'=>Mage::helper('adminhtml')->__('Default')),
            array('value' => 'layout-1', 'label'=>Mage::helper('adminhtml')->__('List Layout 1')),
            array('value' => 'layout-2', 'label'=>Mage::helper('adminhtml')->__('List Layout 2')),
            array('value' => 'layout-3', 'label'=>Mage::helper('adminhtml')->__('List Layout 3')),
            array('value' => 'layout-4', 'label'=>Mage::helper('adminhtml')->__('List Layout 4')),
        );
    }

}