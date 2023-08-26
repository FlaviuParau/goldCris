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
 * @package     Blugento_RobotsEditor
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_RobotsEditor_Model_System_Config_Form_Robots extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave()
    {
        $content = $this->getValue();
        $contArray = explode(PHP_EOL, $content);

        try {
            $url = Mage::getBaseUrl() . 'sitemap.xml';
            $fileName = 'robots_base';

            $storeCode = $this->getStoreCode();
            if ($storeCode && $storeCode !='default') {
                $storeId = Mage::getModel('core/store')->load($storeCode, 'code')->getId();
                $url = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . 'sitemap.xml';
                $fileName = 'robots_' . $this->getStoreCode();
            } else {
                $fp = fopen('robots.txt','w');

                $contentN = $content;
                if (end($contArray) != $url) {
                    $contentN = $content . PHP_EOL  . $url;
                }
                fwrite($fp, $contentN);
                fclose($fp);
            }

            if (end($contArray) != $url) {
                $content .= PHP_EOL  . $url;
            }

            $this->setValue($content);

            $fp = fopen($fileName. '.txt','w');
            fwrite($fp, $content);
            fclose($fp);
        } catch (Exception $e) {
            Mage::throwException($e->getMessage());
        }
    }
}
