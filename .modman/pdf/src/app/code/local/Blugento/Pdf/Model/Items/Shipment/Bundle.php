<?php
/**
 * @package   Blugento_Pdf
 * @copyright 2016 Blugento Team
 */
/**
 * Shipment bundle item model.
 *
 * @category  Blugento
 * @package   Blugento_Pdf
 * @author    Blugento Team <team@blugento.com>
 */
class Blugento_Pdf_Model_Items_Shipment_Bundle extends Mage_Bundle_Model_Sales_Order_Pdf_Items_Shipment
{
    /**
     * Draw item line.
     *
     * @return void
     */
    public function draw()
    {
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();

        $shipItems = $this->getChilds($item);
        $items = array_merge(array($item->getOrderItem()), $item->getOrderItem()->getChildrenItems());

        $_prevOptionId = '';
        $drawItems = array();

        foreach ($items as $_item) {
            $line   = array();

            $attributes = $this->getSelectionAttributes($_item);
            if (is_array($attributes)) {
                $optionId   = $attributes['option_id'];
            } else {
                $optionId = 0;
            }

            if (!isset($drawItems[$optionId])) {
                $drawItems[$optionId] = array(
                    'lines'  => array(),
                    'height' => 15
                );
            }

            if ($_item->getParentItem()) {
                if ($_prevOptionId != $attributes['option_id']) {
                    $line[0] = array(
                        'font'  => 'italic',
                        'text'  => Mage::helper('core/string')->str_split($attributes['option_label'], 60, true, true),
                        'feed'  => 150
                    );

                    $drawItems[$optionId] = array(
                        'lines'  => array($line),
                        'height' => 15
                    );

                    $line = array();

                    $_prevOptionId = $attributes['option_id'];
                }
            }

            // draw SKUs
            if (!$_item->getParentItem()) {
                $text = array();
                foreach (Mage::helper('core/string')->str_split($_item->getSku(), 20) as $part) {
                    $text[] = $part;
                }
                $line[] = array(
                    'text'  => $text,
                    'feed' => 45,
                    'width' => 85
                );
            }

            // draw Name
            if ($_item->getParentItem()) {
                $name = $this->getValueHtml($_item);
            } else {
                $name = $_item->getName();
            }
            $text = array();
            foreach (Mage::helper('core/string')->str_split($name, 60, true, true) as $part) {
                $text[] = $part;
            }
            $line[] = array(
                'text'  => $text,
                'feed'  => 150,
                'align' => 'left',
                'width' => 375
            );

            if (($this->isShipmentSeparately() && $_item->getParentItem())
                || (!$this->isShipmentSeparately() && !$_item->getParentItem())
            ) {
                if (isset($shipItems[$_item->getId()])) {
                    $qty = $shipItems[$_item->getId()]->getQty()*1;
                } elseif ($_item->getIsVirtual()) {
                    $qty = Mage::helper('bundle')->__('N/A');
                } else {
                    $qty = 0;
                }
            } else {
                $qty = '';
            }

            $line[] = array(
                'text'  => $qty,
                'feed'  => 505,
                'align' => 'left',
                'width' => 10
            );

            $drawItems[$optionId]['lines'][] = $line;
        }

        // custom options
        $options = $item->getOrderItem()->getProductOptions();
        if ($options) {
            if (isset($options['options'])) {
                foreach ($options['options'] as $option) {
                    $lines = array();
                    $lines[][] = array(
                        'text'  => Mage::helper('core/string')->str_split(strip_tags($option['label']), 70, true, true),
                        'font'  => 'italic',
                        'feed'  => 60
                    );

                    if (isset($option['value'])) {
                        $text = array();
                        $_printValue = isset($option['print_value'])
                            ? $option['print_value']
                            : strip_tags($option['value']);
                        $values = explode(', ', $_printValue);
                        foreach ($values as $value) {
                            foreach (Mage::helper('core/string')->str_split($value, 50, true, true) as $_value) {
                                $text[] = $_value;
                            }
                        }

                        $lines[][] = array(
                            'text'  => $text,
                            'feed'  => 65
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
}
