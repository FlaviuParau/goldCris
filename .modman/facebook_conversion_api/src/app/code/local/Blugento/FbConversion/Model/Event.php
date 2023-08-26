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

class Blugento_FbConversion_Model_Event extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('blugento_fbconversion/event');
    }

    /**
     * Delete events by ids
     *
     * @param array $ids
     */
    public function deleteEvents($ids)
    {
        $sql = 'DELETE FROM ' . $this->getResource()->getMainTable() . ' WHERE id IN (' . implode(',', $ids) . ')';

        try {
            $this->getConnection(false)->query($sql);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Return connection
     *
     * @param bool $read
     * @return mixed
     */
    private function getConnection($read = true)
    {
        if ($read) {
            $connection = $this->getResourceConnection()->getConnection('core_read');
        } else {
            $connection = $this->getResourceConnection()->getConnection('core_write');
        }

        return $connection;
    }

    /**
     * Return resource
     *
     * @return Mage_Core_Model_Abstract
     */
    private function getResourceConnection()
    {
        return Mage::getSingleton('core/resource');
    }
}
