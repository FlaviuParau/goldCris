<?php
/**
 * @package   Blugento_Pdf
 * @copyright 2016 Blugento Team
 */
/**
 * Bundle item model rewrite.
 *
 * @category  Blugento
 * @package   Blugento_Pdf
 * @author    Blugento Team <team@blugento.com>
 */
class Blugento_Pdf_Model_Items_Ro_Creditmemo_Bundle extends Mage_Bundle_Model_Sales_Order_Pdf_Items_Invoice
{
    public $indents = array(
        25, 35, 45
    );
    public $fontSizes = array(
        'regular' => 9,
        'small' => 8
    );

    /**
     * Draw item line.
     *
     * @param  int $position position of the product
     *
     * @return void
     */
    public function draw($position = 1)
    {
        $order = $this->getOrder();
        $item = $this->getItem();
        $pdf = $this->getPdf();
        $page = $this->getPage();

        $fontSize = $this->fontSizes['regular'];
        $fontSizeSmall = $this->fontSizes['small'];

        $items = $this->getChilds($item);

        $_prevOptionId = '';
        $drawItems = array();

        foreach ($items as $_item) {
            $line = array();

            $attributes = $this->getSelectionAttributes($_item);
            if (is_array($attributes)) {
                $optionId = $attributes['option_id'];
            } else {
                $optionId = 0;
            }

            if (!isset($drawItems[$optionId])) {
                $drawItems[$optionId] = array(
                    'lines'  => array(),
                    'height' => 15
                );
            }

            if ($_item->getOrderItem()->getParentItem()) {
                if ($_prevOptionId != $attributes['option_id']) {
                    $line[0] = array(
                        'font'  => 'italic',
                        'text'  => Mage::helper('core/string')->str_split($attributes['option_label'], 45, true, true),
                        'feed'  => $pdf->margin['left'] + $this->indents[1],
                        'font_size' => $fontSizeSmall
                    );

                    $drawItems[$optionId] = array(
                        'lines'  => array($line),
                        'height' => 15
                    );

                    $line = array();

                    $_prevOptionId = $attributes['option_id'];
                }
            }

            // draw Position Number
            $line[] = array(
                'text'      => $position,
                'feed'      => $pdf->margin['left'] + 10,
                'align'     => 'right',
                'font_size' => $fontSize
            );

            /* in case Product name is longer than 80 chars - it is written in a few lines */
            if ($_item->getOrderItem()->getParentItem()) {
                $name = $this->getValueHtml($_item);
            } else {
                $name = $_item->getName();
            }
            $line[] = array(
                'text'  => Mage::helper('core/string')->str_split($name, 35, true, true),
                'feed'  => $pdf->margin['left'] + $this->indents[1],
                'font_size' => $fontSize
            );

            // draw SKUs
            if (!$_item->getOrderItem()->getParentItem()) {

                // prepare sku
                $columns['sku'] = array(
                    'text'      => Mage::helper('core/string')->str_split($this->getSku($item), 17),
                    'align'     => 'right',
                    'font_size' => $fontSize,
                    '_width'    => $pdf->margin['left'] + 25
                );
            }

            // draw prices
            if ($this->canShowPriceInfo($_item)) {
                $columns = array();
                $qty = $_item->getQty();
                $tax = $_item->getTaxAmount() + $_item->getHiddenTaxAmount();

                // prepare qty
                $columns['qty'] = array(
                    'text'      => $qty * -1,
                    'align'     => 'right',
                    'font_size' => $fontSize,
                    '_width' => 30
                );

                // prepare price
                $columns['price'] = array(
                    'text'      => $_item->getPrice(),
                    'align'     => 'right',
                    'font_size' => $fontSize,
                    '_width'    => 60
                );

                // prepare price_incl_tax
                $columns['price_incl_tax'] = array(
                    'text'      => $_item->getPriceInclTax(),
                    'align'     => 'right',
                    'font_size' => $fontSize,
                    '_width'    => 60
                );

                // prepare price_excl_tax
                $columns['price_excl_tax'] = array(
                    'text'      => $this->formatTxtPrice($_item->getPriceInclTax() - $tax/$qty),
                    'align'     => 'right',
                    'font_size' => $fontSize,
                    '_width'    => 60
                );

                // prepare tax
                $columns['tax'] = array(
                    'text'      => $tax,
                    'align'     => 'right',
                    'font_size' => $fontSize,
                    '_width'    => 50
                );

                // prepare tax_rate
                $columns['tax_rate'] = array(
                    'text'      => round($_item->getOrderItem()->getTaxPercent(), 2),
                    'align'     => 'right',
                    'font_size' => $fontSize,
                    '_width'    => 50
                );

                // prepare subtotal
                $columns['subtotal'] = array(
                    'text'      => $_item->getRowTotal() * -1,
                    'align'     => 'right',
                    'font_size' => $fontSize,
                    '_width'    => 50
                );

                // prepare subtotal_incl_tax
                $columns['subtotal_incl_tax'] = array(
                    'text'      => $_item->getRowTotalInclTax() * -1,
                    'align'     => 'right',
                    'font_size' => $fontSize,
                    '_width'    => 70
                );

                // prepare subtotal_excl_tax
                $columns['subtotal_excl_tax'] = array(
                    'text'      => $this->formatTxtPrice(($_item->getRowTotalInclTax() - $tax) * -1),
                    'align'     => 'right',
                    'font_size' => $fontSize,
                    '_width'    => 70
                );

                // draw columns in specified order
                $columnsOrder = explode(',', Mage::getStoreConfig('sales_pdf/invoice/item_price_column_order'));
                // draw starting from right
                $columnsOrder = array_reverse($columnsOrder);
                $columnOffset = 0;
                foreach ($columnsOrder as $columnName) {
                    $columnName = trim($columnName);
                    if (array_key_exists($columnName, $columns)) {
                        $column = $columns[$columnName];
                        $column['feed'] = $pdf->margin['right'] - $columnOffset;
                        $columnOffset += $column['_width'];
                        unset($column['_width']);
                        $line[] = $column;
                    }
                }
            }

            $drawItems[$optionId]['lines'][] = $line;
        }

        // custom options
        $options = $item->getOrderItem()->getProductOptions();
        if ($options) {
            if (isset($options['options'])) {
                foreach ($options['options'] as $option) {
                    $lines = array();
                    $lines[][] = array(
                        'text' => Mage::helper('core/string')->str_split(strip_tags($option['label']), 40, true, true),
                        'font' => 'italic',
                        'feed' => 35,
                        'font_size' => $fontSizeSmall
                    );

                    if (isset($option['value'])) {
                        $text = array();
                        $_printValue = isset($option['print_value']) ? $option['print_value'] : strip_tags($option['value']);
                        $values = explode(', ', $_printValue);
                        foreach ($values as $value) {
                            foreach (Mage::helper('core/string')->str_split($value, 30, true, true) as $_value) {
                                $text[] = $_value;
                            }
                        }

                        $lines[][] = array(
                            'text' => $text,
                            'feed' => 40,
                            'font_size' => $fontSizeSmall
                        );
                    }

                    $drawItems[] = array(
                        'lines'  => $lines,
                        'height' => 15
                    );
                }
            }
        }

        $page = $pdf->drawLineBlocks($page, $drawItems, array('table_header' => true));
        $this->setPage($page);
    }

    /**
     * Returns the formatted price
     *
     * @param float $price
     * @param null|array $options
     * @return string
     */
    public function formatTxtPrice($price)
    {
        if (!is_numeric($price)) {
            $price = Mage::app()->getLocale()->getNumber($price);
        }
        /**
         * Fix problem with 12 000 000, 1 200 000
         *
         * %f - the argument is treated as a float, and presented as a floating-point number (locale aware).
         * %F - the argument is treated as a float, and presented as a floating-point number (non-locale aware).
         */
        $price = sprintf("%F", $price);
        if ($price == -0) {
            $price = 0;
        }
        return round($price, 2);
    }
}
