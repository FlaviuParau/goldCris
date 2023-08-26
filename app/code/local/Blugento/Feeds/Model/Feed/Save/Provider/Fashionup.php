<?php

class Blugento_Feeds_Model_Feed_Save_Provider_Fashionup extends Blugento_Feeds_Model_Feed_Save_Xml
{
    /**
     * Definition filename
     *
     * @var string
     */
    protected $_definitionFilename = 'fashionup.xml';

    /**
     * Get string content
     *
     * @param array $content
     * @return string
     * @throws Exception
     */
    public function buildXMLString(array $content)
    {
        $XML = $this->createElement('lista_produse');

        foreach ($content as $product) {
            try {
                $xmlProduct = $this->createElement('produs');

                foreach ($product as $key => $value) {
                    if ($key == 'marimi') {
                        $stock = $this->createAttribute('stoc', $value['stoc']);
                        $marime = $this->createElement('marime', $value['marime']);
                        $marimi = $this->createElement('marimi');

                        $marime->appendChild($stock);
                        $marimi->appendChild($marime);
                        $xmlProduct->appendChild($marimi);
                    } elseif ($key == 'imagini') {
                        $imagini = $this->createElement('imagini');
                        foreach ($value as $v) {
                            $order = $this->createAttribute('ord', $v['order']);
                            $imagine = $this->createElement('imagine', $v['imagine']);

                            $imagine->appendChild($order);
                            $imagini->appendChild($imagine);
                            $xmlProduct->appendChild($imagini);
                        }
                    } elseif ($key == 'descriere') {
                        $xmlProduct->appendChild($this->createElement($key))->appendChild($this->_XMLDocument->createCDATASection($value));
                    } else {
                        $xmlProduct->appendChild($this->createElement($key, $value));
                    }
                }
                $XML->appendChild($xmlProduct);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        $this->_XMLDocument->appendChild($XML);

        return $this->_XMLDocument->saveXML();
    }
}
