<?php
/**
 * Class Me_Lff_Helper_Data
 *
 * @category  Me
 * @package   Me_Lff
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Lff_Helper_Data
 */
class Me_Lff_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * Path to store config if front-end output is enabled
     *
     * @var string
     */
    const XML_PATH_ENABLED = 'lff/config/enabled';

    /**
     * Path to store config free shipping is active
     *
     * @var string
     */
    const XML_PATH_FREE_SHIPPING_ACTIVE = 'carriers/freeshipping/active';

    /**
     * Path to store config free shipping amount
     *
     * @var string
     */
    const XML_PATH_FREE_SHIPPING_AMOUNT = 'carriers/freeshipping/free_shipping_subtotal';

    /**
     * Path to store config free shipping notification text
     *
     * @var string
     */
    const XML_PATH_NOTIFICATION_TEXT = 'lff/display/notification';

    /**
     * Path to store config if tax info is display
     *
     * @var string
     */
    const XML_PATH_TAX_INFO = 'lff/display/taxinfo';

    /**
     * Path to store config if tax suffix string
     *
     * @var string
     */
    const XML_PATH_TAX_SUFFIX = 'lff/display/taxsuffix';

    /**
     * Path to store config to show free shipping notification block in sidebar
     *
     * @var string
     */
    const XML_PATH_SIDEBAR = 'lff/display/sidebar';

    /**
     * Path to store config to show free shipping notification block in sidebar if cart is empty
     *
     * @var string
     */
    const XML_PATH_EMPTY = 'lff/display/empty';

    /**
     * Path to store config free shipping notification block title
     *
     * @var string
     */
    const XML_PATH_TITLE = 'lff/display/title';

    /**
     * Path to store config free shipping notification block notification text
     *
     * @var string
     */
    const XML_PATH_BLOCK_NOTIFICATION_TEXT = 'lff/display/block_notification';

    /**
     * Path to store config position of free shipping notification block in sidebar
     *
     * @var string
     */
    const XML_PATH_POSITION = 'lff/display/position';

    /**
     * Checks whether free shipping notification can be displayed in the frontend
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $store);
    }

    /**
     * Checks is free shipping active
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function isFreeShippingEnabled($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_FREE_SHIPPING_ACTIVE, $store);
    }

    /**
     * Checks if free shipping has amount
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function isFreeShippingHasAmount($store = null)
    {
        $amount = (float)Mage::getStoreConfig(self::XML_PATH_FREE_SHIPPING_AMOUNT, $store);
        if ($amount) {
            return true;
        }

        return false;
    }

    /**
     * Check if free shipping notification available
     *
     * @param integer|string|Mage_Core_Model_Store $store store $store store
     * @return bool
     */
    public function isFreeShippingNotificationAvailable($store = null)
    {
        if ($this->isEnabled($store) && $this->isFreeShippingEnabled($store) && $this->isFreeShippingHasAmount($store)) {
            return true;
        }

        return false;
    }

    /**
     * Get notification text
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function getNotificationText($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_NOTIFICATION_TEXT, $store);
    }

    /**
     * Get tax suffix after price
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return int|mixed
     */
    public function getTaxSuffixInfo($store = null)
    {
        $taxSuffix = 0;

        if (Mage::getStoreConfigFlag(self::XML_PATH_TAX_INFO, $store)) {
            $taxSuffix = Mage::getStoreConfig(self::XML_PATH_TAX_SUFFIX, $store);
        }

        return $taxSuffix;
    }

    /**
     * Checks whether free shipping notification can be displayed in the sidebar
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function showInSidebar($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SIDEBAR, $store);
    }

    /**
     * Checks whether free shipping notification can be displayed in the sidebar if cart is empty
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function showIfCartIsEmpty($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_EMPTY, $store);
    }

    /**
     * Get free shipping notification block title
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function getBlockTitle($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TITLE, $store);
    }

    /**
     * Get free shipping notification block text
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function getBlockNotificationText($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_BLOCK_NOTIFICATION_TEXT, $store);
    }

    /**
     * Get free shipping notification block position in the sidebar(left or right)
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function getPositionInSidebar($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_POSITION, $store);
    }

    /**
     * Check if Magento Edition is Enterprise
     *
     * @return bool
     */
    public function isEnterprise()
    {
        if (Mage::EDITION_ENTERPRISE === Mage::getEdition()) {
            return true;
        }

        return false;
    }

    /**
     * Get name helper for full page cache
     *
     * @param string $name name
     * @return mixed|string
     */
    public function getNameHelper($name = '')
    {
        $position = '';

        if ($name) {
            $position = str_replace('me.lff.', '', $name);
        }

        return $position;
    }
}
