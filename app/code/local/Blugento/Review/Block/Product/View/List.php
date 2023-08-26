<?php

class Blugento_Review_Block_Product_View_List extends Mage_Review_Block_Product_View_List
{
	public function getReviewsCollection()
	{
	    $_helper = Mage::helper('review');

	    if ($_helper->isEnabled()) {
            $option = Mage::app()->getRequest()->getParam('option');
			if (null === $this->_reviewsCollection) {
				$this->_reviewsCollection = Mage::getModel('review/review')->getCollection()
					->addStoreFilter(Mage::app()->getStore()->getId())
					->addFieldToFilter('status_id', array(Blugento_Review_Model_Review::STATUS_APPROVED_AND_VERIFIED, Mage_Review_Model_Review::STATUS_APPROVED))
					->addEntityFilter('product', $this->getProduct()->getId());
                switch ($option){
                    case 2:
                        $this->_reviewsCollection->setDateOrder();
                        $this->_reviewsCollection ->getSelect()->joinInner(
                            'rating_option_vote',
                            'main_table.review_id = rating_option_vote.review_id',
                            array('review_value' => 'rating_option_vote.value')
                        );
                        $this->_reviewsCollection->getSelect()->order('review_value DESC');
                        break;
                    case 3:
                        $this->_reviewsCollection->setDateOrder();

                        $this->_reviewsCollection ->getSelect()->joinInner(
                            'rating_option_vote',
                            'main_table.review_id = rating_option_vote.review_id',
                            array('review_value' => 'rating_option_vote.value')
                        );
                        $this->_reviewsCollection->getSelect()->order('review_value ASC');
                        break;
                    default:
                        $this->_reviewsCollection->setDateOrder();
				}
            }
		} else {
			if (null === $this->_reviewsCollection) {
				$this->_reviewsCollection = Mage::getModel('review/review')->getCollection()
					->addStoreFilter(Mage::app()->getStore()->getId())
					->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
					->addEntityFilter('product', $this->getProduct()->getId())
					->setDateOrder();
			}
		}
		return $this->_reviewsCollection;
	}
	
	public function getRatingCount($percent)
	{
		$ratingCount = Mage::getModel('review/review')->getRatingCount($percent, $this->getProduct()->getId());
		
		if (is_numeric($ratingCount)){
		    return $ratingCount;
        }
        return 0;
	}

    public function getRatingCountperPercent($percent)
    {
        $ratingCountperPercent = Mage::getModel('review/review')->getRatingCountperPercent($percent, $this->getProduct()->getId());
        
        if (is_numeric($ratingCountperPercent)){
            return $ratingCountperPercent;
        }
        return 0;
    }

    public function getAllRatingCount()
    {
        $allRatingCount = Mage::getModel('review/review')->getAllRatingCount($this->getProduct()->getId());
        
        if (is_numeric($allRatingCount)){
            return $allRatingCount;
        }
        return 0;
    }
}
