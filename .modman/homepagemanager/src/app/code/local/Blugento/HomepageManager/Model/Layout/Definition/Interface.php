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
 * @package     Blugento_HomepageManager
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

interface Blugento_HomepageManager_Model_Layout_Definition_Interface
{
    /**
     * Get array of layout items
     * @return array
     */
    public function getItems();
    
    /**
     * Set message error for display 
     * It can be use to throw exception in case of error
     * @param string $type
     * @param string $message
     * @return Blugento_HomepageManager_Model_Layout_Definition_Interface
     * @throws Mage_Core_Exception
     */
    public function setMessage($type, $message);
    
    /**
     * Set session model to pass messages to client
     * @param Mage_Core_Model_Session_Abstract $session
     * @return Blugento_HomepageManager_Model_Layout_Definition_Interface
     */
    public function setSession(Mage_Core_Model_Session_Abstract $session);
}
