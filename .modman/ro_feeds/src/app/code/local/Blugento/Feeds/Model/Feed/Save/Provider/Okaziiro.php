<?php
/**
 * Blugento Feeds
 * Model Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Adminmenu
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Feeds_Model_Feed_Save_Provider_Okaziiro extends Blugento_Feeds_Model_Feed_Save_Xml
{
    /**
     * Definition filename
     * @var string
     */
    protected $_definitionFilename = 'okaziiro.xml';

    /**
     * Get string content
     * @param array $content
     * @return string
     */
    public function buildXMLString(array $content)
    {
        $xmlString = '<?xml version="1.0" encoding="UTF-8"?>';
        $xmlString .= '<OKAZII>';

        $split_content = array_chunk($content, 100);

        foreach ($split_content as $groups) {
            foreach ($groups as $product) {
                try {
                    $xmlString .= '<AUCTION>';

                    if (strlen($product['description']) < 3) {
                        $product['description'] = $product['title'];
                    }

                    if (!$product['category']) {
                        $product['category'] = '-';
                    }

                    $xmlString .= '<UNIQUEID>' . $product['unique_id'] . '</UNIQUEID>';
                    $xmlString .= '<SKU>' . $product['unique_id'] . '</SKU>';
                    $xmlString .= '<TITLE><![CDATA[ ' . $product['title'] . ' ]]></TITLE>';
                    $xmlString .= '<CATEGORY>' . $product['category'] . '</CATEGORY>';
                    $xmlString .= '<DESCRIPTION><![CDATA[ ' . $product['description'] . ' ]]></DESCRIPTION>';
                    $xmlString .= '<PRICE>' . $product['price'] . '</PRICE>';


                    if ($product['discount_price']) {
                        $xmlString .= '<DISCOUNT_PRICE>' . $product['discount_price'] . '</DISCOUNT_PRICE>';
                    }

                    $xmlString .= '<CURRENCY>' . $product['currency'] . '</CURRENCY>';

                    if ($product['brand']) {
                        $xmlString .= '<BRAND>' . $product['brand'] . '</BRAND>';
                    }

                    if ($product['images']) {
                        $xmlString .= '<PHOTOS>';
                        foreach ($product['images'] as $photoURL) {
                            $xmlString .= '<URL>' . $photoURL . '</URL>';
                        }
                        $xmlString .= '</PHOTOS>';
                    }

                    if ($product['attributes']) {
                        $xmlString .= '<ATTRIBUTES>';
                        foreach ($product['attributes'] as $attribute) {
                            $xmlString .= '<' . strtoupper($this->slugify($attribute['code'])) . '>' . $attribute['value'] . '</' . strtoupper($this->slugify($attribute['code'])) . '>';
                        }
                        $xmlString .= '</ATTRIBUTES>';
                    }
                    $amount = $product['amount'];
                    if ($product['stocks']) {
                        $amount = 0;
                        $xmlString .= '<STOCKS>';
                        foreach ($product['stocks'] as $stock) {
                            $amount += $stock['amount'];
                            if ($stock['amount'] > 999) {
                                $stock['amount'] = 999;
                            }
                            if ($stock['amount'] < 1) {
                                $stock['amount'] = 0;
                            }
                            $xmlString .= '<STOCK>';
                            $xmlString .= '<AMOUNT>' . $stock['amount'] . '</AMOUNT>';
                            foreach ($stock['attributes'] as $attribute) {
                                $xmlString .= '<' . str_replace(' ','',strtoupper($attribute['code'])) . '>' . $attribute['value'] . '</' . str_replace(' ','',strtoupper($attribute['code'])) . '>';
                            }
                            $xmlString .= '</STOCK>';
                        }
                        $xmlString .= '</STOCKS>';
                    }
                    if ($amount > 999) {
                        $amount = 999;
                    }

                    $xmlString .= '<AMOUNT>' . $amount . '</AMOUNT>';

                    if ($product['delivery_time']) {
                        $xmlString .= '<DELIVERY>';
                        $xmlString .= '<DELIVERY_TIME>' . $product['delivery_time'] . '</DELIVERY_TIME>';
                        $xmlString .= '</DELIVERY>';
                    }

                    $xmlString .= '</AUCTION>';
                } catch (Exception $e) {
                    Mage::logException($e);
                    continue;
                }
            }
        }

        $xmlString .= '</OKAZII>';

        return $xmlString;
    }
}
