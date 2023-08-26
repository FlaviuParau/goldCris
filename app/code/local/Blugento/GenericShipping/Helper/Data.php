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

class Blugento_GenericShipping_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Blugento shipping modules
     *
     * @var array
     */
    protected $shippingModules = array ('Blugento_UrgentCargus', 'Blugento_FanCourier', 'Blugento_SamedayCourier',
        'Blugento_Nemoexpress'); // TODO Add all blugento shipping modules

    /**
     * Check if there is any Blugento shipping module installed and enabled
     *
     * @return bool
     */
    public function isShippingModulesEnabled()
    {
        $enabled = false;
        foreach ($this->shippingModules as $moduleName) {
            if (Mage::helper('core')->isModuleEnabled($moduleName)
                && Mage::helper(strtolower($moduleName))->isEnabled()
            ) {
                $enabled = true;
            }
        }

        return $enabled;
    }
}
