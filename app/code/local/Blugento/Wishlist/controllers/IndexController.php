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
 * @package     Mage_Wishlist
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Wishlist front controller
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
require_once Mage::getModuleDir('controllers', 'Mage_Wishlist') . DS . 'IndexController.php';

class Blugento_Wishlist_IndexController extends Mage_Wishlist_IndexController
{
    /**
     * Adding new item
     *
     * @return Mage_Core_Controller_Varien_Action|void
     */
    public function addAction()
    {
        $this->_addItemToWishList();
    }

    //redirect to 404. Share form should not be accessible
    /**
     * Prepare wishlist for share
     */
//    public function shareAction()
//    {
//        $this->norouteAction();
//        return;
//    }

    //redirect to 404. Send Action should not be possible
    /**
     * Share wishlist
     *
     * @return Mage_Core_Controller_Varien_Action|void
     */
//    public function sendAction()
//    {
//        $this->norouteAction();
//        return;
//    }
}
