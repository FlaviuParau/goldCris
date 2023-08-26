<?php

class Blugento_Feeds_Model_Feed_Save_Provider_Glami extends Blugento_Feeds_Model_Feed_Save_Xml
{
	/**
	 * Definition filename
	 *
	 * @var string
	 */
	protected $_definitionFilename = 'glami.xml';
	
	/**
	 * Get string content
	 *
	 * @param array $content
	 * @return string
	 * @throws Exception
	 */
	public function buildXMLString(array $content)
	{
		$XML = $this->createElement('SHOP');
		
		$cdata = array(
			'PRODUCTNAME',
			'CATEGORYTEXT',
			'DESCRIPTION'
		);
		
		$extraParams = array(
			'MATERIAL',
			'MARIME',
            'CULOARE'
		);
		
		$param = new stdClass();
		
		foreach ($content as $product) {
			try {
				$XMLProduct = $this->createElement('SHOPITEM');
				
				foreach ($product as $key => $value) {
                    if (in_array($key, $cdata)) {
                        $XMLProduct->appendChild($this->createElement($key))->appendChild($this->_XMLDocument->createCDATASection($value));
                    } elseif (in_array($key, $extraParams)) {
                        if ($key == 'MATERIAL') {
                            $param = $this->createElement('PARAM');
                            foreach ($value as $data => $item) {
                                $param->appendChild($this->createElement($data, $item));
                            }
                        }
                        if ($key == 'MARIME') {
                            $param = $this->createElement('PARAM');
                            foreach ($value as $data => $item) {
                                $param->appendChild($this->createElement($data, $item));
                            }
                        }
                        if ($key == 'CULOARE') {
                            $param = $this->createElement('PARAM');
                            foreach ($value as $data => $item) {
                                $param->appendChild($this->createElement($data, $item));
                            }
                        }
                        $XMLProduct->appendChild($param);
                    } elseif ($key == 'IMGURL_ALTERNATIVE') {
                        foreach ($value as $v) {
                            $XMLProduct->appendChild($this->createElement($key, $v));
                        }
                    } else {
						$XMLProduct->appendChild($this->createElement($key, $value));
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
