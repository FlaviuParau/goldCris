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
 * @package     Blugento_UrlCheck
 * @author      Mihai Rastasan <mihai.rastasan@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product url key attribute backend
 *
 * @category   Mage
 * @package    Blugento_UrlCheck
 */
class Blugento_UrlCheck_Model_Product_Attribute_Backend_Name extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Validate object
     *
     * @param Varien_Object $object
     * @throws Mage_Eav_Exception
     * @return boolean
     */
    public function validate($object)
    {
        if (!Mage::getStoreConfig('blugento_urlcheck/general/enabled')) {
            return true;
        }

        if (Mage::getStoreConfig('blugento_urlcheck/general/only_new') && $object->getData('entity_id') ) {
            return true;
        }

        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);

        $urlKey = $object->getData('url_key');
        if ($urlKey) {
            return true;
        }

        if ($this->getAttribute()->getIsRequired() && $this->getAttribute()->isValueEmpty($value)) {
            return false;
        }

        if (!$this->getAttribute()->getEntity()->checkAttributeUniqueValue($this->getAttribute(), $object)) {
            throw Mage::exception('Mage_Eav',
                Mage::helper('eav')->__('The value of this attribute must be unique if you do not set explicit URL Key')
            );
        }

        return true;
    }
}
