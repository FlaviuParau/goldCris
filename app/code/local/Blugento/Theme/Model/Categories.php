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
class Blugento_Theme_Model_Categories extends Mage_Core_Model_Abstract
{
    /**
     * Return categories names as string
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getCategoriesNames($product)
    {
        $categoryIds = implode(',', $product->getCategoryIds());

        $names = $this->getCategoriesNamesByIds($categoryIds);

        $categories = [];
        foreach ($names as $name) {
            $categories[] = $name['name'];
        }

        return '"' . implode('","', $categories) . '"';
    }

    /**
     * Return categories names by ids
     *
     * @param $ids
     * @return mixed
     */
    protected function getCategoriesNamesByIds($ids)
    {
        try {
            $sql = "SELECT `value` as name 
                FROM `catalog_category_entity_varchar`
                WHERE `entity_id` IN ($ids)
                AND `attribute_id` = " . $this->getAttributeId('name');

            $connection = Mage::getSingleton('core/resource')->getConnection('core_read');

            return $connection->fetchAll($sql);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Return attribute id by code
     *
     * @param string $code
     * @return mixed
     */
    protected function getAttributeId($code)
    {
        try {
            $sql = "SELECT `attribute_id`
                FROM `eav_attribute`
                WHERE `entity_type_id` = 3
                AND `attribute_code` LIKE '$code'";

            $connection = Mage::getSingleton('core/resource')->getConnection('core_read');

            return $connection->fetchOne($sql);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }
}
