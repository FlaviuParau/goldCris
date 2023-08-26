<?php
/**
 * Helper class
 * Class Blugento_Theme_Helper_Data
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
 * @package     Blugento_Category
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Category_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Get image url
     *
     * @param Mage_Catalog_Model_Category $category
     * @param string $code
     * @return string
     */
    public function getImageUrl($category, $code)
    {
        if ($path = $category->getData($code)) {
            return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/category' . '/' . $path;
        }

        return false;
    }
}
