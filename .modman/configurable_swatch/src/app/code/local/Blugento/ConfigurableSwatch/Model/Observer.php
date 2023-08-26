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

class Blugento_ConfigurableSwatch_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * Attach children products after product collection load
     * Observes: catalog_product_collection_load_after
     *
     * @param Varien_Event_Observer $observer
     */
    public function productCollectionLoadAfter(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('blugento_configurableswatch')->isEnabled()) {
            return;
        }

        /* @var $mediaHelper Mage_ConfigurableSwatches_Helper_Media */
        $mediaHelper = Mage::helper('blugento_configurableswatch/media');

        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = $observer->getCollection();

        if ($collection
            instanceof Mage_ConfigurableSwatches_Model_Resource_Catalog_Product_Type_Configurable_Product_Collection) {
            return;
        }

        $products = $collection->getItems();

        $mediaHelper->attachProductChildrenAttributeMapping($products, $collection->getStoreId());
    }

    /**
     * Attach children products after product load
     * Observes: catalog_product_load_after
     *
     * @param Varien_Event_Observer $observer
     */
    public function productLoadAfter(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('blugento_configurableswatch')->isEnabled()) {
            return;
        }

        /* @var $helper Mage_ConfigurableSwatches_Helper_Mediafallback */
        $helper = Mage::helper('blugento_configurableswatch/media');

        /* @var $product Mage_Catalog_Model_Product */
        $product = $observer->getDataObject();

        if ($product->getTypeId() != Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
            return;
        }

        $helper->attachProductChildrenAttributeMapping(array($product), $product->getStoreId(), false);
    }
}
