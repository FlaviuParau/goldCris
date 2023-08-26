<?php
/**
 * @package   Blugento_Pdf
 * @copyright 2016 Blugento Team
 */
/**
 * Creditmemo model rewrite.
 *
 * @category  Blugento
 * @package   Blugento_Pdf
 * @author    Blugento Team <team@blugento.com>
 */
class Blugento_Pdf_Model_Engine_Creditmemo_Romania extends Blugento_Pdf_Model_Engine_Invoice_Romania
{
    public $_currentMemo = null;

    /**
     * constructor to set mode to creditmemo
     */
    public function __construct()
    {
        parent::__construct();
        $this->setMode('creditmemo');

        $this->encoding = 'UTF-8';
    }

    /**
     * Return PDF document
     *
     * @param  array $creditmemos creditmemos to generate pdfs for
     *
     * @return Zend_Pdf
     */
    public function getPdf($creditmemos = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('creditmemo');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);

        foreach ($creditmemos as $creditmemo) {
            // pagecounter is 0 at the beginning, because it is incremented in newPage()
            $this->pagecounter = 0;
            if ($creditmemo->getStoreId()) {
                Mage::app()->getLocale()->emulate($creditmemo->getStoreId());
                Mage::app()->setCurrentStore($creditmemo->getStoreId());
            }
            $order = $creditmemo->getOrder();
            $this->setOrder($order);
            $this->_currentMemo  = $creditmemo;
            $this->_currentOrder = $order;

            $page = $this->newPage(array());

            $this->insertAddressesAndHeader($page, $creditmemo, $order);

            $this->_setFontRegular($page, $this->fontSizes['regular']);
            $this->insertTableHeader($page, $creditmemo, $order);
            //$this->_drawHeader($page);

            $this->y -= 20;

            $position = 0;

            foreach ($creditmemo->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }

                /* Draw item */
                /*$position++;
                $this->_drawItem($item, $page, $order, $position);
                $page = end($pdf->pages);*/

                $showFooter = Mage::getStoreConfig('sales_pdf/blugento_pdf/show_footer');
                if ($this->y < 50 || ($showFooter == 1 && $this->y < 100)) {
                    $page = $this->newPage(array());
                }
                $page = $this->_drawItem($item, $page, $order, $position);
            }

            /* add line after items */
            $page->drawLine($this->margin['left'], $this->y + 5, $this->margin['right'], $this->y + 5);

            /* Add totals */
            $page = $this->insertTotals($page, $creditmemo);

            /* add note */
            $page = $this->_insertNote($page, $order, $creditmemo);

            // Add footer
            $this->_addFooter($page, $creditmemo->getStore());

            if ($creditmemo->getStoreId()) {
                Mage::app()->getLocale()->revert();
            }
        }

        $this->_afterGetPdf();

        return $pdf;
    }

    /**
     * Generate new PDF page.
     *
     * @param  array $settings Page settings
     *
     * @return Zend_Pdf_Page
     */
    public function newPage(array $settings = array())
    {
        $pdf = $this->_getPdf();

        $page = $pdf->newPage($this->getPageSize());
        $this->pagecounter++;
        $pdf->pages[] = $page;

        $storeId = $this->_currentMemo ? $this->_currentMemo->getStore() : Mage::app()->getStore();
        $this->insertLogo($page, $storeId);

        $this->y = 692 - $this->_marginTop;
        $this->Ln(12);
        $this->_setFontRegular($page, $this->fontSizes['small']);

        if ($this->_currentMemo && $this->_currentOrder && $this->pagecounter > 1) {
            $this->insertTableHeader($page, $this->_currentMemo, $this->_currentOrder);
            $this->y -= 20;
        }
        $y = $this->y;

        $this->_addFooter($page, Mage::app()->getStore());

        $this->y = $y;

        // set the font because it may not be set
        $this->_setFontRegular($page, $this->fontSizes['small']);

        // provide the possibility to add random stuff to the page
        Mage::dispatchEvent('blugento_pdf_' . $this->getMode() . '_edit_page', array('page' => $page, 'order' => $this->getOrder()));

        return $page;
    }

    /**
     * Draw table header for product items
     *
     * @param  Zend_Pdf_Page $page page to draw on
     *
     * @return void
     */
    protected function _drawHeader(Zend_Pdf_Page $page)
    {
        $page->setFillColor($this->colors['grey1']);
        $page->setLineColor($this->colors['grey1']);
        $page->setLineWidth(1);
        $page->drawRectangle($this->margin['left'], $this->y, $this->margin['right'], $this->y - 15);

        $page->setFillColor($this->colors['black']);
        $font = $this->_setFontRegular($page, 9);

        $this->y -= 11;
        $page->drawText(
            Mage::helper('blugento_pdf')->__('Pos'),
            $this->margin['left'] + 3,
            $this->y,
            $this->encoding
        );
        $page->drawText(
            Mage::helper('blugento_pdf')->__('No.'),
            $this->margin['left'] + 25,
            $this->y,
            $this->encoding
        );
        $page->drawText(
            Mage::helper('blugento_pdf')->__('Description'),
            $this->margin['left'] + 120,
            $this->y,
            $this->encoding
        );

        $singlePrice = Mage::helper('blugento_pdf')->__('Price (excl. tax)');
        $page->drawText(
            $singlePrice,
            $this->margin['right'] - 153 - $this->widthForStringUsingFontSize($singlePrice, $font, 9),
            $this->y,
            $this->encoding
        );

        $page->drawText(
            Mage::helper('blugento_pdf')->__('Qty'),
            $this->margin['left'] + 360,
            $this->y,
            $this->encoding
        );

        $taxLabel = Mage::helper('blugento_pdf')->__('Tax');
        $page->drawText(
            $taxLabel,
            $this->margin['right'] - 65 - $this->widthForStringUsingFontSize($taxLabel, $font, 9),
            $this->y,
            $this->encoding
        );

        $totalLabel = Mage::helper('blugento_pdf')->__('Total');
        $page->drawText(
            $totalLabel,
            $this->margin['right'] - 10 - $this->widthForStringUsingFontSize($totalLabel, $font, 10),
            $this->y,
            $this->encoding
        );
    }

    /**
     * Insert Header
     *
     * @param  Zend_Pdf_Page          &$page    Current page object of Zend_Pdf
     * @param  Mage_Sales_Model_Order $order    Order object
     * @param  object                 $document Document object
     *
     * @return void
     */
    protected function insertHeader(&$page, $order, $document)
    {
        $page->setFillColor($this->colors['black']);

        $font = $this->_setFontBold($page, $this->fontSizes['big']);

        $title = 'Creditmemo No.';
        $title = trim(Mage::helper('blugento_pdf')->__($title) . ' ' . $document->getIncrementId());

        $page->drawText($title,
            ($page->getWidth() - $this->margin['left'] - $this->widthForStringUsingFontSize($title, $font, $this->fontSizes['big'])) / 2,
            $this->y, $this->encoding);
        $this->Ln();

        $title = Mage::helper('blugento_pdf')->__('From') . ' ' . Mage::helper('core')->formatDate($document->getCreatedAtDate(), 'medium', false);
        $page->drawText($title,
            ($page->getWidth() - $this->margin['left'] - $this->widthForStringUsingFontSize($title, $font, $this->fontSizes['big'])) / 2,
            $this->y, $this->encoding);
        $this->Ln();

        if ($order->hasInvoices()) {
            $invIncrementId = '';
            $invDate = '';
            foreach ($order->getInvoiceCollection() as $invoice) {
                $invIncrementId = $invoice->getIncrementId();
                $invDate = Mage::helper('blugento_pdf')->__('From') . ' ' . Mage::helper('core')->formatDate($invoice->getCreatedAtDate(), 'medium', false);
                break;
            }

            if ($invIncrementId != '') {
                $title = 'For Invoice No.';
                $title = trim(Mage::helper('blugento_pdf')->__($title)) . ' ' . $invIncrementId . ' ' . $invDate;

                $font = $this->_setFontRegular($page, $this->fontSizes['large']);

                $page->drawText($title,
                    ($page->getWidth() - $this->margin['left'] - $this->widthForStringUsingFontSize($title, $font, $this->fontSizes['large'])) / 2,
                    $this->y, $this->encoding);
                $this->Ln();
            }

            $this->Ln();
        }
    }

    /**
     * Insert Totals Block
     *
     * @param  object $page   Current page object of Zend_Pdf
     * @param  object $source Fields of footer
     *
     * @return Zend_Pdf_Page
     */
    protected function insertTotals($page, $source)
    {
        $this->y -= 15;

        $order = $source->getOrder();

        $totalTax = 0;
        $shippingTaxRate = 0;
        $shippingTaxAmount = $order->getShippingTaxAmount();

        if ($shippingTaxAmount > 0) {
            $shippingTaxRate = $order->getShippingTaxAmount() * 100 / ($order->getShippingInclTax() - $order->getShippingTaxAmount());
        }

        $groupedTax = array();

        $items['items'] = array();
        foreach ($source->getAllItems() as $item) {
            if ($item->getOrderItem()->getParentItem()) {
                continue;
            }
            $items['items'][] = $item->getOrderItem()->toArray();
        }

        array_push(
            $items['items'], array(
                'row_invoiced'     => $order->getShippingInvoiced(),
                'tax_inc_subtotal' => false,
                'tax_percent'      => $shippingTaxRate,
                'tax_amount'       => $shippingTaxAmount
            )
        );

        foreach ($items['items'] as $item) {
            $_percent = null;
            if (!isset($item['tax_amount'])) {
                $item['tax_amount'] = 0;
            }
            if (!isset($item['row_invoiced'])) {
                $item['row_invoiced'] = 0;
            }
            if (!isset($item['price'])) {
                $item['price'] = 0;
            }
            if (!isset($item['tax_inc_subtotal'])) {
                $item['tax_inc_subtotal'] = 0;
            }
            if (((float)$item['tax_amount'] > 0)
                && ((float)$item['row_invoiced'] > 0)
            ) {
                $_percent = round($item["tax_percent"], 0);
            }
            if (!array_key_exists('tax_inc_subtotal', $item)
                || $item['tax_inc_subtotal']
            ) {
                $totalTax += $item['tax_amount'];
            }
            if (($item['tax_amount']) && $_percent) {
                if (!array_key_exists((int)$_percent, $groupedTax)) {
                    $groupedTax[$_percent] = $item['tax_amount'];
                } else {
                    $groupedTax[$_percent] += $item['tax_amount'];
                }
            }
        }

        $totals = $this->_getTotalsList($source);

        $lineBlock = array(
            'lines'  => array(),
            'height' => 20
        );

        $fontSizeRegular = $this->fontSizes['regular'];

        foreach ($totals as $total) {
            $total->setOrder($order)->setSource($source);

            if ($total->canDisplay()) {
                $total->setFontSize($fontSizeRegular);
                // fix Magento 1.8 bug, so that taxes for shipping do not appear twice
                // see https://github.com/blugento/blugento-pdf/issues/106
                $uniqueTotalsForDisplay = array_map('unserialize', array_unique(array_map('serialize', $total->getTotalsForDisplay())));
                foreach ($uniqueTotalsForDisplay as $totalData) {
                    $label = $this->fixNumberFormat($totalData['label']);
                    if ($label == 'TVA:') {
                        $label = 'Total TVA:';
                    }
                    $font = strpos($label, 'Total ') !== false ? 'bold' : 'regular';
                    $amount = $totalData['amount'];
                    if (strpos($amount, '-') !== false) {
                        $amount = '-(' . $amount . ')';
                    } else {
                        $amount = '-' . $amount;
                    }
                    $lineBlock['lines'][] = array(
                        array(
                            'text'      => $label,
                            'feed'      => $this->margin['right'] - 70,
                            'align'     => 'right',
                            'font_size' => $totalData['font_size'],
                            'font'      => $font
                        ),
                        array(
                            'text'      => $amount,
                            'feed'      => $this->margin['right'],
                            'align'     => 'right',
                            'font_size' => $totalData['font_size'],
                            'font'      => $font
                        ),
                    );
                }
            }
        }

        $page = $this->drawLineBlocks($page, array($lineBlock));

        return $page;
    }

    /**
     * Initialize renderer process
     *
     * @param  string $type renderer type to be initialized
     *
     * @return void
     */
    protected function _initRenderer($type)
    {
        parent::_initRenderer($type);

        $this->_renderers['default'] = array(
            'model'    => 'blugento_pdf/items_ro_creditmemo_default',
            'renderer' => null
        );
        $this->_renderers['grouped'] = array(
            'model'    => 'blugento_pdf/items_grouped',
            'renderer' => null
        );
        $this->_renderers['bundle'] = array(
            'model'    => 'blugento_pdf/items_ro_creditmemo_bundle',
            'renderer' => null
        );
        $this->_renderers['downloadable'] = array(
            'model'    => 'blugento_pdf/items_ro_creditmemo_downloadable',
            'renderer' => null
        );
    }

    /**
     * @return string
     */
    private function getPageSize()
    {
        return Mage::helper('blugento_pdf')->getPageSizeConfigPath();
    }

}
