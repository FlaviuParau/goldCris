<?php

class Blugento_Review_IndexController extends Mage_Core_Controller_Front_Action
{
	/**
	 * Generate feed directly from link without validations.
	 */
	public function thumbsupAction()
	{
		$model = Mage::getModel('review/review')->load($this->getRequest()->getParam('id'), 'review_id');
		$thumbsUp = $model->getThumbsUp();
		$model->setThumbsUp($thumbsUp + 1)->save();
		echo $model->getThumbsUp();
	}
	
	public function thumbsdownAction()
	{
		$model = Mage::getModel('review/review')->load($this->getRequest()->getParam('id'), 'review_id');
		$thumbsDown = $model->getThumbsDown();
		$model->setThumbsDown($thumbsDown + 1)->save();
		echo $model->getThumbsDown();
	}
}
