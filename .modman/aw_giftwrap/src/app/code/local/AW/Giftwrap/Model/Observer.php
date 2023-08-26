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


class AW_Giftwrap_Model_Observer
{

    /**
     * Call before place order
     * @event sales_model_service_quote_submit_before
     * @see Mage_Sales_Model_Service_Quote->submitOrder
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function salesModelServiceQuoteSubmitBefore($observer)
    {
        Mage::getModel('aw_giftwrap/observer_totals')->addGiftWrapTotalsToOrder($observer);
        return $this;
    }

    /**
     * Call after place order
     * @event sales_model_service_quote_submit_after
     * @see Mage_Sales_Model_Service_Quote->submitOrder
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function salesModelServiceQuoteSubmitAfter($observer)
    {
        Mage::getModel('aw_giftwrap/observer_checkout')->saveOrderWrap($observer);
        return $this;
    }

    /**
     * Call after save invoice model
     * @event sales_order_invoice_save_after
     * @see Mage_Sales_Model_Order_Invoice
     * @see Mage_Core_Model_Abstract->_afterSave
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function salesOrderInvoiceSaveAfter($observer)
    {
        Mage::getModel('aw_giftwrap/observer_totals')->saveInvoiceTotal($observer);
        return $this;
    }

    /**
     * Call after save creditmemo model
     * @event sales_order_creditmemo_save_after
     * @see Mage_Sales_Model_Order_Creditmemo
     * @see Mage_Core_Model_Abstract->_afterSave
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function salesOrderCreditmemoSaveAfter($observer)
    {
        Mage::getModel('aw_giftwrap/observer_totals')->saveCreditmemoTotal($observer);
        return $this;
    }

    /**
     * Call after load order
     * @event sales_order_load_after
     * @see Mage_Sales_Model_Order
     * @see Mage_Core_Model_Abstract->_afterLoad
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function salesOrderLoadAfter($observer)
    {
        Mage::getModel('aw_giftwrap/observer_totals')->loadWrapToOrder($observer);
        return $this;
    }

    /**
     * Call after load invoice
     * @event sales_order_invoice_load_after
     * @see Mage_Sales_Model_Order_Invoice
     * @see Mage_Core_Model_Abstract->_afterLoad
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function salesOrderInvoiceLoadAfter($observer)
    {
        Mage::getModel('aw_giftwrap/observer_totals')->loadWrapToInvoice($observer);
        return $this;
    }

    /**
     * Call after load creditmemo
     * @event sales_order_creditmemo_load_after
     * @see Mage_Sales_Model_Order_Creditmemo
     * @see Mage_Core_Model_Abstract->_afterLoad
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function salesOrderCreditmemoLoadAfter($observer)
    {
        Mage::getModel('aw_giftwrap/observer_totals')->loadWrapToCreditmemo($observer);
        return $this;
    }

    /**
     * Call after success apply shipping method information on onepage and OSC by AW
     * @event checkout_controller_onepage_save_shipping_method
     * @see Mage_Checkout_OnepageController->saveShippingMethodAction
     * @see AW_Onestepcheckout_AjaxController ->saveShippingMethodAction
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function checkoutControllerOnepageSaveShippingMethod($observer)
    {
        Mage::getModel('aw_giftwrap/observer_checkout')->saveGiftWrapDataToSession($observer);
        return $this;
    }

    /**
     * Call after success order save
     * @event checkout_type_onepage_save_order_after
     * @see Mage_Checkout_Model_Type_Onepage->saveOrder
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function checkoutTypeOnepageSaveOrderAfter($observer)
    {
        Mage::getModel('aw_giftwrap/observer_checkout')->saveOrderToRegistry($observer);
        return $this;
    }

    /**
     * Call on save System->Configuration->Gift Wrap section
     * @event admin_system_config_changed_section_aw_giftwrap
     * @see Mage_Adminhtml_System_ConfigController->saveAction
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function adminSystemConfigSectionChanged($observer)
    {
        Mage::getModel('aw_giftwrap/observer_type')->saveTypeOnConfigSectionChange($observer);
        return $this;
    }

    /**
     * Call on click "Flush Catalog Images Cache" button in "System"->"Cache Management"
     * @event clean_catalog_images_cache_after
     * @see Mage_Adminhtml_CacheController ->cleanImagesAction
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function cleanCatalogImagesCacheAfter($observer)
    {
        Mage::getModel('aw_giftwrap/observer_cache')->cleanImageCache($observer);
        return $this;
    }

    /**
     * Call on collect totals for Paypal Nvp
     * @event paypal_prepare_line_items
     * @see Mage_Paypal_Model_Cart->_render
     * @see Mage_Paypal_Helper_Data->prepareLineItems
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function paypalPrepareLineItems($observer)
    {
        Mage::getModel('aw_giftwrap/observer_totals')->addTotalToPaypal($observer);
        return $this;
    }
}