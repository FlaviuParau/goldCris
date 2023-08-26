<?php
/**
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
 * @package     Blugento_Newsletter
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Newsletter_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function isSubscribed(){
        $issubscribe= Mage::getModel('newsletter/subscriber')->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer())->isSubscribed();
        if(!Mage::getSingleton('customer/session')->isLoggedIn() || !$issubscribe){
            return true;
        }
        else{
            return false;
        }
    }
}