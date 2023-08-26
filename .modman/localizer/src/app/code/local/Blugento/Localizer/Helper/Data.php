<?php
/**
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
 * @package     Blugento_Localizer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Localizer_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_EU_COUNTRIES_LIST = 'general/country/eu_countries';

    public function isAdmin()
    {
        if (Mage::app()->getStore()->isAdmin() || Mage::getDesign()->getArea() == 'adminhtml') {
            return true;
        }

        return false;
    }

    /**
     * Check whether specified country is in EU countries list
     *
     * @param  string $countryCode Country Code
     * @return bool Flag if country is an EU country
     */
    public function isCountryInEU($countryCode)
    {
        return in_array(strtoupper($countryCode), $this->getEUCountries());
    }

    /**
     * Get countries in the EU
     *
     * @return array EU Countries
     */
    public function getEUCountries()
    {
        return explode(',', Mage::getStoreConfig(self::XML_PATH_EU_COUNTRIES_LIST));
    }

    /**
     * Get url of agreement view for checkout
     *
     * @param  Mage_Checkout_Model_Agreement $agreement Agreement
     * @return string URL for the given agreement
     */
    public function getAgreementUrl(Mage_Checkout_Model_Agreement $agreement)
    {
        return Mage::getUrl('blugento_localizer/frontend/agreements', array('id' => $agreement->getId()));
    }

    /**
     * Get current website id and store id in admin panel
     *
     * @return array
     */
    public function getConfigScopeStoreId()
    {
        $website_id = -1;
        $store_id   = 0;

        $data = array(
            $store_id,
            $website_id,
            'default',
            'default'
        );
        if (strlen($code = Mage::getSingleton('adminhtml/config_data')->getStore())) {
            // store level
            try {
                $data[0] = Mage::getModel('core/store')->load($code)->getId();
                $data[2] = $code;
            } catch (Exception $e) {
                Mage::logException($e);
            }
        } else
        if (strlen($code = Mage::getSingleton('adminhtml/config_data')->getWebsite())) {
            // website level
            try {
                $website_id = Mage::getModel('core/website')->load($code)->getId();
                $data[0] = Mage::app()->getWebsite($website_id)->getDefaultStore()->getId();
                $data[1] = $website_id;
                $data[2] = Mage::app()->getWebsite($website_id)->getDefaultStore()->getCode();
                $data[3] = $code;
            } catch (Exception $e) {
                Mage::logException($e);
            }
        } else {
            // default level
            $data[0] = 0;
        }

        return $data;
    }

    /**
     * Generate URL to configured shipping cost page, or '' if none.
     *
     * @return string Shipping cost url
     */
    public function getShippingCostUrl()
    {
        /** @var $cmsPage Mage_Cms_Model_Page */
        $cmsPage = Mage::getModel('cms/page')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load(Mage::getStoreConfig('catalog/price/cms_page_shipping'));

        if (!$cmsPage->getId() || !$cmsPage->getIsActive()) {
            return '';
        }

        return Mage::helper('cms/page')->getPageUrl($cmsPage->getId());
    }

    /**
     * Get Shipping cost html
     *
     * @return string Shipping cost html
     */
    public function getShippingCostHtml()
    {
        /** @var $cmsPage Mage_Cms_Model_Page */
        $cmsPage = Mage::getModel('cms/page')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load(Mage::getStoreConfig('catalog/price/cms_page_shipping'));

        if (!$cmsPage->getId() || !$cmsPage->getIsActive()) {
            return '';
        }

        return $cmsPage->getContent();
    }

    /**
     * Returns whether or not the price includes shipping taxes
     *
     * @return bool Flag if price includes shipping taxes
     */
    public function isShippingIncludedInPrice()
    {
        return Mage::getStoreConfig('catalog/price/including_shipping_costs');
    }

    /**
     * Get Store name
     *
     * @return string Store name
     */
    public function getStoreName()
    {
        $_siteName = Mage::getStoreConfig('blugentolocalizer/imprint/shop_name');
        if ( ! $_siteName) {
            $_siteName = Mage::getStoreConfig('general/store_information/name');
        }
        return $_siteName;
    }
}
