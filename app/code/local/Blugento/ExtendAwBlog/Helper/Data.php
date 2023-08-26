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

class Blugento_ExtendAwBlog_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getImageWidth()
    {
        $confVal = Mage::getStoreConfig('blog/blog/image_width');

        return $confVal && $confVal >= 0 ? $confVal : 400;
    }

    public function getImageHeight()
    {
        $confVal = Mage::getStoreConfig('blog/blog/image_height');

        return $confVal && $confVal >= 0 ? $confVal : 200;
    }

    public function getImageWidthPost()
    {
        $confVal = Mage::getStoreConfig('blog/blog/image_width_view');

        return $confVal;
    }

    public function getImageHeightPost()
    {
        $confVal = Mage::getStoreConfig('blog/blog/image_height_view');

        return $confVal;
    }
}
