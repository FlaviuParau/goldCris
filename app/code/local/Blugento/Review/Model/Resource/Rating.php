<?php

class Blugento_Review_Model_Resource_Rating extends Mage_Rating_Model_Resource_Rating
{
    const RATING_STATUS_APPROVED_AND_VERIFIED = 'Approved And Verified';

    /**
     * Return data of rating summary
     *
     * @param Mage_Rating_Model_Rating $object
     * @return array
     */
    protected function _getEntitySummaryData($object)
    {
        $adapter    = $this->_getReadAdapter();

        $sumColumn      = new Zend_Db_Expr("SUM(rating_vote.{$adapter->quoteIdentifier('percent')})");
        $countColumn    = new Zend_Db_Expr("COUNT(*)");

        if (Mage::getStoreConfig('catalog/review/enhanced_reviews')) {
            $select = $adapter->select()
                ->from(array('rating_vote' => $this->getTable('rating/rating_option_vote')),
                    array(
                        'entity_pk_value' => 'rating_vote.entity_pk_value',
                        'sum'             => $sumColumn,
                        'count'           => $countColumn))
                ->join(array('review' => $this->getTable('review/review')),
                    'rating_vote.review_id=review.review_id',
                    array())
                ->joinLeft(array('review_store' => $this->getTable('review/review_store')),
                    'rating_vote.review_id=review_store.review_id',
                    array('review_store.store_id'))
                ->join(array('rating_store' => $this->getTable('rating/rating_store')),
                    'rating_store.rating_id = rating_vote.rating_id AND rating_store.store_id = review_store.store_id',
                    array())
                ->join(array('review_status' => $this->getTable('review/review_status')),
                    'review.status_id = review_status.status_id',
                    array())
                ->where('review_status.status_code = :status_code OR review_status.status_code = :other_status_code')
                ->group('rating_vote.entity_pk_value')
                ->group('review_store.store_id');
            $bind = array(':status_code' => self::RATING_STATUS_APPROVED, ':other_status_code' => self::RATING_STATUS_APPROVED_AND_VERIFIED);
        } else {
            $select = $adapter->select()
                ->from(array('rating_vote' => $this->getTable('rating/rating_option_vote')),
                    array(
                        'entity_pk_value' => 'rating_vote.entity_pk_value',
                        'sum'             => $sumColumn,
                        'count'           => $countColumn))
                ->join(array('review' => $this->getTable('review/review')),
                    'rating_vote.review_id=review.review_id',
                    array())
                ->joinLeft(array('review_store' => $this->getTable('review/review_store')),
                    'rating_vote.review_id=review_store.review_id',
                    array('review_store.store_id'))
                ->join(array('rating_store' => $this->getTable('rating/rating_store')),
                    'rating_store.rating_id = rating_vote.rating_id AND rating_store.store_id = review_store.store_id',
                    array())
                ->join(array('review_status' => $this->getTable('review/review_status')),
                    'review.status_id = review_status.status_id',
                    array())
                ->where('review_status.status_code = :status_code')
                ->group('rating_vote.entity_pk_value')
                ->group('review_store.store_id');
            $bind = array(':status_code' => self::RATING_STATUS_APPROVED);
        }

        $entityPkValue = $object->getEntityPkValue();
        if ($entityPkValue) {
            $select->where('rating_vote.entity_pk_value = :pk_value');
            $bind[':pk_value'] = $entityPkValue;
        }

        return $adapter->fetchAll($select, $bind);
    }
}
