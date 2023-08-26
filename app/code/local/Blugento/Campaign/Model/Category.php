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
 * @package     Blugento_Campaign
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Campaign_Model_Category extends Mage_Core_Model_Abstract
{
    /**
     * Return category names by parent category id
     *
     * @param int $categoryId
     * @return array
     */
    public function getCategoriesNames($categoryId)
    {
        try {
            $attributeCode = $this->_getAttributeCode('name');
            $parentCategory = $this->_getParentCategory($categoryId, $attributeCode);
            
            $categories = array();
            $categories['entity_id'] = $parentCategory[0]['entity_id'];
            $categories['name'] = $parentCategory[0]['name'];

            $sql = "SELECT DISTINCT cce.entity_id, ccev.value AS name
                    FROM catalog_category_entity cce
                    LEFT JOIN catalog_category_entity_varchar ccev 
                    ON cce.entity_id = ccev.entity_id AND attribute_id = $attributeCode
                    RIGHT JOIN catalog_category_product ccp
                    ON ccp.category_id = cce.entity_id
                    WHERE cce.parent_id = $categoryId";

            $result =  $this->_getConnection()->fetchAll($sql);

            foreach ($result as $data) {
                $categories['children'][] = array (
                    'entity_id' => $data['entity_id'],
                    'name' => $data['name'],
                );
            }

            return $categories;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Return all products ids from given(campaign) category and from level two child categories
     *
     * @param int $categoryId
     * @return array
     */
    public function getCampaignProducts($categoryId)
    {
        $products = array();
        try {
            $sql = 'SELECT ccp.product_id
                    FROM catalog_category_entity cce
                    JOIN catalog_category_product ccp
                    ON ccp.category_id = cce.entity_id
                    WHERE cce.parent_id = ' . $categoryId;

            $result =  $this->_getConnection()->fetchAll($sql);

            foreach ($result as $item) {
                $products[] = $item['product_id'];
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $products;
    }

    /**
     * Get parent category name by category id
     *
     * @param int $categoryId
     * @param int $nameAttribute
     * @return mixed
     */
    private function _getParentCategory($categoryId, $nameAttribute)
    {
        $sql = "SELECT entity_id, value AS name
                FROM catalog_category_entity_varchar
                WHERE entity_id = $categoryId AND attribute_id = $nameAttribute";

        try {
            return $this->_getConnection()->fetchAll($sql);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Return attribute code by its name
     *
     * @param string $attribute
     * @return mixed
     */
    private function _getAttributeCode($attribute)
    {
        $sql = "SELECT attribute_id
                FROM eav_attribute
                WHERE attribute_code LIKE '$attribute'
                AND entity_type_id = 3";

        try {
            return $this->_getConnection()->fetchOne($sql);
        } catch (Exception $e) {
            Mage::logException($e);
        }
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