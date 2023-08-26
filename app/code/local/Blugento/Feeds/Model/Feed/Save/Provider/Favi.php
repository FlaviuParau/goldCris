<?php

class Blugento_Feeds_Model_Feed_Save_Provider_Favi extends Blugento_Feeds_Model_Feed_Save_Xml
{
    /**
     * Definition filename
     *
     * @var string
     */
    protected $_definitionFilename = 'favi.xml';

    /**
     * Get string content
     *
     * @param array $content
     * @return string
     * @throws Exception
     */
    public function buildXMLString(array $content)
    {
        $XML = $this->createElement('Products');

        $extraAttributes = Mage::getModel('blugento_feeds/feed_provider_favi')->getAttributesMap();

        foreach ($content as $product) {
            try {
                $XMLProduct = $this->createElement('Product');
                $param = $this->createElement('Attributes');

                foreach ($product as $key => $value) {
                    if (!in_array($key, $extraAttributes)) {
                        $XMLProduct->appendChild($this->createElement($key, $value));
                    } else {
                        $childParam = $this->createElement('Attribute');
                        $childParam->appendChild($this->createElement('Attribute_name', $key));
                        $childParam->appendChild($this->createElement('Attribute_value', $value));
                        $param->appendChild($childParam);

                        $XMLProduct->appendChild($param);
                    }
                }
                $XML->appendChild($XMLProduct);
            } catch (Exception $e) {
                throw $e;
            }
        }

        $this->_XMLDocument->appendChild($XML);

        return $this->_XMLDocument->saveXML();
    }
}
