<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Giftwrap
 * @version    1.1.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Giftwrap_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * @param string $version
     * @return bool
     */
    public function isMageVersionLessOrEqualThan($version = '1.4.1.1')
    {
        return version_compare(Mage::getVersion(), $version, '<=');
    }

    public function isValidProduct(Mage_Catalog_Model_Product $product)
    {
        if ($product->isVirtual()) {
            return false;
        }
        if ($product->isGrouped()) {
            return false;
        }

        $excludeProductRule = Mage::helper('aw_giftwrap/config')->getExcludeProductsRule();
        if (!$excludeProductRule->getConditions()->getConditions()) {
            return true;
        }
        $productCollection = Mage::getResourceModel('catalog/product_collection');
        $productCollection->addWebsiteFilter(Mage::app()->getWebsite());
        $excludeProductRule->getConditions()->collectValidatedAttributes($productCollection);
        if ($excludeProductRule->validate($product)) {
            return false;
        }
        return true;
    }
}