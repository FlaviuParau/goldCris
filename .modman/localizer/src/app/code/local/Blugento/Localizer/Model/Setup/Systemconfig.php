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
 * Setup class for System configurations
 */
class Blugento_Localizer_Model_Setup_Systemconfig extends Blugento_Localizer_Model_Setup_Abstract
{
    /**
     * Setup Tax setting
     */
    public function setup($locale)
    {
        $this->_updateConfigData($locale);
    }

    /**
     * Update configuration settings
     */
    protected function _updateConfigData($locale)
    {
        $storeId = $locale['store'];

        $scope = 'default';
        $scopeId = 0;
        if ($storeId && $storeId != 'default') {
            $scope = 'stores';
            $scopeId = $storeId;
        }

        $setup = $this->_getSetup();
        foreach ($this->_getConfigSystemConfig() as $key => $value) {
            $setup->setConfigData(str_replace('__', '/', $key), $value, $scope, $scopeId);
        }
    }

    /**
     * Get tax calculations from config file
     *
     * @return array Config System Config
     */
    protected function _getConfigSystemConfig()
    {
        return $this->_getConfigNode('system_config', 'default');
    }
}
