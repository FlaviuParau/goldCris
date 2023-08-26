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


class AW_Giftwrap_Model_Sales_Total_Invoice_Giftwrap extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * Collect invoice subtotal
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return Mage_Sales_Model_Order_Invoice_Total_Abstract
     */
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $invoice->setAwGiftwrapAmount(0);
        $invoice->setBaseAwGiftwrapAmount(0);
        $orderAwGiftwrapAmount        = $invoice->getOrder()->getAwGiftwrapAmount();
        $baseOrderAwGiftwrapAmount    = $invoice->getOrder()->getBaseAwGiftwrapAmount();
        if ($baseOrderAwGiftwrapAmount) {
            /**
             * If invoice is first in order
             */
            foreach ($invoice->getOrder()->getInvoiceCollection() as $previousInvoice) {
                if ($previousInvoice->getBaseAwGiftwrapAmount() && !$previousInvoice->isCanceled()) {
                    return $this;
                }
            }
            $invoice->setAwGiftwrapAmount($orderAwGiftwrapAmount);
            $invoice->setBaseAwGiftwrapAmount($baseOrderAwGiftwrapAmount);

            $invoice->setGrandTotal($invoice->getGrandTotal() + $orderAwGiftwrapAmount);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseOrderAwGiftwrapAmount);
        }
        return $this;
    }
}