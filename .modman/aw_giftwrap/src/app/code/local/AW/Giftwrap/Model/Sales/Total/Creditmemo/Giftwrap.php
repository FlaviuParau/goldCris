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


class AW_Giftwrap_Model_Sales_Total_Creditmemo_Giftwrap extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    /**
     * Collect credit memo subtotal
     *
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return AW_Giftwrap_Model_Sales_Total_Creditmemo_Giftwrap
     */
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $creditmemo->setAwGiftwrapAmount(0);
        $creditmemo->setBaseAwGiftwrapAmount(0);
        $orderAwGiftwrapAmount        = $creditmemo->getOrder()->getAwGiftwrapAmount();
        $baseOrderAwGiftwrapAmount    = $creditmemo->getOrder()->getBaseAwGiftwrapAmount();
        if ($baseOrderAwGiftwrapAmount) {
            $subtotalToRefund = floatval($creditmemo->getBaseSubtotal());
            $subtotalRefunded = floatval($creditmemo->getOrder()->getBaseSubtotalRefunded());
            $fullSubtotal = floatval($creditmemo->getOrder()->getBaseSubtotal());
            if ($fullSubtotal > ($subtotalToRefund + $subtotalRefunded)) {
                return $this;
            }
            $creditmemo->setAwGiftwrapAmount($orderAwGiftwrapAmount);
            $creditmemo->setBaseAwGiftwrapAmount($baseOrderAwGiftwrapAmount);

            $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $orderAwGiftwrapAmount);
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseOrderAwGiftwrapAmount);
        }
        return $this;
    }
}