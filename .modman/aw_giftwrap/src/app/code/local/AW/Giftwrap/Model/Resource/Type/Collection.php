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


class AW_Giftwrap_Model_Resource_Type_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('aw_giftwrap/type');
    }

    /**
     * @return AW_Giftwrap_Model_Resource_Type_Collection
     */
    public function addStoreFilter($storeId = null)
    {
        if (!$storeId) {
            $storeId = Mage::app()->getStore()->getId();
        }
        $this->addFieldToFilter(
           'store_ids',
            array(
                array('finset' => $storeId),
                array('finset' => 0)
            )
        );

        return $this;
    }

    /**
     * @return AW_Giftwrap_Model_Resource_Type_Collection
     */
    public function sortBySortOrder()
    {
        $this->setOrder('CAST(sort_order AS SIGNED)', Varien_Data_Collection::SORT_ORDER_ASC);
        return $this;
    }
}