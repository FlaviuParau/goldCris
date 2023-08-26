<?php
class Blugento_Checkout_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * Save comment from agreement form to order
     *
     * @param $observer
     * @return mixed
     */
    public function saveOrderComment($observer)
    {
        if ( ! Mage::helper('blugento_checkout')->isEnabled()) {
            return false;
        }

        if ( ! Mage::helper('blugento_checkout')->isOrderCommentEnabled()) {
            return false;
        }

        if(Mage::helper('core')->isModuleEnabled('MW_Onestepcheckout') && Mage::getStoreConfig('onestepcheckout/config/enabled')){
            $comment = Mage::app()->getRequest()->getPost('onestepcheckout_comments');
            if(isset($comment)){
                $comment = nl2br(trim($comment));
                if(!empty($comment)) {
                    $order = $observer->getEvent()->getOrder();
                    $order->setCustomerNoteNotify(true);
                    $order->setCustomerNote($comment);
                }
            }
        }else{
            $comment = Mage::app()->getRequest()->getPost('order_comment');

            if (is_array($comment) && isset($comment['text'])) {
                $commentText = nl2br(trim($comment['text']));

                if ( ! empty($commentText)) {
                    $order = $observer->getEvent()->getOrder();
                    $order->setCustomerNoteNotify(true);
                    $order->setCustomerNote($commentText);
                }
            }
        }
    }
}
