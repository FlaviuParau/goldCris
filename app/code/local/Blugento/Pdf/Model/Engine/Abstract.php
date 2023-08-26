<?php
/**
 * Blugento PDF
 * Abstract pdf model.
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Adminmenu
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

abstract class Blugento_Pdf_Model_Engine_Abstract extends Mage_Sales_Model_Order_Pdf_Abstract
{
    /**
     * Document margins
     * @var array
     */
    public $margin = array(
        'left'  => 45,
        'right' => 540
    );

    /**
     * @var array
     */
    public $colors = array();

    /**
     * @var string
     */
    public $mode;

    /**
     * @var string
     */
    public $encoding;

    /**
     * @var int
     */
    public $pagecounter;

    /**
     * Font sizes
     * @var array
     */
    public $fontSizes = array(
        'tiny'      => 8,
        'small'     => 9,
        'regular'   => 10,
        'large'     => 12,
        'big'       => 14
    );

    /**
     * Company block size
     * @var int
     */
    public $companyBlockSize = 250;

    /**
     * Imprint fields
     * @var array
     */
    protected $_imprint;

    /**
     * Correct all y values if the logo is full width and bigger than normal
     * @var int
     */
    protected $_marginTop = 0;

    /**
     * Constructor to init settings
     */
    public function __construct()
    {
        parent::__construct();

        $this->encoding = 'UTF-8';

        $this->colors['black'] = new Zend_Pdf_Color_GrayScale(0);
        $this->colors['grey1'] = new Zend_Pdf_Color_GrayScale(0.9);

        // get the default imprint
        $this->_imprint = Mage::getStoreConfig('blugentolocalizer/imprint');
    }

    /**
     * Draw one line
     *
     * @param  Zend_Pdf_Page $page         Current page object of Zend_Pdf
     * @param  array         $draw         items to draw
     * @param  array         $pageSettings page settings to use for new pages
     *
     * @return Zend_Pdf_Page
     */
    public function drawLineBlocks(Zend_Pdf_Page $page, array $draw, array $pageSettings = array())
    {
        $fontSizeSmall = $this->fontSizes['small'];

        foreach ($draw as $itemsProp) {
            if (!isset($itemsProp['lines']) || !is_array($itemsProp['lines'])) {
                Mage::throwException(Mage::helper('sales')->__('Invalid draw line data. Please define "lines" array'));
            }
            $lines = $itemsProp['lines'];
            $height = isset($itemsProp['height']) ? $itemsProp['height'] : 10;

            if (empty($itemsProp['shift'])) {
                $shift = 0;
                foreach ($lines as $line) {
                    $maxHeight = 0;
                    foreach ($line as $column) {
                        $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                        if (!is_array($column['text'])) {
                            $column['text'] = array($column['text']);
                        }
                        $top = count($column['text']) * $lineSpacing;

                        $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                    }
                    $shift += $maxHeight;
                }
                $itemsProp['shift'] = $shift;
            }

            if ($this->y - $itemsProp['shift'] < 50 || (Mage::getStoreConfig('sales_pdf/blugento_pdf/show_footer') == 1 && $this->y - $itemsProp['shift'] < 100)) {
                $page = $this->newPage($pageSettings);
            }

            foreach ($lines as $line) {
                $maxHeight = 0;
                foreach ($line as $column) {
                    $fontSize = empty($column['font_size']) ? $fontSizeSmall : $column['font_size'];
                    if (!empty($column['font_file'])) {
                        $font = Zend_Pdf_Font::fontWithPath($column['font_file']);
                        $page->setFont($font, $fontSize);
                    } else {
                        $fontStyle = empty($column['font']) ? 'regular' : $column['font'];
                        switch ($fontStyle) {
                            case 'bold':
                                $font = $this->_setFontBold($page, $fontSize);
                                break;
                            case 'italic':
                                $font = $this->_setFontItalic($page, $fontSize);
                                break;
                            default:
                                $font = $this->_setFontRegular($page,
                                    $fontSize);
                                break;
                        }
                    }

                    if (!is_array($column['text'])) {
                        $column['text'] = array($column['text']);
                    }

                    $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                    $top = 0;
                    foreach ($column['text'] as $part) {
                        $feed = $column['feed'];
                        $textAlign = empty($column['align']) ? 'left' : $column['align'];
                        $width = empty($column['width']) ? 0 : $column['width'];
                        switch ($textAlign) {
                            case 'right':
                                if ($width) {
                                    $feed = $this->getAlignRight($part, $feed, $width, $font, $fontSize);
                                } else {
                                    $feed = $feed - $this->widthForStringUsingFontSize($part, $font, $fontSize);
                                }
                                break;
                            case 'center':
                                if ($width) {
                                    $feed = $this->getAlignCenter($part, $feed, $width, $font, $fontSize);
                                }
                                break;
                        }
                        $page->drawText($part, $feed, $this->y - $top, 'UTF-8');
                        $top += $lineSpacing;
                    }

                    $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                }
                $this->y -= $maxHeight;
            }
        }

        return $page;
    }

    /**
     * Set pdf mode.
     *
     * @param  string $mode set mode to differ between creditmemo, invoice, etc.
     *
     * @return Blugento_Pdf_Model_Engine_Abstract
     */
    public function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * Return pdf mode.
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Set next line position
     *
     * @param  int $height Line-Height
     *
     * @return void
     */
    protected function Ln($height = 15)
    {
        $this->y -= $height;
    }

    /**
     * Insert logo
     *
     * @param  Zend_Pdf_Page &$page Current page object of Zend_Pdf
     * @param  mixed         $store store to get data from
     *
     * @return void
     */
    protected function insertLogo(&$page, $store = null)
    {
        if ($this->_isLogoFullWidth($store)) {
            $this->_insertLogoFullWidth($page, $store);
        } else {
            $this->_insertLogoPositioned($page, $store);
        }
    }

    /**
     * is the setting to show the logo full width?
     *
     * @param  mixed $store store we want the config setting from
     *
     * @return bool
     */
    protected function _isLogoFullWidth($store)
    {
        return Mage::helper('blugento_pdf')->isLogoFullWidth($store);
    }

    /**
     * Inserts the logo if it is positioned left, center or right.
     *
     * @param  Zend_Pdf_Page &$page Current page object of Zend_Pdf
     * @param  mixed         $store store to get data from
     *
     * @return void
     */
    protected function _insertLogoPositioned(&$page, $store = null)
    {
        $imageRatio = (int)Mage::getStoreConfig('sales_pdf/blugento_pdf/logo_ratio', $store);
        $imageRatio = (empty($imageRatio)) ? 100 : $imageRatio;

        $maxwidth  = ($this->margin['right'] - $this->margin['left']) * $imageRatio / 100;
        $maxheight = 100;

        $image = Mage::getStoreConfig('sales/identity/logo', $store);
        if ($image and file_exists(Mage::getBaseDir('media', $store) . '/sales/store/logo/' . $image)) {
            $image = Mage::getBaseDir('media', $store) . '/sales/store/logo/' . $image;

            list ($width, $height) = Mage::helper('blugento_pdf')->getScaledImageSize($image, $maxwidth, $maxheight);

            if (is_file($image)) {
                $image = Zend_Pdf_Image::imageWithPath($image);

                $logoPosition
                    = Mage::getStoreConfig('sales_pdf/blugento_pdf/logo_position',
                    $store);

                switch ($logoPosition) {
                    case 'center':
                        $startLogoAt = $this->margin['left'] + (($this->margin['right'] - $this->margin['left']) / 2) - $width / 2;
                        break;
                    case 'right':
                        $startLogoAt = $this->margin['right'] - $width;
                        break;
                    default:
                        $startLogoAt = $this->margin['left'];
                }

                $position['x1'] = $startLogoAt;
                $position['y1'] = 720;
                $position['x2'] = $position['x1'] + $width;
                $position['y2'] = $position['y1'] + $height;

                $page->drawImage($image, $position['x1'], $position['y1'], $position['x2'], $position['y2']);
            }
        }
    }

    /**
     * inserts the logo from complete left to right
     *
     * @param Zend_Pdf_Page &$page current Zend_Pdf_Page object
     * @param mixed         $store store we need the config setting from
     *
     * @todo merge _insertLogoPositioned and _insertLogoFullWidth
     */
    protected function _insertLogoFullWidth(&$page, $store = null)
    {
        $imageRatio = (int)Mage::getStoreConfig('sales_pdf/blugento_pdf/logo_ratio', $store);
        $imageRatio = (empty($imageRatio)) ? 1 : $imageRatio;

        $maxwidth  = 594 * $imageRatio / 100;
        $maxheight = 300;

        $image = Mage::getStoreConfig('sales/identity/logo', $store);
        if ($image and file_exists(Mage::getBaseDir('media', $store) . '/sales/store/logo/' . $image)) {
            $image = Mage::getBaseDir('media', $store) . '/sales/store/logo/' . $image;

            list ($width, $height) = Mage::helper('blugento_pdf')->getScaledImageSize($image, $maxwidth, $maxheight);

            if (is_file($image)) {
                $image = Zend_Pdf_Image::imageWithPath($image);

                $logoPosition = Mage::getStoreConfig('sales_pdf/blugento_pdf/logo_position', $store);

                switch ($logoPosition) {
                    case 'center':
                        $startLogoAt = $this->margin['left'] + (($this->margin['right'] - $this->margin['left']) / 2) - $width / 2;
                        break;
                    case 'right':
                        $startLogoAt = $this->margin['right'] - $width;
                        break;
                    default:
                        $startLogoAt = 0;
                }

                $position['x1'] = $startLogoAt;
                $position['y1'] = 663;
                $position['x2'] = $position['x1'] + $width;
                $position['y2'] = $position['y1'] + $height;

                $page->drawImage($image, $position['x1'], $position['y1'], $position['x2'], $position['y2']);
                $this->_marginTop = $height - 130;
            }
        }
    }

    /**
     * insert customer address and all header like customer number, etc.
     *
     * @param Zend_Pdf_Page             $page   current Zend_Pdf_Page
     * @param Mage_Sales_Model_Abstract $source source for the address information
     * @param Mage_Sales_Model_Order    $order  order to print the document for
     */
    protected function insertAddressesAndHeader(Zend_Pdf_Page $page, Mage_Sales_Model_Abstract $source, Mage_Sales_Model_Order $order) {
        // Add logo
        $this->insertLogo($page, $source->getStore());

        // Add company address
        $this->y = 692 - $this->_marginTop;
        $this->_insertCompanyBlock($page);
        $this->Ln(12);

        // Add billing address
        $position = $this->y;
        $this->_insertCustomerAddress($page, $order);
        $position1 = $this->y;

        // Add invoice info
        $this->y = $position;
        $this->_insertInvoiceInfo($page, $order, $source);
        $position2 = $this->y;

        // Add head
        $this->y = min($position1, $position2); //492 - $this->_marginTop;
        $this->insertHeader($page, $order, $source);
        $this->Ln(14);
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
        $fontSize = $this->fontSizes['regular'];
        $font = $this->_setFontRegular($page, $fontSize);

        $page->drawText(trim(strip_tags($this->_imprint['company_first'])), $this->margin['left'], $this->y, $this->encoding);
        $this->Ln();

        $y = $this->y;
        foreach ($this->_prepareText($this->_getCompanyAddressLine(), $page, $font, $fontSize, $this->companyBlockSize) as $tmpVal) {
            $page->drawText($tmpVal, $this->margin['left'], $y, $this->encoding);
            $y -= 14;
        }

        $this->y = $y;
    }

    protected function _insertInvoiceInfo(Zend_Pdf_Page &$page, Mage_Sales_Model_Order $order, Mage_Sales_Model_Abstract $document)
    {
        $marginLeft = $this->margin['left'] + $this->getHeaderblockOffset() + $this->companyBlockSize + 50;

        $mode = $this->getMode();

        $fontSizeRegular = $this->fontSizes['regular'];

        $valueRightOffset = 10 + $this->getHeaderblockOffset();
        $font = $this->_setFontRegular($page, $fontSizeRegular);
        $numberOfLines = 0;

        // Invoice/shipment/creditmemo Number
        if ($mode == 'invoice') {
            $numberTitle = 'Invoice number:';
        } elseif ($mode == 'shipment') {
            $numberTitle = 'Shipment number:';
        } else {
            $numberTitle = 'Creditmemo number:';
        }
        $page->drawText(Mage::helper('blugento_pdf')->__($numberTitle), $marginLeft, $this->y, $this->encoding);

        $incrementId = $document->getIncrementId();
        $page->drawText($incrementId, ($this->margin['right'] - $valueRightOffset - $this->widthForStringUsingFontSize($incrementId, $font, $fontSizeRegular)), $this->y, $this->encoding);
        $this->Ln();
        $numberOfLines++;

        // Order Number
        $putOrderId = $this->_putOrderId($order);
        if ($putOrderId) {
            $page->drawText(Mage::helper('blugento_pdf')->__('Order number:'), $marginLeft, $this->y, $this->encoding);
            $page->drawText($putOrderId, ($this->margin['right'] - $valueRightOffset - $this->widthForStringUsingFontSize($putOrderId, $font, $fontSizeRegular)), $this->y, $this->encoding);
            $this->Ln();
            $numberOfLines++;
        }

        // Invoice date
        $page->drawText(Mage::helper('blugento_pdf')->__(($mode == 'invoice') ? 'Invoice date:' : 'Date:'), $marginLeft, $this->y, $this->encoding);
        $documentDate = Mage::helper('core')->formatDate($document->getCreatedAtDate(), 'medium', false);
        $page->drawText($documentDate, ($this->margin['right'] - $valueRightOffset - $this->widthForStringUsingFontSize($documentDate, $font, $fontSizeRegular)), $this->y, $this->encoding);
        $this->Ln();
        $numberOfLines++;

        // Company imprint fields
        $fields = array();
        if (array_key_exists('telephone', $this->_imprint) && $this->_imprint['telephone']) {
            $value = trim(strip_tags($this->_imprint['telephone']));
            if ($value) {
                $fields['Telephone:'] = trim(strip_tags($this->_imprint['telephone']));
            }
        }
        if (array_key_exists('fax', $this->_imprint) && $this->_imprint['fax']) {
            $value = trim(strip_tags($this->_imprint['fax']));
            if ($value) {
                $fields['Fax:'] = trim(strip_tags($this->_imprint['fax']));
            }
        }
        if (array_key_exists('web', $this->_imprint) && $this->_imprint['web']) {
            $value = trim(strip_tags($this->_imprint['web']));
            if ($value) {
                $fields['Web:'] = trim(strip_tags($this->_imprint['web']));
            }
        }
        if (array_key_exists('email', $this->_imprint) && $this->_imprint['email']) {
            $value = trim(strip_tags($this->_imprint['email']));
            if ($value) {
                $fields['E-mail:'] = trim(strip_tags($this->_imprint['email']));
            }
        }

        if (!empty($fields)) {
            $this->Ln();
            $numberOfLines++;
            foreach ($fields as $label => $text) {
                $page->drawText(Mage::helper('blugento_pdf')->__($label), $marginLeft, $this->y, $this->encoding);
                $page->drawText($text, ($this->margin['right'] - $valueRightOffset - $this->widthForStringUsingFontSize($text, $font, $fontSizeRegular)), $this->y, $this->encoding);
                $this->Ln();
                $numberOfLines++;
            }
        }

        $this->y -= ($numberOfLines * 2);

    }

    /**
     * Build address line from imprint fields
     * @return string
     */
    protected function _getCompanyAddressLine()
    {
        $address = array();
        if (array_key_exists('street', $this->_imprint)) {
            $address[] = $this->_imprint['street'];
        }
        if (array_key_exists('city', $this->_imprint)) {
            $address[] = $this->_imprint['city'] . "\n";
        }
        if (array_key_exists('zip', $this->_imprint)) {
            $address[] = $this->_imprint['zip'];
        }

        return implode(' ', $address);
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
        $this->_setFontRegular($page, $this->fontSizes['regular']);
        $billing = $this->_formatAddress($order->getBillingAddress()->format('pdf'));
        foreach ($billing as $line) {
            $page->drawText(trim(strip_tags($line)), $this->margin['left'] + $this->getHeaderblockOffset(), $this->y, $this->encoding);
            $this->Ln(12);
        }
    }

    /**
     * get the offset to position the address block left or right
     *
     * @return int
     */
    protected function getHeaderblockOffset()
    {
        if (Mage::getStoreConfig('sales_pdf/blugento_pdf/headerblocks_position') == Blugento_Pdf_Model_System_Config_Source_Headerblocks::LEFT) {
            $offsetAdjustment = 0;
        } else {
            $offsetAdjustment = 315;
        }

        return $offsetAdjustment;
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
        $mode = $this->getMode();
        $this->_setFontBold($page, $this->fontSizes['big']);

        if ($mode == 'invoice') {
            $title = 'Invoice';
        } elseif ($mode == 'shipment') {
            $title = 'Packingslip';
        } else {
            $title = 'Creditmemo';
        }
        $page->drawText(Mage::helper('blugento_pdf')->__($title), $this->margin['left'], $this->y, $this->encoding);
    }

    /**
     * Return the order id or false if order id should not be displayed on document.
     *
     * @param  Mage_Sales_Model_Order $order order to get id from
     *
     * @return int|false
     */
    protected function _putOrderId($order)
    {
        return Mage::helper('blugento_pdf')->putOrderId($order, $this->mode);
    }

    /**
     * do we show the customber number on this document
     *
     * @param  mixed $store store from whom we need the config setting
     *
     * @return bool
     */
    protected function _showCustomerNumber($store)
    {
        return Mage::helper('blugento_pdf')->showCustomerNumber($this->mode, $store);
    }

    /**
     * do we show the customber VAT number on this document
     *
     * @param  mixed $store store from whom we need the config setting
     *
     * @return bool
     */
    protected function _showCustomerVATNumber($store)
    {
        return Mage::helper('blugento_pdf')->showCustomerVATNumber($this->mode, $store);
    }

    /**
     * which customer number should be displayed for guest orders
     *
     * @param  mixed $store store from whom we need the config setting
     *
     * @return string
     */
    protected function _getGuestorderCustomerNo($store)
    {
        return Mage::helper('blugento_pdf')->getGuestorderCustomerNo($this->mode, $store);
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

        $this->_addFooter($page, Mage::app()->getStore());

        // set the font because it may not be set, see https://github.com/blugento/blugento-pdf/issues/184
        $this->_setFontRegular($page, $this->fontSizes['small']);

        // provide the possibility to add random stuff to the page
        Mage::dispatchEvent('blugento_pdf_' . $this->getMode() . '_edit_page', array('page' => $page, 'order' => $this->getOrder()));

        $this->y = 800;
        $this->_setFontRegular($page, $this->fontSizes['small']);

        return $page;
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

        $renderer->indents = array(
            35, 140, 145
        );
        $renderer->fontSizes = array(
            'regular' => $this->fontSizes['small'],
            'small'   => $this->fontSizes['tiny']
        );

        $renderer->draw($position);

        return $renderer->getPage();
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
                    $lineBlock['lines'][] = array(
                        array(
                            'text'      => $label,
                            'feed'      => $this->margin['right'] - 70,
                            'align'     => 'right',
                            'font_size' => $totalData['font_size']
                        ),
                        array(
                            'text'      => $totalData['amount'],
                            'feed'      => $this->margin['right'],
                            'align'     => 'right',
                            'font_size' => $totalData['font_size']
                        ),
                    );
                }
            }
        }
        $page = $this->drawLineBlocks($page, array($lineBlock));

        return $page;
    }

    /**
     * Insert Notes
     *
     * @param  Zend_Pdf_Page             $page   Current Page Object of Zend_PDF
     * @param  Mage_Sales_Model_Order    &$order order to get note from
     * @param  Mage_Sales_Model_Abstract &$model invoice/shipment/creditmemo
     *
     * @return \Zend_Pdf_Page
     */
    protected function _insertNote($page, &$order, &$model)
    {
        $fontSize = $this->fontSizes['small'];
        $font = $this->_setFontItalic($page, $fontSize);
        $this->y = $this->y - 60;

        $notes = array();
        $result = new Varien_Object();
        $result->setNotes($notes);
        Mage::dispatchEvent(
            'blugento_pdf_' . $this->getMode() . '_insert_note',
            array(
                'order'          => $order,
                $this->getMode() => $model,
                'result'         => $result
            )
        );
        $notes = array_merge($notes, $result->getNotes());

        // Get free text notes.
        $note = Mage::getStoreConfig('sales_pdf/' . $this->getMode() . '/note');
        if (!empty($note)) {
            $tmpNotes = explode("\n", $note);
            $notes = array_merge($notes, $tmpNotes);
        }

        // Draw notes on PDF.
        foreach ($notes as $note) {
            // prepare the text so that it fits to the paper
            foreach ($this->_prepareText($note, $page, $font, $fontSize) as $tmpNote) {
                // create a new page if necessary
                if ($this->y < 50 || (Mage::getStoreConfig('sales_pdf/blugento_pdf/show_footer') == 1 && $this->y < 100)) {
                    $page = $this->newPage(array());
                    $this->y = $this->y - 60;
                    $font = $this->_setFontRegular($page, $fontSize);
                }
                $page->drawText($tmpNote, $this->margin['left'], $this->y + 30, $this->encoding);
                $this->Ln(15);
            }
        }

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

        $fontSize = $this->fontSizes['tiny'];

        $footerText = trim(strip_tags(Mage::getStoreConfig('sales_pdf/blugento_pdf/footer_text')));
        if ($footerText) {
            $this->y = 110;
            $font = $this->_setFontRegular($page, $fontSize);
            $this->_insertFooterText($page, $footerText, $font, $fontSize);

            // Add page counter.
            $this->y = 110;
            $this->_insertPageCounter($page, $fontSize);
            return;
        }

        // get the imprint of the store if a store is set
        if (!empty($store)) {
            $imprintObject = new Varien_Object();
            $imprintObject->setImprint(Mage::getStoreConfig('blugentolocalizer/imprint', $store));
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
            $this->_insertPageCounter($page, $fontSize);
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

        $fontSize = $this->fontSizes['tiny'];
        $this->_insertFooterAddress($page, null, $fontSize);

        $fields = array(
            'court'              => Mage::helper('blugento_pdf')->__('Register court:'),
            'register_number'    => Mage::helper('blugento_pdf')->__('Register number:'),
            'tax_number'         => Mage::helper('blugento_pdf')->__('Tax number:'),
            'vat_id'             => Mage::helper('blugento_pdf')->__('VAT-ID:'),
            'ceo'                => Mage::helper('blugento_pdf')->__('CEO:'),
            'owner'              => Mage::helper('blugento_pdf')->__('Owner:'),
            //'bank_account'       => Mage::helper('blugento_pdf')->__('Account:'),
            'bank_account_owner' => Mage::helper('blugento_pdf')->__('Account owner:'),
            'bank_name'          => Mage::helper('blugento_pdf')->__('Bank name:'),
            'iban'               => Mage::helper('blugento_pdf')->__('IBAN:'),
            'swift'              => Mage::helper('blugento_pdf')->__('SWIFT:'),
            //'bank_code_number'   => Mage::helper('blugento_pdf')->__('Bank number:'),
        );


        $cleanFields = array(
            array(
                'court'              => Mage::helper('blugento_pdf')->__('Register court:'),
                'register_number'    => Mage::helper('blugento_pdf')->__('Register number:'),
                'tax_number'         => Mage::helper('blugento_pdf')->__('Tax number:'),
                'vat_id'             => Mage::helper('blugento_pdf')->__('VAT-ID:'),
            ),
            array(
                'ceo'                => Mage::helper('blugento_pdf')->__('CEO:'),
                'owner'              => Mage::helper('blugento_pdf')->__('Owner:'),
            ),
            array(
                'bank_account_owner' => Mage::helper('blugento_pdf')->__('Account owner:'),
                'bank_name'          => Mage::helper('blugento_pdf')->__('Bank name:'),
                'iban'               => Mage::helper('blugento_pdf')->__('IBAN:'),
                'swift'              => Mage::helper('blugento_pdf')->__('SWIFT:'),
            )
        );

        $font = $this->_setFontRegular($page, $fontSize);
        $colPosition = -20;
        $colSpace = $this->_getMaxWidthFields($fields, $font, $fontSize) + 5;
        $colWidth = 155;
        foreach ($cleanFields as $i => $chunk) {
            if ($i>0) {
                $colPosition += $colWidth + 20;
            }
            $this->_insertFooterBlock($page, $chunk, $colPosition, $colSpace, 190, $fontSize);
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
    protected function _insertFooterBlock(&$page, $fields, $colposition = 0, $valadjust = 30, $colwidth = null, $fontSize = null)
    {
        if (!$fontSize) {
            $fontSize = $this->fontSizes['regular'];
        }
        $font = $this->_setFontRegular($page, $fontSize);
        $y = $this->y;

        $valposition = $colposition + $valadjust;

        if (is_array($fields)) {
            foreach ($fields as $field => $label) {
                if (empty($this->_imprint[$field])) {
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
            $fontSize = $this->fontSizes['tiny'];
        }
        $font = $this->_setFontBold($page, $fontSize);
        $y = $this->y;
        $y -= 5;

        $address = array($this->_imprint['company_first']);
        if (array_key_exists('street', $this->_imprint)) {
            $address[] = $this->_imprint['street'];
        }
        if (array_key_exists('zip', $this->_imprint)) {
            $zip = $this->_imprint['zip'];
            if (array_key_exists('city', $this->_imprint)) {
                $zip .= ' ' . $this->_imprint['city'];
            }
            $address[] = $zip;
        } else
        if (array_key_exists('city', $this->_imprint)) {
            $address[] = $this->_imprint['city'];
        }
        if (!empty($this->_imprint['country'])) {
            $countryName = Mage::getModel('directory/country')->loadByCode($this->_imprint['country'])->getName();
            $address[] = Mage::helper('core')->__($countryName);
        }
        $address = implode(' | ', $address);

        foreach ($this->_prepareText($address, $page, $font, $fontSize, $page->getWidth()) as $line) {
            $page->drawText($line, $this->margin['left'] - 20, $y, $this->encoding);
            $y -= 12;
        }
        $y -= 5;

        $this->y = $y;
    }

    /**
     * Insert page counter
     *
     * @param  Zend_Pdf_Page &$page Current page object of Zend_Pdf
     *
     * @return void
     */
    protected function _insertPageCounter(&$page, $fontSize = null)
    {
        if (!$fontSize) {
            $fontSize = $this->fontSizes['small'];
        }
        $font = $this->_setFontRegular($page, $fontSize);
        $page->drawText(Mage::helper('blugento_pdf')->__('Page') . ' ' . $this->pagecounter, $this->margin['right'] - 23 - $this->widthForStringUsingFontSize($this->pagecounter, $font, 9), $this->y, $this->encoding);
    }

    /**
     * get stanard font
     *
     * @return Zend_Pdf_Resource_Font the regular font
     */
    public function getFontRegular()
    {
        if ($this->getRegularFont() && $this->regularFontFileExists()) {
            return Zend_Pdf_Font::fontWithPath($this->getRegularFontFile());
        }

        return Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
    }

    /**
     * Set default font
     *
     * @param  Zend_Pdf_Page $object Current page object of Zend_Pdf
     * @param  string|int    $size   Font size
     *
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontRegular($object, $size = 10)
    {
        $font = $this->getFontRegular();
        $object->setFont($font, $size);

        return $font;
    }

    /**
     * get default bold font
     *
     * @return Zend_Pdf_Resource_Font the bold font
     */
    public function getFontBold()
    {
        if ($this->getBoldFont() && $this->boldFontFileExists()) {
            return Zend_Pdf_Font::fontWithPath($this->getBoldFontFile());
        }

        return Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
    }

    /**
     * Set bold font
     *
     * @param  Zend_Pdf_Page $object Current page object of Zend_Pdf
     * @param  string|int    $size   Font size
     *
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontBold($object, $size = 10)
    {
        $font = $this->getFontBold();
        $object->setFont($font, $size);

        return $font;
    }

    /**
     * get italic font
     *
     * @return Zend_Pdf_Resource_Font
     */
    public function getFontItalic()
    {
        if ($this->getItalicFont() && $this->italicFontFileExists()) {
            return Zend_Pdf_Font::fontWithPath($this->getItalicFontFile());
        }

        return Zend_Pdf_Font::fontWithName(
            Zend_Pdf_Font::FONT_HELVETICA_ITALIC
        );
    }

    /**
     * Set italic font
     *
     * @param  Zend_Pdf_Page $object Current page object of Zend_Pdf
     * @param  string|int    $size   Font size
     *
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontItalic($object, $size = 10)
    {
        $font = $this->getFontItalic();
        $object->setFont($font, $size);

        return $font;
    }

    /**
     * Prepares the text so that it fits to the given page's width.
     *
     * @param  string                 $text     the text which should be prepared
     * @param  Zend_Pdf_Page          $page     the page on which the text will be rendered
     * @param  Zend_Pdf_Resource_Font $font     the font with which the text will be rendered
     * @param  int                    $fontSize the font size with which the text will be rendered
     * @param  int                    $width    [optional] the width for the given text, defaults to the page width
     *
     * @return array the given text in an array where each item represents a new line
     */
    public function _prepareText($text, $page, $font, $fontSize, $width = null)
    {
        if (empty($text)) {
            return array();
        }
        $lines = '';
        $currentLine = '';
        // calculate the page's width with respect to the margins
        if (empty($width)) {
            $width = $page->getWidth() - $this->margin['left'] - ($page->getWidth() - $this->margin['right']);
        }
        // regular expression that splits on whitespaces and dashes based on http://stackoverflow.com/a/11758732/719023
        $textChunks = preg_split('/([^\s-]+[\s-]+)/', $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        foreach ($textChunks as $textChunk) {
            $textChunk = trim($textChunk);
            if ($this->widthForStringUsingFontSize($currentLine . ' ' . $textChunk, $font, $fontSize) < $width) {
                // do not add whitespace on first line
                if (!empty($currentLine)) {
                    $currentLine .= ' ';
                }
                $currentLine .= $textChunk;
            } else {
                // text is too broad, so add new line character
                $lines .= $currentLine . "\n";
                $currentLine = $textChunk;
            }
        }
        // append the last line
        $lines .= $currentLine;

        return explode("\n", $lines);
    }

    /**
     * Fix the percentage for taxes which come with four decimal places
     * from magento core.
     *
     * @param  string $label tax label which contains the badly formatted tax percentage
     *
     * @return string
     */
    protected function fixNumberFormat($label)
    {
        $pattern = "/(.*)\((\d{1,2}\.\d{4}%)\)/";
        if (preg_match($pattern, $label, $matches)) {
            $percentage = Zend_Locale_Format::toNumber(
                $matches[2],
                array(
                    'locale'    => Mage::app()->getLocale()->getLocale(),
                    'precision' => 2,
                )
            );

            return $matches[1] . '(' . $percentage . '%)';
        }

        return $label;
    }

    /**
     * get bold font file
     *
     * @return string
     */
    protected function getBoldFontFile()
    {
        return Mage::helper('blugento_pdf')->getFontPath() . DS . $this->getBoldFont();
    }

    /**
     * get bold font path
     *
     * @return string
     */
    protected function getBoldFont()
    {
        return Mage::getStoreConfig(
            Blugento_Pdf_Helper_Data::XML_PATH_BOLD_FONT
        );
    }

    /**
     * check whether font file exists for bold font
     *
     * @return bool
     */
    protected function boldFontFileExists()
    {
        return file_exists($this->getBoldFontFile());
    }

    /**
     * get italic font path
     *
     * @return string
     */
    protected function getItalicFont()
    {
        return Mage::getStoreConfig(
            Blugento_Pdf_Helper_Data::XML_PATH_ITALIC_FONT
        );
    }

    /**
     * check whether italic font file exists
     *
     * @return bool
     */
    protected function ItalicFontFileExists()
    {
        return file_exists($this->getItalicFontFile());
    }

    /**
     * get italic font file
     *
     * @return string
     */
    protected function getItalicFontFile()
    {
        return Mage::helper('blugento_pdf')->getFontPath() . DS . $this->getItalicFont();
    }


    /**
     * get the regular font path
     *
     * @return string
     */
    protected function getRegularFont()
    {
        return Mage::getStoreConfig(Blugento_Pdf_Helper_Data::XML_PATH_REGULAR_FONT);
    }

    /**
     * check whether font file exists for regular font
     *
     * @return bool
     */
    protected function regularFontFileExists()
    {
        return file_exists($this->getRegularFontFile());
    }

    /**
     * get the path to the font file for regular font
     *
     * @return string
     */
    protected function getRegularFontFile()
    {
        return Mage::helper('blugento_pdf')->getFontPath() . DS . $this->getRegularFont();
    }

    /**
     * @return string
     */
    private function getPageSize()
    {
        return Mage::helper('blugento_pdf')->getPageSizeConfigPath();
    }

    /**
     * Get maximum width of fields
     *
     * @param $fields
     * @param $font
     * @param $fontSize
     * @return float|int
     */
    protected function _getMaxWidthFields($fields, $font, $fontSize)
    {
        $max = 0;
        foreach ($fields as $key => $val) {
            $width = $this->widthForStringUsingFontSize($val, $font, $fontSize);
            if ($width > $max) {
                $max = $width;
            }
        }
        return $max;
    }
}
