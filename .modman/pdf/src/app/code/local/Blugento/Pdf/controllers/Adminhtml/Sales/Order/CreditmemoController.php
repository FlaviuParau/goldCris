<?php
/**
 * @package   Blugento_Pdf
 * @copyright 2016 Blugento Team
 */

require('Mage/Adminhtml/controllers/Sales/Order/CreditmemoController.php');

/**
 * Class Blugento_Pdf_Adminhtml_Sales_Order_CreditmemoController
 *
 * @category Blugento
 * @package  Blugento_Pdf
 * @author   Blugento Team <team@blugento.com>
 */
class Blugento_Pdf_Adminhtml_Sales_Order_CreditmemoController
    extends Mage_Adminhtml_Sales_Order_CreditmemoController
{

    /**
     * Create pdf for current creditmemo
     */
    public function printAction()
    {
        $this->_initCreditmemo();
        /** @see Mage_Adminhtml_Sales_Order_InvoiceController */
        if ($creditmemoId = $this->getRequest()->getParam('creditmemo_id')) {
            if ($creditmemo = Mage::getModel('sales/order_creditmemo')
                ->load($creditmemoId)
            ) {
                $pdf = Mage::getModel('sales/order_pdf_creditmemo')
                    ->getPdf(array($creditmemo));
                $this->_prepareDownloadResponse(
                    Mage::helper('blugento_pdf')
                        ->getExportFilename('creditmemo', $creditmemo),
                    $pdf->render(), 'application/pdf'
                );
            }
        } else {
            $this->_forward('noRoute');
        }
    }

}
