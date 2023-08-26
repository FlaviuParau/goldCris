<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Commonrules
 */
class Amasty_Commonrules_Model_Resource_Rule extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * type of a concrete resource rule class
     *
     * @var string $_type
     */
    protected $_type;

    /**
     * resource model table description
     *
     * @var array
     */
    protected $_tableDscr;

    public function _construct()
    {
        $this->_init($this->_type . '/rule', 'rule_id');
        $this->_setTableDescription();
    }

    /**
     * initialize $this->_tableDscr array
     *
     * @return $this
     */
    protected function _setTableDescription()
    {
        $readAdapter = Mage::getSingleton('core/resource')->getConnection('core_read');
        $tableName   = $this->getMainTable();
        $this->_tableDscr = $readAdapter->describeTable($tableName);
        return $this;
    }

    /**
     * is a resource table has a certain field
     *
     * @param string $column
     * @return bool
     */
    public function hasField($column)
    {
        return isset($this->_tableDscr[$column]);
    }

    public function massChangeStatus($ids, $status)
    {
        $db = $this->_getWriteAdapter();
        $db->update($this->getMainTable(),
            array('is_active' => $status), 'rule_id IN(' . implode(',', $ids) . ') ');

        return true;
    }

    /**
     * Return codes of all product attributes currently used in promo rules
     *
     * @return array
     */
    public function getAttributes()
    {
        $read = $this->_getReadAdapter();
        $tbl   = $this->getTable($this->_type . '/attribute');

        $select = $read->select()->from($tbl, new Zend_Db_Expr('DISTINCT code'));
        return $read->fetchCol($select);
    }

    /**
     * Save product attributes currently used in conditions and actions of the rule
     *
     * @param int $id rule id
     * @param mixed $attributes
     * @return Amasty_Commonrules_Model_Resource_Rule
     */
    public function saveAttributes($id, $attributes)
    {
        $write = $this->_getWriteAdapter();
        $tbl   = $this->getTable($this->_type . '/attribute');

        $write->delete($tbl, array('rule_id=?' => $id));

        $data = array();
        foreach ($attributes as $code){
            $data[] = array(
                'rule_id' => $id,
                'code'    => $code,
            );
        }
        $write->insertMultiple($tbl, $data);

        return $this;
    }

}