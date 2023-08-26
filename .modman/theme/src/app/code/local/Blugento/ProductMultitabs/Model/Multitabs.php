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
 * @package     Blugento_ProductMultitabs
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_ProductMultitabs_Model_Multitabs extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('blugento_productmultitabs/multitabs');
    }

    /**
     * Return default tabs.
     *
     * @return array
     */
    public function getDefaultTabs()
    {
        $collection = $this->getCollection()->addFieldToFilter('type', 'default');
        return $collection->getData();
    }

    /**
     * Return custom tabs.
     *
     * @return array
     */
    public function getCustomTabs()
    {
        $collection = $this->getCollection()
            ->addFieldToFilter('type', 'custom')
            ->addFieldToFilter('status', 1);

        return $collection->getData();
    }

    /**
     * Check if a tab with the same name already exists.
     *
     * @param string $name
     * @return bool
     */
    public function nameExist($name) {
        $collection = $this->getCollection()->addFieldToFilter('name', $name);

        $valid = false;
        foreach ($collection->getData() as $data) {
            if ($data['id'] != $this->getId()) {
                $valid = true;
            }
        }

        return $valid;
    }

    /**
     * Check if table is enabled.
     *
     * @param string $tab
     * @return bool
     */
    public function isTabEnabled($tab)
    {
        $reviewTab = $this->load($tab, 'identifier')->getData();
        $valid = true;
        if (!$reviewTab['status']) {
            $valid = false;
        }

        return $valid;
    }

    /**
     * Return tabs sort order.
     *
     * @return null|array
     */
    public function getSortOrder()
    {
        $sql = "SELECT identifier, sort_order
                FROM blugento_productmultitabs_tabs";

        try {
            $result = $this->_getWriteConnection()->fetchAll($sql);
            return $result;
        } catch (Exception $e) {
            Mage::logException($e);
        }

        $result = null;
    }

    /**
     * Retrieve the write connection
     *
     * @return mixed
     */
    private function _getWriteConnection()
    {
        return $this->_getResourceConnection()->getConnection('core_write');
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