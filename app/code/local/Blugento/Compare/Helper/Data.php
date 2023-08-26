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
 * @package     Blugento_Compare
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Compare_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Check if module is enabled
     *
     * @return bool|mixed
     */
    public function enableCompare()
    {
        return $this->getConfigNodeValue('enabled');
    }

    /**
     * Return the Max Compare Products allowed
     *
     * @return int|mixed
     */
    public function getMaxCompareProduct()
    {
        return $this->getConfigNodeValue('max_number');
    }

    /**
     * Return the Reach Limit Message
     *
     * @return string|mixed
     */
    public function getLimitCompareMessage()
    {
        $message = $this->getConfigNodeValue('message');

        if (strpos($message, '{{max_number}}')) {
            $message = str_replace('{{max_number}}', $this->getMaxCompareProduct(), $message);
        }

        return $message;
    }

    /**
     * Return sys config value
     *
     * @param string $resource
     * @return mixed
     */
    public function getConfigNodeValue($resource)
    {
        return Mage::getStoreConfig($this->getConfigNodePath($resource));
    }

    /**
     * Return sys config path
     *
     * @param string $resource
     * @return string
     */
    public function getConfigNodePath($resource)
    {
        return 'blugento_compare/settings/' . $resource;
    }

    /**
     * Set the module enable/disable and max_number from an external process
     *
     * @param string $state
     * @return bool
     */
    public function setState($state)
    {
        $disableValues = array('OFF', '0', 'DISABLE');
        $enableValues  = array(1, 2, 3, 4);

        try {
            if (in_array($state, $disableValues)) {
                Mage::getModel('core/config')->saveConfig($this->getConfigNodePath('enabled'), 0);
            } else if (in_array($state, $enableValues)) {
                Mage::getModel('core/config')->saveConfig($this->getConfigNodePath('enabled'), 1);
                Mage::getModel('core/config')->saveConfig($this->getConfigNodePath('max_number'), $state);
            } else {
                return false;
            }
            return true;
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return false;
    }
}
