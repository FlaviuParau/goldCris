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

class Blugento_Feeds_Model_Feed_Save_Provider_Compariro extends Blugento_Feeds_Model_Feed_Save_Xml
{
    /**
     * Definition filename
     * @var string
     */
    protected $_definitionFilename = 'compariro.xml';

    /**
     * Get string content
     * @param array $content
     * @return string
     */
    public function buildXMLString(array $content)
    {
        $XML = $this->createElement('products');

        $cdata = array(
            'manufacturer',
            'name',
            'category',
            'description'
        );

        foreach ($content as $product) {

            try {
                $XMLProduct = $this->createElement('product');

                foreach ($product as $key => $value) {
                    if (in_array($key, $cdata)) {
                        $XMLProduct->appendChild($this->createElement($key))->appendChild($this->_XMLDocument->createCDATASection($value));
                    } else {
                        $XMLProduct->appendChild($this->createElement($key, $value));
                    }
                }

                $XML->appendChild($XMLProduct);
            } catch (Exception $e) {
                continue;
            }
        }

        $this->_XMLDocument->appendChild($XML);

        return $this->_XMLDocument->saveXML();
    }
}
