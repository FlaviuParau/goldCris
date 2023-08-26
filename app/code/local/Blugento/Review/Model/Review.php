<?php

class Blugento_Review_Model_Review extends Mage_Review_Model_Review
{
	const STATUS_APPROVED_AND_VERIFIED = 4;

    public function getRatingCount($percent, $productId)
    {
        $sql1 = "SELECT COUNT(rov.percent) as 'result'
                 FROM rating_option_vote rov 
                 INNER JOIN  review r 
                 ON rov.review_id = r.review_id 
                 WHERE rov.entity_pk_value = {$productId} AND r.status_id IN (1,4) 
                 GROUP BY rov.percent 
                 HAVING rov.percent = {$percent}";

        $sql2 = "SELECT COUNT(*) as 'result'
                 FROM rating_option_vote rov 
                 INNER JOIN  review r 
                 ON rov.review_id = r.review_id 
                 WHERE rov.entity_pk_value = {$productId} AND r.status_id IN (1,4)";

        try {
            $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
            $result1 = $conn->fetchRow($sql1);
            $result2 = $conn->fetchRow($sql2);
            return round($result1['result'] / $result2['result'] * 100);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    public function getRatingCountperPercent($percent, $productId)
    {
        $sql= "SELECT COUNT(*) as 'result'
                 FROM rating_option_vote rov 
                 INNER JOIN  review r 
                 ON rov.review_id = r.review_id 
                 WHERE rov.entity_pk_value = {$productId} AND r.status_id IN (1,4) and rov.percent = {$percent}";

        try {
            $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
            $result= $conn->fetchRow($sql);

            return $result['result'] ;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    public function getAllRatingCount($productId)
    {
        $sql = "SELECT COUNT(*) as 'result'
                 FROM rating_option_vote rov 
                 INNER JOIN  review r 
                 ON rov.review_id = r.review_id 
                 WHERE rov.entity_pk_value = {$productId} AND r.status_id IN (1,4)";
        try {
            $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
            $result = $conn->fetchRow($sql);
            return $result['result'];
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }
}
