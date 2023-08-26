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
class Blugento_FullFeed_Model_Api2_Feed_Rest_Admin_V1 extends Blugento_FullFeed_Model_Api2_Feed
{
    /**
     * Return feed file from /media/fullfeed
     *
     * @return array
     */
    protected function _retrieve()
    {
        /** @var Blugento_FullFeed_Helper_Data $helper */
        $helper = Mage::helper('blugento_fullfeed');

        try {
            $feedName = $this->getRequest()->getParam('feed_file');
            $feedDir = Mage::getBaseDir('media') . DS . 'fullfeed' . DS . $feedName . '.xml';
            $feedUrl = Mage::getBaseUrl('media') . 'fullfeed' . DS . $feedName . '.xml';

            if (!file_exists($feedDir)) {
                $products = array (
                    'products_feed' => 'There is no feed file with name: "' . $feedName . '.xml"'
                );
            } else {
                $content = file_get_contents($feedUrl);
                $content = $helper->replaceDiacritics($content);

                $xmlObj = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
                $out = $helper->xml2array($xmlObj);

                $products = array (
                    'products_feed' => $out['product']
                );
            }

            return $products;
        } catch (Mage_Api2_Exception $e) {
            $this->_errorMessage($e->getMessage(), $e->getCode());
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }
}