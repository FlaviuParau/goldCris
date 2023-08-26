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
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */

class Facebook_AdsExtension_Block_Adminhtml_System_Config_Feedurl extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Returns html part of the setting
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $baseUrl = str_replace('cdn.', '', Mage::getBaseUrl('media'));
        $tsvUrl = $baseUrl . 'facebook_adstoolbox_product_feed.tsv';
        $xmlUrl = $baseUrl . 'facebook_adstoolbox_product_feed.xml';

        $baseDir = Mage::getBaseDir('media');
        $tsvDir = $baseDir . DS . 'facebook_adstoolbox_product_feed.tsv';
        $xmlDir = $baseDir . DS . 'facebook_adstoolbox_product_feed.xml';

        if (file_exists($tsvDir)) {
            return "<a href='$tsvUrl' target='_blank' >".   $tsvUrl . "</a>";
        } else if (file_exists($xmlDir)) {
            return "<a href='$xmlUrl' target='_blank' >".   $xmlUrl . "</a>";
        } else {
            return $this->__('File not found, generate the feed first.');
        }
    }
}
