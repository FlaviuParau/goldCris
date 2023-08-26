<?php
/**
 * @package   Blugento_Pdf
 * @copyright 2016 Blugento Team
 */

require('Mage/Adminhtml/controllers/Sales/Order/InvoiceController.php');

/**
 * Class Blugento_Pdf_Adminhtml_Sales_Order_InvoiceController
 *
 * @category Blugento
 * @package  Blugento_Pdf
 * @author   Blugento Team <team@blugento.com>
 */
class Blugento_Pdf_Adminhtml_Sales_Order_InvoiceController
    extends Mage_Adminhtml_Sales_Order_InvoiceController
{

    /**
     * Create pdf for current invoice
     */
    public function printAction()
    {
        $this->_initInvoice();
        if ($invoiceId = $this->getRequest()->getParam('invoice_id')) {
            if ($invoice = Mage::getModel('sales/order_invoice')
                ->load($invoiceId)
            ) {
                $pdf = Mage::getModel('sales/order_pdf_invoice')
                    ->getPdf(array($invoice));
                $this->_prepareDownloadResponse(
                    Mage::helper('blugento_pdf')
                        ->getExportFilename('invoice', $invoice),
                    $pdf->render(), 'application/pdf'
                );
            }
        } else {
            $this->_forward('noRoute');
        }
    }

}
