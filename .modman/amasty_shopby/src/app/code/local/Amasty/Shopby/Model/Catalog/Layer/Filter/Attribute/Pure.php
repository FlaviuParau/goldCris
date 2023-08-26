<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


if (Mage::helper('amshopby')->useSolr()) {
    $autoloader = Varien_Autoload::instance();
    $autoloader->autoload('Amasty_Shopby_Model_Catalog_Layer_Filter_Attribute_Enterprise');
} else {
    class Amasty_Shopby_Model_Catalog_Layer_Filter_Attribute_Pure extends Mage_Catalog_Model_Layer_Filter_Attribute {}
}