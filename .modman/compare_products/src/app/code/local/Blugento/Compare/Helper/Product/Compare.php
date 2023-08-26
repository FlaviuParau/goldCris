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
 * @package     Blugento_Compare
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Compare_Helper_Product_Compare extends Mage_Catalog_Helper_Product_Compare
{
    /**
     * Retrieve url for adding product to compare list, return false if is disabled
     *
     * @return  bool
     */
    public function getAddUrl($product)
    {
        /* @var Blugento_Compare_Helper_Data $helper */
        $helper = Mage::helper('blugento_compare');

        if (!$helper->enableCompare()) {
            return false;
        } else {
            if ($this->_logCondition->isVisitorLogEnabled() || $this->_customerSession->isLoggedIn()) {
                return $this->_getUrl('catalog/product_compare/add', $this->_getUrlParams($product));
            }
        }

        return false;
    }
}
