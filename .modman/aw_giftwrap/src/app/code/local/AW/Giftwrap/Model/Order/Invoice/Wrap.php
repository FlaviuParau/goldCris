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


class AW_Giftwrap_Model_Order_Invoice_Wrap extends Mage_Core_Model_Abstract
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('aw_giftwrap/order_invoice_wrap');
    }

    /**
     * @return Mage_Sales_Model_Order_Invoice|null
     */
    public function getInvoiceModel()
    {
        $invoice = Mage::getModel('sales/order_invoice')->load($this->getInvoiceId());
        if (is_null($invoice->getId())) {
            return null;
        }
        return $invoice;
    }

    /**
     * @param Mage_Sales_Model_Order_Invoice $order
     * @return AW_Giftwrap_Model_Order_Invoice_Wrap
     */
    public function loadByInvoice(Mage_Sales_Model_Order_Invoice $invoice)
    {
        if (is_null($invoice->getId())) {
            return $this;
        }
        return $this->load($invoice->getId(), 'invoice_id');
    }
}