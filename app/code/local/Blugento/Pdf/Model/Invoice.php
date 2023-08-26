<?php
/**
 * @package   Blugento_Pdf
 * @copyright 2016 Blugento Team
 */
/**
 * Invoice model rewrite.
 *
 * The invoice model serves as a proxy to the actual PDF engine as set via
 * backend configuration.
 *
 * @category  Blugento
 * @package   Blugento_Pdf
 * @author    Blugento Team <team@blugento.com>
 */
class Blugento_Pdf_Model_Invoice extends Mage_Sales_Model_Order_Pdf_Invoice
{
    /**
     * The actual PDF engine responsible for rendering the file.
     *
     * @var Mage_Sales_Model_Order_Pdf_Abstract
     */
    private $_engine;

    /**
     * get pdf rendering engine
     *
     * @return Mage_Sales_Model_Order_Pdf_Abstract|Mage_Sales_Model_Order_Pdf_Invoice
     */
    protected function getEngine()
    {
        if (!$this->_engine) {
            $modelClass = Mage::getStoreConfig('sales_pdf/invoice/engine');
            $engine = Mage::getModel($modelClass);

            if (!$engine) {
                // Fallback to Magento standard invoice layout.
                $engine = new Mage_Sales_Model_Order_Pdf_Invoice();
            }

            $this->_engine = $engine;
        }

        return $this->_engine;
    }

    /**
     * get pdf for invoices
     *
     * @param  array|Varien_Data_Collection $invoices invoices to render pdfs for
     *
     * @return mixed
     */
    public function getPdf($invoices = array())
    {
        if (Mage::helper('core')->isModuleEnabled('Blugento_ErpIntegration')
            && Mage::getStoreConfig('blugento_erpintegration/general/send_pdf')
        ) {
            foreach ($invoices as $invoice) {
                $order = $invoice->getOrder();
                $url = $order->getInvoiceDownloadUrl();
                if ($url) {
                    return file_get_contents($url);
                }
            }
        }

        return $this->getEngine()->getPdf($invoices);
    }
}
