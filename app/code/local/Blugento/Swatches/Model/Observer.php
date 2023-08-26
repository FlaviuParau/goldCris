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
 * @package     Blugento_Swatches
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Swatches_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * Add swatches values when extension is enabled
     */
    public function addSwatches()
    {
        /** @var Blugento_Swatches_Helper_Data $helper */
        $helper = Mage::helper('blugento_swatches');

        if ($helper->isExtensionEnabled()) {
            if ($attributes = $helper->getSwatchAttributes()) {
                /** @var Blugento_Swatches_Model_Swatches $swatches */
                $swatches = Mage::getModel('blugento_swatches/swatches');

                $swatches->addSwatches($attributes);
            }
        }
    }

    /**
     * Add swatches values new attribute option is added
     *
     * @param Varien_Event_Observer $observer
     */
    public function addSwatchOption(Varien_Event_Observer $observer)
    {
        /** @var Blugento_Swatches_Helper_Data $helper */
        $helper = Mage::helper('blugento_swatches');

        if ($helper->isExtensionEnabled()) {
            $swatchAttributes = explode(',', $helper->getSwatchAttributes());
            $attribute = $observer->getEvent()->getAttribute()->getAttributeId();

            if (count($swatchAttributes) > 0 && in_array($attribute, $swatchAttributes)) {
                /** @var Blugento_Swatches_Model_Swatches $swatches */
                $swatches = Mage::getModel('blugento_swatches/swatches');

                $swatches->addSwatches($attribute);
            }
        }
    }
}