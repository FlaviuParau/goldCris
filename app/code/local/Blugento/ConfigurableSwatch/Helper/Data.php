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
 * @package     Blugento_ConfigurableSwatch
 * @author      Mihai Rastasan <mihai.rastasan@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_ConfigurableSwatch_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_configAttributeIds = null;

    const CONFIG_PATH_SWATCH_ATTRIBUTES = 'blugento_configurableswatch/general/swatch_attributes';

    public function isEnabled()
    {
        return Mage::getStoreConfig('blugento_configurableswatch/general/enabled');
    }

    /**
     * Get list of attributes that should use swatches
     *
     * @return array
     */
    public function getSwatchAttributeIds()
    {
        if (is_null($this->_configAttributeIds)) {
            $this->_configAttributeIds = array();
            if (Mage::getStoreConfig(self::CONFIG_PATH_SWATCH_ATTRIBUTES)) {
                $this->_configAttributeIds = explode(',', Mage::getStoreConfig(self::CONFIG_PATH_SWATCH_ATTRIBUTES));
            }
        }
        return $this->_configAttributeIds;
    }
	
	public function isSwatchImageEnabled()
	{
		return Mage::getStoreConfig('blugento_configurableswatch/general/swatch_image');
	}
}
