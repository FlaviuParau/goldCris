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
 * @package     Blugento_Feeds
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Feeds_Model_System_Config_Source_Categories
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $categories[] = [
            'value' => 'all',
            'label' => Mage::helper('blugento_feeds')->__('All Products')
        ];

        foreach ($this->getCategories() as $category) {
            if ($category->getId() != 1) {
                $categories[] = [
                    'value' => $category->getId(),
                    'label' => $category->getName()
                ];
            }
        }

        return $categories;
    }

    /**
     * Return category collection
     *
     * @return mixed
     */
    public function getCategories()
    {
        return Mage::getModel('catalog/category')->getCollection()->addAttributeToSelect('name');
    }
}