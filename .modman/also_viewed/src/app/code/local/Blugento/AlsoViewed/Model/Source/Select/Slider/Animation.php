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
 * @package     Blugento_AlsoViewed
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_AlsoViewed_Model_Source_Select_Slider_Animation
{
    public function toOptionArray()
    {
        return array(
            array(
                'label'      => Mage::helper('blugento_alsoviewed')->__('500 ms'),
                'value'      => 500
            ),
            array(
                'label'      => Mage::helper('blugento_alsoviewed')->__('400 ms'),
                'value'      => 400
            ),
            array(
                'label'      => Mage::helper('blugento_alsoviewed')->__('300 ms'),
                'sort_order' => 1,
                'value'      => 300
            ),
            array(
                'label'      => Mage::helper('blugento_alsoviewed')->__('200 ms'),
                'value'      => 200
            ),
            array(
                'label'      => Mage::helper('blugento_alsoviewed')->__('100 ms'),
                'value'      => 100
            )
        );
    }
}
