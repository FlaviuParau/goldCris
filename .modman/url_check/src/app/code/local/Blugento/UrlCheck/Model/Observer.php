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

class Blugento_UrlCheck_Model_Observer
{
      /**
     * Dispatch by :: catalog_category_save_before
     *
     * @param $observer
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function categorySaveBefore($observer)
    {
        if (!Mage::getStoreConfig('blugento_urlcheck/general/enabled')) {
            return $this;
        }

        $category = $observer->getEvent()->getCategory();

        if (Mage::getStoreConfig('blugento_urlcheck/general/only_new') && $category->getEntityId()) {
            return $this;
        }

        $urlKey = $this->_formatUrlKey($category->getUrlKey());
        if (!$urlKey) {
            $urlKey = $this->_formatUrlKey($category->getName());
        }

        $category->setUrlKey($this->_formatUrlKey($urlKey));
        $urlKey = $this->_formatUrlKey($category->getUrlKey());
        
        if (!$this->_urlKeyExist($urlKey, $category->getId())) {
            return $this;
        }

        /** @var Blugento_UrlCheck_Helper_Data $helper */
        $helper = Mage::helper('blugento_urlcheck');

        Mage::getSingleton('adminhtml/session')->addError($helper->__('Duplicate URL.'));
        throw new Mage_Core_Exception(
            $helper->__('You need to change the category name or the category URL key.')
        );
    }

    /**
     * Check if URL Key exist.
     *
     * @param string $urlKey
     * @return mixed
     */
    private function _urlKeyExist($urlKey, $catId)
    {

        if (isset($catId)){
        $query = "SELECT value_id
        FROM catalog_category_entity_varchar
        WHERE value = '" . $urlKey . "' AND attribute_id = (
            SELECT attribute_id
            FROM eav_attribute
            WHERE entity_type_id = 3 AND attribute_code = 'url_key')
            AND entity_id NOT IN ($catId)";
        } else {
                $query = "SELECT value_id
        FROM catalog_category_entity_varchar
        WHERE value = '" . $urlKey . "' AND attribute_id = (
            SELECT attribute_id
            FROM eav_attribute
            WHERE entity_type_id = 3 AND attribute_code = 'url_key')";
            }

        return $this->_getReadConnection()->fetchOne($query);

    }

    /**
     * Retrieve the read connection
     *
     * @return mixed
     */
    private function _getReadConnection()
    {
        return Mage::getSingleton('core/resource')->getConnection('core_read');
    }

    public function changeUrlKeyProductDuplicate($observer)
    {
        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSort('created_at', 'desc');
        $collection->getSelect()->limit(1);
        $latestItemId = $collection->getLastItem()->getId();
        $latestItemId ++;

        $newProduct = $observer->getEvent()->getNewProduct();
        $old_url_key = $newProduct->getUrlKey();
        $newProduct->setUrlKey($old_url_key . '-' . $latestItemId);
    }

    private function _formatUrlKey($str)
    {
        $helper = Mage::helper('blugento_urlcheck');
        $str = $helper->sanitizeText($str);

        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($str));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');

        return $urlKey;
    }
}
