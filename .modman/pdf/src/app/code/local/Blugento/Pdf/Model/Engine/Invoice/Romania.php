<?php
/**
 * @package   Blugento_Pdf
 * @copyright 2016 Blugento Team
 */
/**
 * Default invoice rendering engine.
 *
 * @category  Blugento
 * @package   Blugento_Pdf
 * @author    Blugento Team <team@blugento.com>
 */
class Blugento_Pdf_Model_Engine_Invoice_Romania extends Blugento_Pdf_Model_Engine_Abstract
{
    public $fontSizes = array(
        'tiny'    => 7,
        'small'   => 8,
        'regular' => 9,
        'large'   => 11,
        'big'     => 13
    );

    public $companyBlockSize = 250;

    public $_currentOrder = null;
    public $_currentInvoice = null;

    /**
     * constructor to set mode to invoice
     */
    public function __construct()
    {
        parent::__construct();
        $this->setMode('invoice');

        $this->encoding = 'UTF-8';
    }

    /**
     * Return PDF document
     *
     * @param  array $invoices invoices to render pdfs for
     *
     * @return Zend_Pdf
     */
    public function getPdf($invoices = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);

        foreach ($invoices as $invoice) {
            // pagecounter is 0 at the beginning, because it is incremented in newPage()
            $this->pagecounter = 0;
            if ($invoice->getStoreId()) {
                Mage::app()->getLocale()->emulate($invoice->getStoreId());
                Mage::app()->setCurrentStore($invoice->getStoreId());
            }
            $order = $invoice->getOrder();
            $this->setOrder($order);
            $this->_currentInvoice = $invoice;
            $this->_currentOrder = $order;

            $page = $this->newPage(array());

            $this->insertAddressesAndHeader($page, $invoice, $order);

            $this->_setFontRegular($page, $this->fontSizes['regular']);
            $this->insertTableHeader($page, $invoice, $order);

            $this->y -= 20;

            $position = 0;

            foreach ($invoice->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }

                $showFooter = Mage::getStoreConfig('sales_pdf/blugento_pdf/show_footer');
                if ($this->y < 50 || ($showFooter == 1 && $this->y < 100)) {
                    $page = $this->newPage(array());
                }

                $position++;
                $page = $this->_drawItem($item, $page, $order, $position);
            }

            /* add line after items */
            $page->drawLine($this->margin['left'], $this->y + 5, $this->margin['right'], $this->y + 5);

            /* add totals */
            $page = $this->insertTotals($page, $invoice);

            /* add note */
            $page = $this->_insertNote($page, $order, $invoice);

            // Add footer
            $this->_addFooter($page, $invoice->getStore());

            if ($invoice->getStoreId()) {
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

        $storeId = $this->_currentInvoice ? $this->_currentInvoice->getStore() : Mage::app()->getStore();
        $this->insertLogo($page, $storeId);

        $this->y = 692 - $this->_marginTop;
        $this->Ln(12);
        $this->_setFontRegular($page, $this->fontSizes['small']);

        if ($this->_currentInvoice && $this->_currentOrder && $this->pagecounter > 1) {
            $this->insertTableHeader($page, $this->_currentInvoice, $this->_currentOrder);
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
     * @return string
     */
    private function getPageSize()
    {
        return Mage::helper('blugento_pdf')->getPageSizeConfigPath();
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
                    $lineBlock['lines'][] = array(
                        array(
                            'text'      => $label,
                            'feed'      => $this->margin['right'] - 70,
                            'align'     => 'right',
                            'font_size' => $totalData['font_size'],
                            'font'      => $font
                        ),
                        array(
                            'text'      => $totalData['amount'],
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
     * draw footer on pdf
     *
     * @param Zend_Pdf_Page &$page page to draw on
     * @param mixed         $store store to get infos from
     */
    protected function _addFooter(&$page, $store = null)
    {
        if (!Mage::getStoreConfig('sales_pdf/blugento_pdf/show_footer')) {
            return false;
        }

        $footerText = trim(strip_tags(Mage::getStoreConfig('sales_pdf/blugento_pdf/footer_text')));
        if ($footerText) {
            $this->y = 110;
            $fontSize = $this->fontSizes['small'];
            $font = $this->_setFontRegular($page, $fontSize);
            $this->_insertFooterText($page, $footerText, $font, $fontSize);

            // Add page counter.
            $this->y = 110;
            $this->_insertPageCounter($page);
            return;
        }

        // get the imprint of the store if a store is set
        if (!empty($store)) {
            $imprintObject = new Varien_Object();
            $imprintObject->setImprint(Mage::getStoreConfig('blugentolocalizer/imprint',
                $store));
            Mage::dispatchEvent('blugento_pdf_imprint_load_after', array(
                    'transport_object' => $imprintObject
                )
            );
            $this->_imprint = $imprintObject->getImprint();
        }

        // Add footer if imprint is set.
        if ($this->_imprint) {
            $this->y = 110;
            $this->_insertFooter($page);

            // Add page counter.
            $this->y = 110;
            $this->_insertPageCounter($page);
        }
    }

    /**
     * insert customer address and all header like customer number, etc.
     *
     * @param Zend_Pdf_Page             $page   current Zend_Pdf_Page
     * @param Mage_Sales_Model_Abstract $source source for the address information
     * @param Mage_Sales_Model_Order    $order  order to print the document for
     */
    protected function insertAddressesAndHeader(
        Zend_Pdf_Page $page,
        Mage_Sales_Model_Abstract $source,
        Mage_Sales_Model_Order $order
    ) {
        // Add logo
        $this->insertLogo($page, $source->getStore());

        // Add company address
        $this->y = 692 - $this->_marginTop;
        $this->_insertCompanyBlock($page);
        $this->Ln(12);

        // Add customer
        $this->y = 692 - $this->_marginTop;
        $this->_insertCustomerAddress($page, $order);

        // Add head
        if ($this->y > 520 - $this->_marginTop) {
            $this->y = 520 - $this->_marginTop;
        }
        $this->insertHeader($page, $order, $source);

        /* Add table head */
        // make sure that item table does not overlap heading
        if ($this->y > 580 - $this->_marginTop) {
            $this->y = 580 - $this->_marginTop;
        }
    }

    /**
     * Insert Table Header for Items
     *
     * @param  Zend_Pdf_Page &$page current page object of Zend_PDF
     *
     * @return void
     */
    protected function insertTableHeader(&$page, $invoice, $order)
    {
        $page->setFillColor($this->colors['grey1']);
        $page->setLineColor($this->colors['grey1']);
        $page->setLineWidth(1);
        $page->drawRectangle($this->margin['left'], $this->y, $this->margin['right'] + 3, $this->y - 40);

        $page->setFillColor($this->colors['black']);
        $font = $this->_setFontRegular($page, $this->fontSizes['regular']);

        $this->y -= 11;
        $arr = explode('<br/>', Mage::helper('blugento_pdf')->__('Pos'));
        $y = $this->y;
        foreach ($arr as $tmpVal) {
            $page->drawText($tmpVal, $this->margin['left'] + 3, $y, $this->encoding);
            $y -= 12;
        }
        $page->drawText(Mage::helper('blugento_pdf')->__('Description'), $this->margin['left'] + 35, $this->y, $this->encoding);

        $columns = array();
        $columns['price'] = array(
            'label'  => Mage::helper('blugento_pdf')->__('Unit Price'),
            '_width' => 60,
            'currency' => true
        );
        $columns['price_incl_tax'] = array(
            'label' => Mage::helper('blugento_pdf')->__('Unit Price (incl. tax)'),
            '_width' => 60,
            'currency' => true
        );
        $columns['price_excl_tax'] = array(
            'label' => Mage::helper('blugento_pdf')->__('Unit Price (excl. tax)'),
            '_width' => 60,
            'currency' => true
        );
        $columns['subtotal_incl_tax'] = array(
            'label'  => Mage::helper('blugento_pdf')->__('Subtotal (incl. tax)_'),
            '_width' => 70,
            'currency' => true
        );
        $columns['subtotal_excl_tax'] = array(
            'label'  => Mage::helper('blugento_pdf')->__('Subtotal (excl. tax)_'),
            '_width' => 65,
            'currency' => true
        );
        $columns['subtotal'] = array(
            'label'  => Mage::helper('blugento_pdf')->__('Value'),
            '_width' => 50,
            'currency' => true
        );
        $columns['measuring_unit'] = array(
            'label'  => Mage::helper('blugento_pdf')->__('UM'),
            '_width' => 30,
            'currency' => false
        );
        $columns['old_price'] = array(
            'label'  => Mage::helper('blugento_pdf')->__('Old Price'),
            '_width' => 60
        );
        $columns['qty'] = array(
            'label'  => Mage::helper('blugento_pdf')->__('Qty'),
            '_width' => 30,
            'currency' => false
        );
        $columns['tax'] = array(
            'label'  => Mage::helper('blugento_pdf')->__('Tax'),
            '_width' => 40,
            'currency' => true
        );
        $columns['tax_rate'] = array(
            'label'  => Mage::helper('blugento_pdf')->__('Tax rate %'),
            '_width' => 50,
            'currency' => false
        );
        $columns['sku'] = array(
            'label'  => Mage::helper('blugento_pdf')->__('No.'),
            '_width' => 30,
            'currency' => false
        );
        // draw price, tax, and subtotal in specified order
        $columnsOrder = explode(',', Mage::getStoreConfig('sales_pdf/invoice/item_price_column_order'));
        // draw starting from right
        $columnsOrder = array_reverse($columnsOrder);
        $columnOffset = 18;
        $currency = '- ' . $order->getOrderCurrencyCode() . ' -';
        //$currency = '- ' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol() . ' -';
        $i = 0;
        foreach ($columnsOrder as $columnName) {
            $columnName = trim($columnName);
            if (array_key_exists($columnName, $columns)) {
                $column = $columns[$columnName];

                $arr = explode('<br/>', $column['label']);
                $y = $this->y;

                foreach ($arr as $tmpVal) {
                    $labelWidth = $this->widthForStringUsingFontSize($tmpVal, $font, $this->fontSizes['regular']);
                    $page->drawText($tmpVal, $this->margin['right'] - $columnOffset - $labelWidth / 2 - 1, $y, $this->encoding);
                    $y -= 12;
                }

                if ($column['currency']) {
                    $labelWidth = $this->widthForStringUsingFontSize($currency, $font, $this->fontSizes['regular']);
                    $page->drawText($currency, $this->margin['right'] - $columnOffset - $labelWidth / 2 - 1, $y, $this->encoding);
                    $y -= 12;
                }

                $columnOffset += $column['_width'];
            }
        }

        $this->y -= 24;
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
            'model'    => 'blugento_pdf/items_ro_default',
            'renderer' => null
        );
        $this->_renderers['grouped'] = array(
            'model'    => 'blugento_pdf/items_grouped',
            'renderer' => null
        );
        $this->_renderers['bundle'] = array(
            'model'    => 'blugento_pdf/items_ro_bundle',
            'renderer' => null
        );
        $this->_renderers['downloadable'] = array(
            'model'    => 'blugento_pdf/items_ro_downloadable',
            'renderer' => null
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

        $mode = $this->getMode();
        if ($mode == 'invoice') {
            $customTitle = Mage::getStoreConfig('sales_pdf/invoice/title');
            $title = 'Invoice No.';
            if ($customTitle) {
                $title = $customTitle . ' nr.';
            }

        } elseif ($mode == 'shipment') {
            $title = 'Packingslip No.';
        } else {
            $title = 'Creditmemo No.';
        }
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

    }

    /**
     * Insert address of store owner
     *
     * @param  Zend_Pdf_Page &$page Current page object of Zend_Pdf
     * @param  mixed $store store to get info from
     *
     * @return void
     */
    protected function _insertCompanyBlock(&$page)
    {
        $fontSize = $this->fontSizes['big'];
        $font = $this->_setFontBold($page, $fontSize);

        $page->drawText(Mage::helper('blugento_pdf')->__('Provider'), $this->margin['left'], $this->y, $this->encoding);
        $this->Ln();

        $page->drawText(trim(strip_tags($this->_imprint['company_first'])), $this->margin['left'], $this->y, $this->encoding);
        $this->Ln();

        $fontSize = $this->fontSizes['large'];

        $fields = array(
            'vat_id'                => Mage::helper('blugento_pdf')->__('CUI / CIF:'),
            'register_number'       => Mage::helper('blugento_pdf')->__('Register number:'),
            'address'               => Mage::helper('blugento_pdf')->__('Address:'),
            'iban'                  => Mage::helper('blugento_pdf')->__('IBAN:'),
            'bank_name'             => Mage::helper('blugento_pdf')->__('Bank:'),
            'collectible_vat'       => ''
        );

        $this->_insertHeaderBlock($page, $fields, 0, $this->_getMaxWidthFields($fields, $font, $this->fontSizes['regular']) + 30, $this->companyBlockSize, $fontSize);
    }

    /**
     * Inserts the customer address. The default address is the billing address.
     *
     * @param  Zend_Pdf_Page          &$page Current page object of Zend_Pdf
     * @param  Mage_Sales_Model_Order $order Order object
     *
     * @return void
     */
    protected function _insertCustomerAddress(&$page, $order)
    {
        $this->_setFontBold($page, $this->fontSizes['big']);

        $marginLeft = $this->margin['left'] + $this->getHeaderblockOffset() + $this->companyBlockSize + 50;

        $page->drawText(Mage::helper('blugento_pdf')->__('Client'), $marginLeft, $this->y, $this->encoding);
        $this->Ln();

        $this->_setFontRegular($page, $this->fontSizes['large']);
        $billing = $this->_formatAddress($order->getBillingAddress()->format('pdf'));
        $first = true;
        foreach ($billing as $line) {
            if ($first) {
                $first = false;
                $this->_setFontBold($page, $this->fontSizes['big']);
            } else {
                $this->_setFontRegular($page, $this->fontSizes['large']);
            }
            $page->drawText(trim(strip_tags($line)), $marginLeft, $this->y, $this->encoding);
            $this->Ln(14);
        }
    }

    /**
     * Insert footer block
     *
     * @param  Zend_Pdf_Page &$page       Current page object of Zend_Pdf
     * @param  array         $fields      Fields of footer
     * @param  int           $colposition Starting colposition
     * @param  int           $valadjust   Margin between label and value
     * @param  int           $colwidth    the width of this footer block - text will be wrapped if it is broader
     *                                    than this width
     *
     * @return void
     */
    protected function _insertHeaderBlock(&$page, $fields, $colposition = 0, $valadjust = 30, $colwidth = null, $fontSize = null) {
        if (!$fontSize) {
            $fontSize = $this->fontSizes['regular'];
        }
        $font = $this->_setFontRegular($page, $fontSize);
        $y = $this->y;

        $valposition = $colposition + $valadjust;

        if (is_array($fields)) {
            foreach ($fields as $field => $label) {
                if ($field != 'address' && $field != 'collectible_vat' && empty($this->_imprint[$field])) {
                    continue;
                }
                if ($field == 'collectible_vat') {
                    // draw the label
                    $page->drawText($label, $this->margin['left'] + $colposition, $y, $this->encoding);

                    if (Mage::getStoreConfig('sales_pdf/invoice/show_collectible_vat')) {
                        $page->drawText(Mage::helper('blugento_pdf')->__('Collectible VAT'), $this->margin['left'] + $colposition, $y, $this->encoding);
                    }
                    continue;
                }
                // draw the label
                $page->drawText($label, $this->margin['left'] + $colposition, $y, $this->encoding);
                // prepare the value: wrap it if necessary
                $val = $field == 'address' ? $this->_getCompanyAddressLine() : $this->_imprint[$field];
                $width = $colwidth;
                if (!empty($colwidth)) {
                    // calculate the maximum width for the value
                    $width = $this->margin['left'] + $colposition + $colwidth - ($this->margin['left'] + $valposition);
                }
                foreach ($this->_prepareText($val, $page, $font, $fontSize, $width) as $tmpVal) {
                    $page->drawText($tmpVal, $this->margin['left'] + $valposition, $y, $this->encoding);
                    $y -= 14;
                }
            }
        }
    }

    /**
     * Insert footer text
     *
     * @param  Zend_Pdf_Page &$page Current page object of Zend_Pdf
     *
     * @return void
     */
    protected function _insertFooterText(&$page, $text, $font, $fontSize)
    {
        $page->setLineColor($this->colors['black']);
        $page->setLineWidth(0.5);
        $page->drawLine($this->margin['left'] - 20, $this->y - 5, $this->margin['right'] + 30, $this->y - 5);

        $this->Ln(15);
        $notes = explode("\n", $text);

        // Draw footer text on PDF.
        foreach ($notes as $note) {
            // prepare the text so that it fits to the paper
            foreach ($this->_prepareText($note, $page, $font, $fontSize) as $tmpNote) {
                $page->drawText($tmpNote, $this->margin['left'], $this->y - 5, $this->encoding);
                $this->Ln(12);
            }
        }

        return $page;
    }

    /**
     * Insert footer
     *
     * @param  Zend_Pdf_Page &$page Current page object of Zend_Pdf
     *
     * @return void
     */
    protected function _insertFooter(&$page)
    {
        $page->setLineColor($this->colors['black']);
        $page->setLineWidth(0.5);
        $page->drawLine($this->margin['left'] - 20, $this->y - 5, $this->margin['right'] + 30, $this->y - 5);
        $this->Ln(15);

        $fontSize = $this->fontSizes['small'];
        $this->_insertFooterAddress($page, null, $fontSize);

        $fields = array(
            'telephone'         => Mage::helper('blugento_pdf')->__('Telephone:'),
            'fax'               => Mage::helper('blugento_pdf')->__('Fax:'),
            'email'             => Mage::helper('blugento_pdf')->__('E-Mail:'),
            'web'               => Mage::helper('blugento_pdf')->__('Web:'),
            'bank_name'         => Mage::helper('blugento_pdf')->__('Bank name:'),
            'bank_account'      => Mage::helper('blugento_pdf')->__('Account:'),
            'iban'              => Mage::helper('blugento_pdf')->__('IBAN:'),
            'vat_id'            => Mage::helper('blugento_pdf')->__('VAT-ID:'),
            'register_number'   => Mage::helper('blugento_pdf')->__('Register number:'),
        );

        $cleanFields = array();
        foreach($fields as $key => $value) {
            if (isset($this->_imprint[$key]) && $this->_imprint[$key]) {
                $cleanFields[$key] = $value;
            }
        }

        $cleanFields = array_chunk($cleanFields, 5, true);
        $font = $this->_setFontRegular($page, $fontSize);
        $colPosition = 110;
        $colSpace = $this->_getMaxWidthFields($fields, $font, $fontSize) + 5;
        $colWidth = 190;
        foreach ($cleanFields as $i => $chunk) {
            if ($i>0) {
                $colPosition += $colWidth + 20;
                //$colSpace += 5;
            }
            if ($i >= 2) {
                break;
            }
            $this->_insertFooterBlock($page, $chunk, $colPosition, $colSpace, 190, $fontSize);
        }
    }

    /**
     * Insert address of store owner
     *
     * @param  Zend_Pdf_Page &$page Current page object of Zend_Pdf
     * @param  mixed         $store store to get info from
     *
     * @return void
     */
    protected function _insertFooterAddress(&$page, $store = null, $fontSize = null)
    {
        if ($fontSize === null) {
            $fontSize = $this->fontSizes['regular'];
        }
        $font = $this->_setFontRegular($page, $fontSize);
        $y = $this->y;
        $address = '';

        foreach ($this->_prepareText($this->_imprint['company_first'], $page, $font, $fontSize, 90) as $companyFirst) {
            $address .= $companyFirst . "\n";
        }

        if (array_key_exists('company_second', $this->_imprint)) {
            foreach ($this->_prepareText($this->_imprint['company_second'], $page, $font, $fontSize, 90) as $companySecond) {
                $address .= $companySecond . "\n";
            }
        }

        if (array_key_exists('street', $this->_imprint)) {
            $address .= $this->_imprint['street'] . "\n";
        }
        if (array_key_exists('zip', $this->_imprint)) {
            $address .= $this->_imprint['zip'] . " ";
        }
        if (array_key_exists('city', $this->_imprint)) {
            $address .= $this->_imprint['city'] . "\n";
        }

        if (!empty($this->_imprint['country'])) {
            $countryName = Mage::getModel('directory/country')->loadByCode($this->_imprint['country'])->getName();
            $address .= Mage::helper('core')->__($countryName);
        }

        foreach (explode("\n", $address) as $value) {
            if ($value !== '') {
                $page->drawText(trim(strip_tags($value)), $this->margin['left'] - 20, $y, $this->encoding);
                $y -= 12;
            }
        }
    }

    /**
     * Draw
     *
     * @param  Varien_Object          $item     creditmemo/shipping/invoice to draw
     * @param  Zend_Pdf_Page          $page     Current page object of Zend_Pdf
     * @param  Mage_Sales_Model_Order $order    order to get infos from
     * @param  int                    $position position in table
     *
     * @return Zend_Pdf_Page
     */
    protected function _drawItem(Varien_Object $item, Zend_Pdf_Page $page, Mage_Sales_Model_Order $order, $position = 1)
    {
        $type = $item->getOrderItem()->getProductType();

        $renderer = $this->_getRenderer($type);
        $renderer->setOrder($order);
        $renderer->setItem($item);
        $renderer->setPdf($this);
        $renderer->setPage($page);
        $renderer->setRenderedModel($this);

//        $renderer->indents = array(
//            35, 140, 145
//        );
//        $renderer->fontSizes = array(
//            'regular' => $this->fontSizes['small'],
//            'small'   => $this->fontSizes['tiny']
//        );

        $renderer->draw($position);

        return $renderer->getPage();
    }

    /**
     * Get stanard font
     *
     * @return Zend_Pdf_Resource_Font the regular font
     */
    public function getFontRegular()
    {
        if ($this->getRegularFont() && $this->regularFontFileExists()) {
            return Zend_Pdf_Font::fontWithPath($this->getRegularFontFile());
        }

        return Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_Re-4.4.1.ttf');
    }

    /**
     * Get default bold font
     *
     * @return Zend_Pdf_Resource_Font the bold font
     */
    public function getFontBold()
    {
        if ($this->getBoldFont() && $this->boldFontFileExists()) {
            return Zend_Pdf_Font::fontWithPath($this->getBoldFontFile());
        }

        return Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_Bd-2.8.1.ttf');
    }
}
