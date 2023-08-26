<?php

class Blugento_Review_Model_Resource_Review extends Mage_Review_Model_Resource_Review
{
    /**
     * Retrieves total reviews
     *
     * @param int $entityPkValue
     * @param bool $approvedOnly
     * @param int $storeId
     * @return int
     */
    public function getTotalReviews($entityPkValue, $approvedOnly = false, $storeId = 0)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->_reviewTable,
                array(
                    'review_count' => new Zend_Db_Expr('COUNT(*)')
                ))
            ->where("{$this->_reviewTable}.entity_pk_value = :pk_value");
        $bind = array(':pk_value' => $entityPkValue);
        if ($storeId > 0) {
            $select->join(array('store'=>$this->_reviewStoreTable),
                $this->_reviewTable.'.review_id=store.review_id AND store.store_id = :store_id',
                array());
            $bind[':store_id'] = (int)$storeId;
        }

        if (Mage::getStoreConfig('catalog/review/enhanced_reviews')) {
            if ($approvedOnly) {
                $select->where("{$this->_reviewTable}.status_id = :status_id OR {$this->_reviewTable}.status_id = :other_status_id");
                $bind[':status_id'] = Mage_Review_Model_Review::STATUS_APPROVED;
                $bind[':other_status_id'] = Blugento_Review_Model_Review::STATUS_APPROVED_AND_VERIFIED;
            }
        } else {
            if ($approvedOnly) {
                $select->where("{$this->_reviewTable}.status_id = :status_id");
                $bind[':status_id'] = Mage_Review_Model_Review::STATUS_APPROVED;
            }
        }

        return $adapter->fetchOne($select, $bind);
    }
}

