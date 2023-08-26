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

class Blugento_FanCourier_Helper_Data extends Mage_Core_Helper_Abstract
{
    const CACHE_ID = 'blugento_fancourier';

    /**
     * Check if shipping method is enabled
     *
     * @return int
     */
    public function isEnabled()
    {
        return (int) Mage::getStoreConfig('carriers/bgfancourier/active');
    }
}
