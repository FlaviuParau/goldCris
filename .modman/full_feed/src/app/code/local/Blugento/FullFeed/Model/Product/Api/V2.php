<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @category    Blugento
 * @package     Blugento_FullFeed
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Blugento_FullFeed_Model_Product_Api_V2 extends Blugento_FullFeed_Model_Product_Api
{
    /**
     * Return feed file from /media/fullfeed
     *
     * @param string $file
     * @return false|array
     */
    public function retrieve($file)
    {
        /** @var Blugento_FullFeed_Helper_Data $helper */
        $helper = Mage::helper('blugento_fullfeed');

        try {
            $fileDir = Mage::getBaseDir('media') . DS . 'fullfeed' . DS . $file . '.xml';
            $fileUrl = Mage::getBaseUrl('media') . 'fullfeed' . DS . $file . '.xml';

            if (!file_exists($fileDir)) {
                $this->_fault('data_not_exists');
            }
            $content = file_get_contents($fileUrl);

            $xmlObj = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
            $xml = $helper->xml2array($xmlObj);

            $xmlStr = new SimpleXMLElement('<products></products>');

            $helper->array2xml($xml['product'], $xmlStr);

            return $xmlStr->asXML();
        } catch (Mage_Api2_Exception $e) {
            $this->_errorMessage($e->getMessage(), $e->getCode());
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }
}