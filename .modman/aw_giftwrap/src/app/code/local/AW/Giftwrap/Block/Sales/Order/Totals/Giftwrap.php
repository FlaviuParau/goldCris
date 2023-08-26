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


class AW_Giftwrap_Block_Sales_Order_Totals_Giftwrap extends Mage_Core_Block_Template
{
    /**
     * Retrieve order model
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * Retrieve source model (order, invoice, shipment, creditmemo)
     * @return mixed
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Initialize totals with AW_Giftwrap
     *
     * @return AW_Giftwrap_Block_Sales_Order_Totals_Giftwrap
     */
    public function initTotals()
    {
        if ($this->getSource()->getBaseAwGiftwrapAmount()) {
            $this->getParentBlock()->addTotal(
                new Varien_Object(
                    array(
                        'code'   => 'aw_giftwrap',
                        'strong' => false,
                        'label'  => Mage::helper('aw_giftwrap')->__('Gift Wrap'),
                        'value'  => $this->getSource()->getAwGiftwrapAmount(),
                    )
                ),
                'tax'
            );
        }
        return $this;
    }
}