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

class Blugento_Localizer_Model_Setup extends Mage_Core_Model_Abstract
{
    /**
     * Run Blugento Localizer Setup
     *
     * Trigger by event:
     * - admin_system_config_changed_section_blugentolocalizer
     *
     * @param Varien_Event_Observer $observer
     * @return Blugento_Localizer_Model_Setup
     */
    public function runLocalizerSetupAdmin($observer)
    {
        $buttonFlag = Mage::app()->getRequest()->getParam('blu_localizer_setup');

        $conf = Mage::app()->getRequest()->getParam('groups');
        $country = array($conf["global_config"]["fields"]["country"]["value"]);

        if ($buttonFlag && $country) {
            $data = Mage::helper('blugento_localizer')->getConfigScopeStoreId();
            $storeId = isset($conf["global_config"]["fields"]["store_id"]["value"]) ? $conf["global_config"]["fields"]["store_id"]["value"] : $data[0];

            $this->runLocalizerSetup($country, $storeId);
        }

        return;
    }

    /**
     * Run setup for selected countries
     * @param $countries
     */
    public function runLocalizerSetup($countries, $storeId = null)
    {
        if (is_array($countries)) {
            foreach ($countries as $country) {
                $this->_runSetup($country, $storeId);
            }
        } else {
            $this->_runSetup($countries, $storeId);
        }
    }

    /**
     * Run setup for selected country
     * @param $country
     */
    private function _runSetup($country, $storeId = null)
    {
        //$country = strtolower($country);
        $helper  = $this->_getHelper();

        if (!$storeId) {
            $storeId = 'default';
        }

        $this->installResources($country, $storeId);
    }

    /**
     * Install resources based on country select
     *
     * @param string $country
     * @param mixed  $storeId
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    private function installResources($country, $storeId)
    {
        if ($storeId == 0) {
            $storeId = 'default';
        }
        $helper = $this->_getHelper();

        foreach ($this->_getAvailableResources($country) as $resource) {
            $locale = array(
                'store' => $storeId,
                'code'  => $country
            );
            try {
                Mage::getSingleton('blugento_localizer/setup_' . $resource)->setup($locale);
            } catch(Exception $e) {
                if ($helper->isAdmin()) {
                    $message = $helper->__('Blugento Localizer: Error for country(%s) resource(%s).' . $e->getMessage(), $country, $resource);
                    Mage::logException($e);
                    Mage::throwException(new Exception($message));
                }
                Mage::logException($e);
            }
        }

        if ($helper->isAdmin()) {
            Mage::getSingleton('adminhtml/session')->addSuccess(
                $this->_getHelper()->__('Blugento Localizer: System Config Settings have been updated.')
            );
        }
    }

    /**
     * Retrieve the helper class
     *
     * @return Blugento_Localizer_Helper_Data Helper Class
     */
    protected function _getHelper()
    {
        return Mage::helper('blugento_localizer');
    }

    protected function _getAvailableResources($country)
    {
        $config = Mage::getConfig();
        $filePath = $config->getModuleDir('etc', 'Blugento_Localizer') . DS . 'resource.xml';

        if (!is_readable($filePath)) {
            throw new Exception('Can not read xml file '.$filePath);
        }

        $xmlObj = new Varien_Simplexml_Config($filePath);
        $xmlData = $xmlObj->getNode();

        $resources = array();

        $countries = $xmlData->country->asArray();
        foreach ($countries as $code => $resource) {
            if ($code == $country) {
                $resources = explode(',', $resource);
            }
        }

        return $resources;
    }
}
