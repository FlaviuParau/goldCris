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
 * @package     Blugento_ExtendAwBlog
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_ExtendAwBlog_Model_Mysql4_Blog_Collection extends AW_Blog_Model_Mysql4_Blog_Collection
{
    public function _construct()
    {
        parent::_construct();

        $this->_init('blog/blog');

        $confSortBy = Mage::getStoreConfig('blog/blog/sort_by')
            ? Mage::getStoreConfig('blog/blog/sort_by')
            : 'created_time';
        $confSortDirection = Mage::getStoreConfig('blog/blog/sort_direction')
            ? Mage::getStoreConfig('blog/blog/sort_direction')
            : 'desc';

        $this->setOrder($confSortBy, $confSortDirection);
    }
}
