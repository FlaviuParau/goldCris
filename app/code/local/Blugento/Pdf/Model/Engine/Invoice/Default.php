<?php
/**
 * Blugento PDF
 * Default invoice rendering engine.
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Adminmenu
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Pdf_Model_Engine_Invoice_Default extends Blugento_Pdf_Model_Engine_Abstract
{
    /**
     * constructor to set mode to invoice
     */
    public function __construct()
    {
        parent::__construct();
        $this->setMode('invoice');
    }

    /**
     * Return PDF document
     *
     * @param array $invoices invoices to render pdfs for
     *
     * @return Zend_Pdf
     */
    public function getPdf($invoices = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);

        $fontSizeRegular = $this->fontSizes['regular'];

        foreach ($invoices as $invoice) {
            // pagecounter is 0 at the beginning, because it is incremented in newPage()
            $this->pagecounter = 0;
            if ($invoice->getStoreId()) {
                Mage::app()->getLocale()->emulate($invoice->getStoreId());
                Mage::app()->setCurrentStore($invoice->getStoreId());
            }
            $order = $invoice->getOrder();
            $this->setOrder($order);

            $page = $this->newPage();

            $this->insertAddressesAndHeader($page, $invoice, $order);

            $this->_setFontRegular($page, $fontSizeRegular);
            $this->insertTableHeader($page);

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
     * Insert Table Header for Items
     *
     * @param  Zend_Pdf_Page &$page current page object of Zend_PDF
     *
     * @return void
     */
    protected function insertTableHeader(&$page)
    {
        $fontSizeRegular = $this->fontSizes['small'];

        $page->setFillColor($this->colors['grey1']);
        $page->setLineColor($this->colors['grey1']);
        $page->setLineWidth(1);
        $page->drawRectangle($this->margin['left'], $this->y, $this->margin['right'], $this->y - 15);

        $page->setFillColor($this->colors['black']);
        $font = $this->_setFontRegular($page, $fontSizeRegular);

        $this->y -= 11;
        $page->drawText(strip_tags(Mage::helper('blugento_pdf')->__('Pos')), $this->margin['left'] + 3, $this->y, $this->encoding);
        $page->drawText(Mage::helper('blugento_pdf')->__('No.'), $this->margin['left'] + 35, $this->y, $this->encoding);
        $page->drawText(Mage::helper('blugento_pdf')->__('Description'), $this->margin['left'] + 140, $this->y, $this->encoding);

        $columns = array();
        $columns['price'] = array(
            'label'  => strip_tags(Mage::helper('blugento_pdf')->__('Price')),
            '_width' => 60
        );
        $columns['price_incl_tax'] = array(
            'label'  => strip_tags(Mage::helper('blugento_pdf')->__('Price (incl. tax)')),
            '_width' => 60
        );
        $columns['price_excl_tax'] = array(
            'label'  => strip_tags(Mage::helper('blugento_pdf')->__('Price (excl. tax)')),
            '_width' => 55
        );
        $columns['qty'] = array(
            'label'  => strip_tags(Mage::helper('blugento_pdf')->__('Qty')),
            '_width' => 40
        );
        $columns['measuring_unit'] = array(
            'label'  => strip_tags(Mage::helper('blugento_pdf')->__('UM')),
            '_width' => 60
        );
        $columns['old_price'] = array(
            'label'  => strip_tags(Mage::helper('blugento_pdf')->__('Old Price')),
            '_width' => 60
        );
        $columns['tax'] = array(
            'label'  => strip_tags(Mage::helper('blugento_pdf')->__('Tax')),
            '_width' => 45
        );
        $columns['tax_rate'] = array(
            'label'  => strip_tags(Mage::helper('blugento_pdf')->__('Tax rate')),
            '_width' => 50
        );
        $columns['subtotal'] = array(
            'label'  => strip_tags(Mage::helper('blugento_pdf')->__('Total')),
            '_width' => 50
        );
        $columns['subtotal_incl_tax'] = array(
            'label'  => strip_tags(Mage::helper('blugento_pdf')->__('Total (incl. tax)')),
            '_width' => 70
        );
        $columns['subtotal_excl_tax'] = array(
            'label'  => strip_tags(Mage::helper('blugento_pdf')->__('Total (excl. tax)')),
            '_width' => 70
        );
        // draw price, tax, and subtotal in specified order
        $columnsOrder = explode(',', Mage::getStoreConfig('sales_pdf/invoice/item_price_column_order'));
        // draw starting from right
        $columnsOrder = array_reverse($columnsOrder);
        $columnOffset = 0;
        foreach ($columnsOrder as $columnName) {
            $columnName = trim($columnName);
            if (array_key_exists($columnName, $columns)) {
                $column = $columns[$columnName];
                $labelWidth = $this->widthForStringUsingFontSize($column['label'], $font, $fontSizeRegular);
                $page->drawText(
                    $column['label'],
                    $this->margin['right'] - $columnOffset - $labelWidth,
                    $this->y,
                    $this->encoding
                );
                $columnOffset += $column['_width'];
            }
        }
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
            'model'    => 'blugento_pdf/items_default',
            'renderer' => null
        );
        $this->_renderers['grouped'] = array(
            'model'    => 'blugento_pdf/items_grouped',
            'renderer' => null
        );
        $this->_renderers['bundle'] = array(
            'model'    => 'blugento_pdf/items_bundle',
            'renderer' => null
        );
        $this->_renderers['downloadable'] = array(
            'model'    => 'blugento_pdf/items_downloadable',
            'renderer' => null
        );
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
        $fontSize = $this->fontSizes['tiny'];
        $font = $this->_setFontRegular($page, $fontSize);

        $y = $this->y;
        $text = trim(strip_tags($this->_imprint['company_first'])) . ', ' . $this->_getCompanyAddressLine();
        foreach ($this->_prepareText($text, $page, $font, $fontSize) as $tmpVal) {
            $page->drawText($tmpVal, $this->margin['left'], $y, $this->encoding);
            $y -= 14;
        }

        $this->y = $y;
    }

}
