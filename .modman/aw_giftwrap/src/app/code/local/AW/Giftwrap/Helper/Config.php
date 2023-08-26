<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Giftwrap
 * @version    1.1.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Giftwrap_Helper_Config extends Mage_Core_Helper_Data
{
    /**
     * "Enable Gift Wrap" from system config
     */
    const GENERAL_IS_ENABLED = 'aw_giftwrap/general/is_enabled';

    /**
     * "Allow wrapping products separately" from system config
     */
    const GENERAL_IS_WRAP_PRODUCTS_SEPARATELY = 'aw_giftwrap/general/is_wrap_products_separately';

    /**
     * "Enable Gift Message" from system config
     */
    const GENERAL_IS_GIFT_MESSAGE_ENABLED = 'aw_giftwrap/general/is_gift_message_enabled';

    /**
     * "Exclude Products Rule" from config
     */
    const EXCLUDE_PRODUCTS_RULE = 'aw_giftwrap/exclude_products/rule';

    /**
     * @param Mage_Core_Model_Store|int $store = null
     * @return boolean
     */
    public function isEnabled($store = null)
    {
        $isModuleEnabled = $this->isModuleEnabled();
        $isModuleOutputEnabled = $this->isModuleOutputEnabled();
        return $isModuleOutputEnabled && $isModuleEnabled && Mage::getStoreConfigFlag(self::GENERAL_IS_ENABLED, $store);
    }

    /**
     * @param Mage_Core_Model_Store|int $store = null
     * @return boolean
     */
    public function isWrapProductsSeparately($store = null)
    {
        return Mage::getStoreConfigFlag(self::GENERAL_IS_WRAP_PRODUCTS_SEPARATELY, $store);
    }

    /**
     * @param Mage_Core_Model_Store|int $store = null
     * @return boolean
     */
    public function isGiftMessageEnabled($store = null)
    {
        return !!Mage::getStoreConfigFlag(self::GENERAL_IS_GIFT_MESSAGE_ENABLED, $store);
    }

    /**
     * @param Mage_Core_Model_Store|int $store = null
     * @return AW_Giftwrap_Model_Exclude_Products_Rule
     */
    public function getExcludeProductsRule($store = null)
    {
        $ruleModel = Mage::getModel('aw_giftwrap/exclude_products_rule');
        $conditionsSerialized = $this->getSerializedExcludeProductsRule($store);
        $ruleModel->setData('conditions_serialized', $conditionsSerialized);

        /* Fix for magento equal or less 1.6.2.0 start */
        if (Mage::helper('aw_giftwrap')->isMageVersionLessOrEqualThan('1.6.2.0')) {
            $conditionsArr = unserialize($conditionsSerialized);
            if (!empty($conditionsArr) && is_array($conditionsArr)) {
                $ruleModel->getConditions()->loadArray($conditionsArr);
            }
        }
        /* Fix for magento equal or less 1.6.2.0 end */

        return $ruleModel;
    }

    /**
     * @param Mage_Core_Model_Store|int $store = null
     * @return string
     */
    public function getSerializedExcludeProductsRule($store = null)
    {
        return Mage::getStoreConfig(self::EXCLUDE_PRODUCTS_RULE, $store);
    }

    /**
     * @param array $ruleConditionsArray
     */
    public function saveExcludeProductsRule(array $ruleConditionsArray)
    {
        $ruleModel = Mage::getModel('aw_giftwrap/exclude_products_rule');
        $ruleModel->loadPost($ruleConditionsArray);
        try {
            $serializedConditions = serialize($ruleModel->getConditions()->asArray());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Exclude Products Rule error: ') . $e->getMessage());
            $urlExtraParams = array(
                'section' => Mage::app()->getRequest()->getParam('section'),
            );
            Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl('*/*/edit', $urlExtraParams));
            return;
        }
        Mage::getModel('core/config')->saveConfig('aw_giftwrap/exclude_products/rule', $serializedConditions);
    }

    /**
     * Retrieve adminhtml session model object
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

}