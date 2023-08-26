<?php
/**
 * @package   Blugento_Pdf
 * @copyright 2016 Blugento Team
 */
/**
 * Default item model rewrite.
 *
 * @category  Blugento
 * @package   Blugento_Pdf
 * @author    Blugento Team <team@blugento.com>
 */
class Blugento_Pdf_Model_Items_Default extends Mage_Sales_Model_Order_Pdf_Items_Invoice_Default
{
    public $indents = array(
        25, 130, 135
    );
    public $fontSizes = array(
        'regular' => 9,
        'small' => 7
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
        $lines = array();

        $fontSize = $this->fontSizes['regular'];
        $fontSizeSmall = $this->fontSizes['small'];
        $um = $this->getUMValue($item);

        // draw Position Number
        $lines[0] = array(
            array(
                'text'      => $position,
                'feed'      => $pdf->margin['left'] + 10,
                'align'     => 'right',
                'font_size' => $fontSize
            )
        );

        // draw SKU
        $lines[0][] = array(
            'text' => Mage::helper('core/string')->str_split($this->getSku($item), 19),
            'feed' => $pdf->margin['left'] + $this->indents[0],
            'font_size' => $fontSize
        );

        // draw Product name
        $lines[0][] = array(
            'text' => Mage::helper('core/string')->str_split($item->getName(), 40, true, true),
            'feed' => $pdf->margin['left'] + $this->indents[1],
            'font_size' => $fontSize
        );

        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                $optionTxt = $option['label'] . ': ';
                // append option value
                if (isset($option['value'])) {
                    $optionTxt .= isset($option['print_value']) ? $option['print_value'] : strip_tags($option['value']);
                }
                $optionArray = $pdf->_prepareText($optionTxt, $page, $pdf->getFontRegular(), $fontSize, 215);
                $lines[][] = array(
                    'text' => $optionArray,
                    'feed' => $pdf->margin['left'] + $this->indents[2],
                    'font_size' => $fontSizeSmall
                );
            }
        }

        $show_delivery = Mage::getStoreConfig('sales_pdf/invoice/show_delivery_time');
        if ($show_delivery == 1) {
            $storeId = Mage::app()->getStore()->getStoreId();
            $delivery_time = Mage::getResourceModel('catalog/product')->getAttributeRawValue($item->getProductId(), 'delivery_time', $storeId);
            if ($delivery_time) {
                $optionTxt = Mage::helper('blugento_pdf')->__('Delivery Time:') . ' ' . $delivery_time;
                $optionArray = $pdf->_prepareText($optionTxt, $page, $pdf->getFontRegular(), $fontSize, 215);
                $lines[][] = array(
                    'text' => $optionArray,
                    'feed' => $pdf->margin['left'] + $this->indents[2],
                    'font_size' => $fontSizeSmall
                );
            }
        }

        $columns = array();

        $tax = $item->getTaxAmount() + $item->getHiddenTaxAmount();
        $qty = $item->getQty() * 1;

        // prepare UM
        $columns['measuring_unit'] = array(
            'text'      => $um,
            'align'     => 'right',
            'font_size' => $fontSize,
            '_width'    => 30
        );

        // prepare old price
        $columns['old_price'] = array(
            'text'      => number_format(Mage::getModel('catalog/product')->load($item->getProductId())->getPrice(), 2),
            'align'     => 'right',
            'font_size' => $fontSize,
            '_width'    => 60
        );

        // prepare qty
        $columns['qty'] = array(
            'text'      => $qty,
            'align'     => 'right',
            'font_size' => $fontSize,
            '_width'    => 30
        );

        // prepare price
        $columns['price'] = array(
            'text'      => $order->formatPriceTxt($item->getPrice()),
            'align'     => 'right',
            'font_size' => $fontSize,
            '_width'    => 60
        );

        // prepare price_incl_tax
        $columns['price_incl_tax'] = array(
            'text'      => $order->formatPriceTxt($item->getPriceInclTax()),
            'align'     => 'right',
            'font_size' => $fontSize,
            '_width'    => 60
        );

        // prepare price_excl_tax
        $columns['price_excl_tax'] = array(
            'text'      => $order->formatPriceTxt($item->getPriceInclTax() - $tax/$qty),
            'align'     => 'right',
            'font_size' => $fontSize,
            '_width'    => 60
        );

        // prepare tax
        $columns['tax'] = array(
            'text'      => $order->formatPriceTxt($tax),
            'align'     => 'right',
            'font_size' => $fontSize,
            '_width'    => 50
        );

        // prepare tax_rate
        $columns['tax_rate'] = array(
            'text'      => round($item->getOrderItem()->getTaxPercent(), 2) . '%',
            'align'     => 'right',
            'font_size' => $fontSize,
            '_width'    => 50
        );

        // prepare subtotal
        $columns['subtotal'] = array(
            'text'      => $order->formatPriceTxt($item->getRowTotal()),
            'align'     => 'right',
            'font_size' => $fontSize,
            '_width'    => 50
        );

        // prepare subtotal_incl_tax
        $columns['subtotal_incl_tax'] = array(
            'text'      => $order->formatPriceTxt($item->getRowTotalInclTax()),
            'align'     => 'right',
            'font_size' => $fontSize,
            '_width'    => 70
        );

        // prepare subtotal_excl_tax
        $columns['subtotal_excl_tax'] = array(
            'text'      => $order->formatPriceTxt($item->getRowTotalInclTax() - $tax),
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
                $lines[0][] = $column;
            }
        }

        if (Mage::getStoreConfig('sales_pdf/invoice/show_item_discount') && 0 < $item->getDiscountAmount()) {
            // print discount
            $text = Mage::helper('blugento_pdf')->__(
                'You get a discount of %s.',
                $order->formatPriceTxt($item->getDiscountAmount())
            );
            $lines[][] = array(
                'text'  => $text,
                'align' => 'right',
                'feed'  => $pdf->margin['right'] - $columnOffset
            );
        }

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 15
        );

        $page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $this->setPage($page);
    }

    private function getUMValue($item){

        $product = Mage::getModel('catalog/product')->load($item->getProductId());
        $attribute = $product->getResource()->getAttribute('measuring_unit');
        if (!$attribute){
            $attributeValue = 'pcs';
        }
        else {
            $attributeValue = $attribute->getDefaultValue();
        }

        return $attributeValue;
    }

}
