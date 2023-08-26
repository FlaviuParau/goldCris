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

/**
 * Block to retrieve data from imprint config.
 */
class Blugento_Localizer_Block_Imprint_Content extends Mage_Core_Block_Template
{
    /**
     * Constructor to set config store view.
     */
    public function __construct()
    {
        $storeId = $this->getStoreId();
        $this->setData(Mage::getStoreConfig('blugentolocalizer/imprint', $storeId));
    }

    /**
     * Set StoreId to get impressum data for this store.
     *
     * @param int $storeId Store id.
     */
    public function setStoreId($storeId)
    {
        $this->setData(Mage::getStoreConfig('blugentolocalizer/imprint', $storeId));
    }

    /**
     * Getting StoreId to get proper store related
     * information in order comments.
     *
     * @return int|null Store Id
     */
    protected function getStoreId()
    {
        $orderId = $this->getRequest()->getParam('order_id', 0);
        if ($orderId > 0) {
            return Mage::getSingleton('sales/order')->load($orderId)->getStoreId();
        }

        return null;
    }

    /**
     * Retrieve the setting "website". If parameter checkForProtocol is true,
     * check if there is a valid protocol given, otherwise add http:// manually.
     *
     * @param  bool $checkForProtocol Flag if website url should be checked for http(s) protocol
     * @return string Website URL
     */
    public function getWeb($checkForProtocol = false)
    {
        $web = $this->getData('web');
        if ($checkForProtocol && strlen(trim($web))) {
            if (strpos($web, 'http://') === false
                && strpos($web, 'https://') === false
            ) {
                $web = 'http://' . $web;
            }
        }

        return $web;
    }

    /**
     * Retrieve the specific country name by the selected country code
     *
     * @return string Country
     */
    public function getCountry()
    {
        $countryCode = $this->getData('country');

        return Mage::app()->getLocale()->getCountryTranslation($countryCode);
    }

    /**
     * Try to limit spam by generating a javascript email link
     *
     * @param boolean true
     * @return string
     */
    public function getEmail($antispam = false)
    {
        $email = $this->getData('email');
        $parts = explode('@', $email);

        if (!$antispam) {
            return $email;
        }

        if (count($parts) == 0) {
            return;
        }

        $html = '<a href="#" onclick="javascript:toRecipient();">';
        $html .= $parts[0] . '<span class="no-display">nospamplease</span>@<span class="no-display">nospamplease</span>' . $parts[1];
        $html .= '</a>';
        $html .= $this->getEmailJs($parts);

        return $html;
    }

    /**
     * Generate JS code
     *
     * @param $parts
     * @return string
     */
    public function getEmailJs($parts)
    {
        $js = <<<JS
<script>function toRecipient(){var m = '$parts[0]';m += '@';m += '$parts[1]';location.href= "mailto:"+m;}</script>
JS;

        return $js;
    }
}
