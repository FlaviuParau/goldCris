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

class Blugento_Localizer_Model_Product_Type extends Mage_Catalog_Model_Product_Type
{
    const TYPE_GIFT = 'gift-';

    /**
     * Remove virtual and downloadable product types from admin display.
     *
     * @return type
     */
    static public function getTypes() {
        if (Mage::getStoreConfig('general/locale/code') != 'de_DE') {
            return parent::getTypes();
        }

        if (is_null(self::$_types)) {
            $productTypes = Mage::getConfig()->getNode('global/catalog/product/type')->asArray();
            if(Mage::app()->getStore()->isAdmin()) {
                unset($productTypes[self::TYPE_VIRTUAL]);
                unset($productTypes[Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE]);
            }
            foreach ($productTypes as $productKey => $productConfig) {
                $moduleName = 'catalog';
                if (isset($productConfig['@']['module'])) {
                    $moduleName = $productConfig['@']['module'];
                }
                $translatedLabel = Mage::helper($moduleName)->__($productConfig['label']);
                $productTypes[$productKey]['label'] = $translatedLabel;
            }
            self::$_types = $productTypes;
        }

        return self::$_types;
    }

    /**
     * Here as the getTypes method will not be directly overwritten
     *
     * @return type
     */
    static public function getOptionArray()
    {
        $options = array();
        foreach(self::getTypes() as $typeId => $type) {
            $options[$typeId] = Mage::helper('catalog')->__($type['label']);
        }

        return $options;
    }
}
