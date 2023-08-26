<?php
/**
 * Blugento Sliders
 * Model Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Sliders
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @author Daniel Gheoltan <daniel.gheoltan@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Sliders_Model_Mysql4_Group extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('blugento_sliders/group', 'group_id');
    }

    /**
     * Retrieve the load select object
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Core_Model_Abstract $object
     * @return Varien_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if (!Mage::app()->isSingleStoreMode() && Mage::app()->getStore()->getId() > 0) {
            $select->where('store_id IN (?)', array(0, Mage::app()->getStore()->getId()))
                ->order('store_id DESC')
                ->limit(1);
        }

        return $select;
    }

    /**
     * Retrieve a collection of banners associated with the group
     *
     * @param Blugento_Sliders_Model_Group $group
     * @param bool $includeDisabled
     * @return Blugento_Sliders_Model_Mysql4_Banner_Collection
     */
    public function getBannerCollection(Blugento_Sliders_Model_Group $group, $includeDisabled = false)
    {
        $banners = Mage::getResourceModel('blugento_sliders/banner_collection')
            ->addGroupIdFilter($group->getId());

        if ($group->getRandomiseBanners()) {
            $banners->addOrderByRandom();
        } else {
            $banners->addOrderBySortOrder();
        }

        if (!$includeDisabled) {
            $banners->addIsEnabledFilter(1);
        }

        return $banners;
    }

    /**
     * Apply processing before saving object
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Mysql4_Abstract
     * @throws Exception
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getCode()) {
            throw new Exception(Mage::helper('blugento_sliders')->__('Banner group must have a unique code'));
        }

        $object->setCode($this->formatGroupCode($object->getCode()));

        if (Mage::getDesign()->getArea() == 'adminhtml') {
            foreach ($object->getData() as $field => $value) {
                if (preg_match("/^use_config_([a-zA-Z_]{1,})$/", $field, $result)) {

                    $object->setData($result[1], null);
                    $object->unsetData($field);
                }
            }
        }

        return parent::_beforeSave($object);
    }

    /**
     * Convert a string into a valid group code
     *
     * @param string $str
     * @return string
     */
    public function formatGroupCode($str)
    {
        $str = preg_replace('#[^0-9a-z]+#i', '_', Mage::helper('catalog/product_url')->format($str));
        $str = strtolower($str);
        $str = trim($str, '_');

        return $str;
    }
}
