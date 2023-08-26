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

class Blugento_Swatches_Model_Swatches extends Mage_Core_Model_Abstract
{
    /**
     * Product id
     */
    protected $_productId;

    public function _construct()
    {
        parent::_construct();
        $this->_init('blugento_swatches/swatches');
    }

    /**
     * Add swatches values when extension is enabled
     *
     * @param int|string $attributeIds
     */
    public function addSwatches($attributeIds)
    {
        $query = 'INSERT INTO blugento_swatches (option_id, attribute, mode)
                  SELECT option_id, attribute_code, 1 
                  FROM eav_attribute_option ao
                  INNER JOIN eav_attribute a
                  ON a.`attribute_id` = ao.`attribute_id`
                  WHERE a.`attribute_id` IN (' . $attributeIds . ')
                  AND ao.`option_id` NOT IN (
                      SELECT option_id 
                      FROM blugento_swatches 
                  )';

        try {
            $connection = $this->_getConnection('write');
            $connection->query($query);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Return swatch option value by option id
     *
     * @param int $optionId
     * @return string
     */
    public function getOptionValue($optionId)
    {
        $query = 'SELECT value 
                  FROM eav_attribute_option_value
                  WHERE option_id = ' . $optionId;

        $value = '';
        try {
            $connection = $this->_getConnection();
            $value = $connection->fetchOne($query);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $value;
    }

    /**
     * Return media gallery for all the children products
     *
     * @return array
     */
    public function getChildrenGallery()
    {
        $sql = 'SELECT DISTINCT gallery.value_id, gallery.value AS file, super.product_id, gv.label, gv.position, gv.disabled, 
                   gv.label AS label_default, gv.position AS position_default, gv.disabled AS disabled_default 
                FROM catalog_product_super_link super
                JOIN catalog_product_entity_media_gallery gallery
                ON super.product_id = gallery.entity_id
                JOIN catalog_product_entity_media_gallery_value gv
                ON gallery.value_id = gv.value_id
                WHERE super.parent_id = ' . $this->getProductId();

        $gallery = array();

        try {
            $connection = $this->_getConnection();
            $gallery = $connection->fetchAll($sql);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $gallery;
    }

    /**
     * Set product id
     *
     * @param int $id
     */
    public function setProductId($id)
    {
        $this->_productId = $id;
    }

    /**
     * Return product id
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->_productId;
    }

    /**
     * Retrieve connection
     *
     * @param null|string $type
     * @return mixed
     */
    private function _getConnection($type = null)
    {
        if ($type == 'write') {
            return $this->_getResourceConnection()->getConnection('core_write');
        } else {
            return $this->_getResourceConnection()->getConnection('core_read');
        }
    }

    /**
     * Get the resource model
     *
     * @return Mage_Core_Model_Abstract
     */
    private function _getResourceConnection()
    {
        return Mage::getSingleton('core/resource');
    }
}