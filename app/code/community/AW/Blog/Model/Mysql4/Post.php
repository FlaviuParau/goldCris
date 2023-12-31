<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Blog
 * @version    1.3.16
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Blog_Model_Mysql4_Post extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('blog/post', 'post_id');
    }

    public function load(Mage_Core_Model_Abstract $object, $value, $field = null)
    {
        if (strcmp($value, (int)$value) !== 0) {
            $field = 'identifier';
        }
        return parent::load($object, $value, $field);
    }

    public function save(Mage_Core_Model_Abstract $post)
    {
        $identifier = $post->getIdentifier();
        if (!$identifier || $identifier == '') {
            $identifier = preg_replace('#[^0-9a-z]+#i', '-', $post->getTitle());
            $identifier = strtolower($identifier);
            $post->setIdentifier($identifier);
        }

        return parent::save($post);
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $post)
    {
        $id = $post->getId();
        $identifier = $post->getIdentifier();

        if (!$identifier || $identifier == '') {
            $identifier = preg_replace('#[^0-9a-z]+#i', '-', $post->getTitle());
            $identifier = strtolower($identifier);
        }

        if (!$this->getIsUniqueIdentifier($identifier, $id)) {
            Mage::throwException(Mage::helper('blog')->__('Post Identifier already exist.'));
        }

        if ($this->isNumericIdentifier($identifier)) {
            Mage::throwException(Mage::helper('blog')->__('Post Identifier cannot consist only of numbers.'));
        }

        return $this;
    }

    public function getIsUniqueIdentifier($identifier, $id=null)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from($this->getMainTable())
            ->where($this->getMainTable() . '.identifier = ?', $identifier)
        ;
        if ($id) {
            $select->where($this->getMainTable() . '.post_id <> ?', $id);
        }

        if ($this->_getWriteAdapter()->fetchRow($select)) {
            return false;
        }

        return true;
    }

    protected function isNumericIdentifier($identifier)
    {
        return preg_match('/^[0-9]+$/', $identifier);
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $condition = $this->_getWriteAdapter()->quoteInto('post_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('store'), $condition);
        if (!$object->getData('stores')) {
            $storeArray = array();
            $storeArray['post_id'] = $object->getId();
            $storeArray['store_id'] = '0';
            $this->_getWriteAdapter()->insert($this->getTable('store'), $storeArray);
        } else {
            foreach ((array)$object->getData('stores') as $store) {
                $storeArray = array();
                $storeArray['post_id'] = $object->getId();
                $storeArray['store_id'] = $store;
                $this->_getWriteAdapter()->insert($this->getTable('store'), $storeArray);
            }
        }

        $condition = $this->_getWriteAdapter()->quoteInto('post_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('post_cat'), $condition);

        foreach ((array)$object->getData('cats') as $store) {
            $storeArray = array();
            $storeArray['post_id'] = $object->getId();
            $storeArray['cat_id'] = $store;
            $this->_getWriteAdapter()->insert($this->getTable('post_cat'), $storeArray);
        }


        return parent::_afterSave($object);
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     *
     * @return $this|Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('store'))
            ->where('post_id = ?', $object->getId())
        ;

        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $storesArray = array();
            foreach ($data as $row) {
                $storesArray[] = $row['store_id'];
            }
            $object->setData('store_id', $storesArray);
        }

        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('post_cat'))
            ->where('post_id = ?', $object->getId())
        ;

        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $catsArray = array();
            foreach ($data as $row) {
                $catsArray[] = $row['cat_id'];
            }
            $object->setData('cat_id', $catsArray);
        }

        return parent::_afterLoad($object);
    }

    public function getCategoriesForPost($posts)
    {
        $select = $this->_getReadAdapter()
            ->select()
            ->from(array('post_category' => $this->getTable('blog/post_cat')))
            ->joinLeft(
                array('category_store' => $this->getTable('blog/cat_store')),
                'post_category.cat_id = category_store.cat_id', array()
            )
            ->joinLeft(
                array('category_main' => $this->getTable('blog/cat')), 'post_category.cat_id = category_main.cat_id',
                array('title', 'identifier', 'posts' => new Zend_Db_Expr('GROUP_CONCAT(DISTINCT post_id)'))
            )
            ->where('category_store.store_id = 0 OR category_store.store_id = ?', Mage::app()->getStore()->getId())
            ->where('post_category.post_id IN(?)', $posts)
            ->group('category_main.cat_id')
        ;

        return $this->_getReadAdapter()->fetchAll($select);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string                   $field
     * @param mixed                    $value
     * @param Mage_Core_Model_Abstract $object
     *
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $select->join(array('cps' => $this->getTable('store')), $this->getMainTable() . '.post_id = `cps`.post_id')
                ->where('`cps`.store_id in (0, ?) ', $object->getStoreId())
                ->order('store_id DESC')
                ->limit(1);
        }
        return $select;
    }
}