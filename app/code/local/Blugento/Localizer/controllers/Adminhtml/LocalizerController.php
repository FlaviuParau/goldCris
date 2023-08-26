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

class Blugento_Localizer_Adminhtml_LocalizerController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Localizer setup
     */
    public function setupAction()
    {
        $buttonFlag = Mage::app()->getRequest()->getParam('blu_localizer_setup');
        $conf       = Mage::app()->getRequest()->getParams();
        $storeId    = 0;

        if (isset($conf['groups']["global_config"]["fields"]["country"]["value"])) {
            $country = array($conf['groups']["global_config"]["fields"]["country"]["value"]);

            $country_ = is_array($country) ? $country[0] : $country;
            if (!is_null(Mage::registry('setup_country'))) {
                Mage::unregister('setup_country');
            }
            Mage::register('setup_country', $country_);

            if ($buttonFlag) {
                $data = Mage::helper('blugento_localizer')->getConfigScopeStoreId();
                $storeId = isset($conf['groups']["global_config"]["fields"]["store_id"]["value"]) ?
                            $conf['groups']["global_config"]["fields"]["store_id"]["value"] : $data[0];

                Mage::getModel('blugento_localizer/setup')->runLocalizerSetup($country, $storeId);
            } else {
                Mage::getSingleton('adminhtml/session')->addError(
                    $this->_getHelper()->__('Blugento Localizer: System Config Settings have NOT been updated. Please try again later.')
                );
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                $this->_getHelper()->__('Blugento Localizer: System Config Settings have NOT been updated. Please try again later.')
            );
        }

        if ($storeId != 0 && $storeId != 'default') {
            $store = Mage::getModel('core/store')->load($storeId);
            $storeCode = $store->getCode();
            $websiteCode = $store->getWebsite()->getCode();
            $this->_redirect('adminhtml/system_config/edit/section/blugentolocalizer/website/' . $websiteCode . '/store/' . $storeCode);
        } else {
            $this->_redirect('adminhtml/system_config/edit/section/blugentolocalizer');
        }
    }
}
