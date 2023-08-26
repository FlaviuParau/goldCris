<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Review
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review helper
 *
 * @category   Mage
 * @package    Mage_Review
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Blugento_Review_Block_Helper extends Mage_Review_Block_Helper
{
	public function getReviewsCount()
	{
		$_helper = Mage::helper('review');
        $storeId = Mage::app()->getStore()->getStoreId();

        if ($_helper->isEnabled()) {
            $sql = "SELECT COUNT(r.review_id) as 'result'
                    FROM review r
                    JOIN review_detail rd
                    ON r.review_id = rd.review_id
                    WHERE r.entity_pk_value = {$this->getProduct()->getId()} 
                    AND r.status_id IN (1,4) 
                    AND rd.store_id IN (0, $storeId)";
        } else {
            $sql = "SELECT COUNT(r.review_id) as 'result'
                    FROM review r
                    JOIN review_detail rd
                    ON r.review_id = rd.review_id
                    WHERE r.entity_pk_value = {$this->getProduct()->getId()} 
                    AND r.status_id IN (1) 
                    AND rd.store_id IN (0, $storeId)";
        }
        try {
            $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
            $result = $conn->fetchRow($sql);
            return $result['result'];
        } catch (Exception $e) {
            echo $e->getMessage();
        }
	}

	public function getRatingSummary()
    {
        $_helper = Mage::helper('review');
        $storeId = Mage::app()->getStore()->getStoreId();

        if ($_helper->isEnabled()) {
            $sql = "SELECT rov.review_id, avg(rov.percent) as percent 
                    FROM rating_option_vote rov
                    WHERE rov.entity_pk_value = {$this->getProduct()->getId()} 
                    AND rov.review_id IN (
                        SELECT r.review_id 
                        FROM review r
                        JOIN review_detail rd
                        ON r.review_id = rd.review_id 
                        WHERE r.status_id IN (1,4)
                        AND rd.store_id = $storeId
                    )";
        } else {
            $sql = "SELECT rov.review_id, avg(rov.percent) as percent 
                    FROM rating_option_vote rov
                    WHERE rov.entity_pk_value = {$this->getProduct()->getId()} 
                    AND rov.review_id IN (
                        SELECT r.review_id 
                        FROM review r
                        JOIN review_detail rd
                        ON r.review_id = rd.review_id
                        WHERE r.status_id IN (1)
                        AND rd.store_id = $storeId
                    )";
        }
        try {
            $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
            $result = $conn->fetchRow($sql);
            return $result['percent'];
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
