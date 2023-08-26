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
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */


class Blugento_ExtendAwBlog_Model_System_Config_Source_Categories extends AW_Blog_Model_System_Config_Source_Categories
{
    public function toOptionArray()
    {
        $categories = parent::toOptionArray();

        $emptyElement = array(
            'label' => Mage::helper('blugento_extendawblog')->__('-- Please Select --'),
            'value' => '',
        );

        array_unshift($categories, $emptyElement);

        return $categories;
    }
}