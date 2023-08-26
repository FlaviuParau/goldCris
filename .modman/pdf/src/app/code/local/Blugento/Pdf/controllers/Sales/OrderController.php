<?php
/**
 * @package   Blugento_Pdf
 * @copyright 2016 Blugento Team
 */

require_once 'Mage/Sales/controllers/OrderController.php';

/**
 * Sales orders controller
 *
 * @category  Blugento
 * @package   Blugento_Pdf
 * @author    Blugento Team <team@blugento.com>
 */
class Blugento_Pdf_Sales_OrderController extends Mage_Sales_OrderController
{
    protected $_types
        = array(
            'invoice', 'creditmemo', 'shipment'
        );

    /**
     * Print PDF Invoice Action
     *
     * it changes the standard action with html output to pdf output
     *
     * @return void
     */
    public function printInvoiceAction()
    {
        $this->printDocument('invoice');
    }

    /**
     * Print PDF Creditmemo action
     *
     * it changes the standard action with html output to pdf output
     *
     * @return void
     */

    public function printCreditmemoAction()
    {
        $this->printDocument('creditmemo');
    }

    /**
     * Print PDF Shipment Action
     *
     * it changes the standard action with html output to pdf output
     *
     * @return void
     */
    public function printShipmentAction()
    {
        $this->printDocument('shipment');
    }

    /**
     * Create invoice, creditmemo or shipment pdf
     *
     * @param string $type which document should be created? invoice, creditmemo or shipment
     */
    public function printDocument($type)
    {
        if (!in_array($type, $this->_types)) {
            Mage::throwException('Type not found in type table.');
        }
        /* @var $order Mage_Sales_Model_Order */
        $documentId = (int)$this->getRequest()->getParam($type . '_id');
        $document = null;
        if ($documentId) {
            /* @var $document Mage_Sales_Model_Abstract */
            $document = Mage::getModel('sales/order_' . $type);
            $document->load($documentId);
            $order = $document->getOrder();
        } else {
            $orderId = (int)$this->getRequest()->getParam('order_id');
            $order = Mage::getModel('sales/order')->load($orderId);
        }

        if ($this->_canViewOrder($order)) {
            if (isset($orderId)) {
                // Create a pdf file from all $type s of requested order.
                /* @var $documentsCollection Mage_Sales_Model_Resource_Order_Collection_Abstract */
                $documentsCollection = Mage::getResourceModel('sales/order_' . $type . '_collection');
                $documentsCollection
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('order_id', $orderId)
                    ->load();
                if (count($documentsCollection) == 1) {
                    $filename = Mage::helper('blugento_pdf')->getExportFilename($type, $documentsCollection->getFirstItem());
                } else {
                    $filename = Mage::helper('blugento_pdf')->getExportFilenameForMultipleDocuments($type);
                }
            } else {
                // Create a single $type pdf.
                $documentsCollection = array($document);
                $filename = Mage::helper('blugento_pdf')->getExportFilename($type, $document);
            }

            // Store current area and set to adminhtml for $type generation.
            $currentArea = Mage::getDesign()->getArea();
            Mage::getDesign()->setArea('adminhtml');

            /* @var $pdfGenerator Mage_Sales_Model_Order_Pdf_Abstract */
            $pdfGenerator = Mage::getModel('sales/order_pdf_' . $type);
            $pdf = $pdfGenerator->getPdf($documentsCollection);
            $this->_prepareDownloadResponse(
                $filename, $pdf->render(), 'application/pdf'
            );

            // Restore area.
            Mage::getDesign()->setArea($currentArea);

        } else {
            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                $this->_redirect('*/*/history');
            } else {
                $this->_redirect('sales/guest/form');
            }
        }
    }

    /**
     * Declare headers and content file in response for file download
     *
     * @param string $fileName
     * @param string|array $content set to null to avoid starting output, $contentLength should be set explicitly in
     *                              that case
     * @param string $contentType
     * @param int $contentLength    explicit content length, if strlen($content) isn't applicable
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _prepareDownloadResponse($fileName, $content, $contentType = 'application/octet-stream',
                                                $contentLength = null
    ) {
        $session = Mage::getSingleton('admin/session');
        if ($session->isFirstPageAfterLogin()) {
            $this->_redirect($session->getUser()->getStartupPageUrl());
            return $this;
        }

        $isFile = false;
        $file   = null;
        if (is_array($content)) {
            if (!isset($content['type']) || !isset($content['value'])) {
                return $this;
            }
            if ($content['type'] == 'filename') {
                $isFile         = true;
                $file           = $content['value'];
                $contentLength  = filesize($file);
            }
        }

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true)
            ->setHeader('Content-Length', is_null($contentLength) ? strlen($content) : $contentLength)
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setHeader('Last-Modified', date('r'));

        if (!is_null($content)) {
            if ($isFile) {
                $this->getResponse()->clearBody();
                $this->getResponse()->sendHeaders();

                $ioAdapter = new Varien_Io_File();
                if (!$ioAdapter->fileExists($file)) {
                    Mage::throwException(Mage::helper('core')->__('File not found'));
                }
                $ioAdapter->open(array('path' => $ioAdapter->dirname($file)));
                $ioAdapter->streamOpen($file, 'r');
                while ($buffer = $ioAdapter->streamRead()) {
                    print $buffer;
                }
                $ioAdapter->streamClose();
                if (!empty($content['rm'])) {
                    $ioAdapter->rm($file);
                }

                exit(0);
            } else {
                $this->getResponse()->clearBody();
                $this->getResponse()->sendHeaders();
                //$this->getResponse()->setBody($content);
                print $content;

                exit(0);
            }
        }

        return $this;
    }
}
