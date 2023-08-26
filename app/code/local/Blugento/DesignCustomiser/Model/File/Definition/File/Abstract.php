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
 * @package     Blugento_DesignCustomiser
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

abstract class Blugento_DesignCustomiser_Model_File_Definition_File_Abstract
    extends Varien_File_Object
    implements Blugento_DesignCustomiser_Model_File_Definition_Interface
{
    /**
     * Session model used to pass messages
     * @var Mage_Core_Model_Session_Abstract
     */
    protected $_session = null;
    
    /**
     * Helper class to get info about definition file
     * @var Mage_Core_Helper_Abstract 
     */
    protected $_helper = null;

    /**
     * Constructor
     * Initializes file for this configuration
     */
    public function __construct($sourceData = null) {
        if (is_array($sourceData)) {
            $sourceData = array_pop($sourceData);
        }
        parent::__construct($sourceData);
        $this->_setHelper();
    }
    
    /**
     * Set helper
     * @return Blugento_DesignCustomiser_Model_File_Definition_File_Abstract
     */
    abstract protected function _setHelper();

    /**
     * Get file content as string
     * @return string|null
     */
    abstract public function loadContent();
    
    /**
     * Set message error for display
     * @param string $type
     * @param string $message
     * @return Blugento_DesignCustomiser_Model_File_Definition_File_Abstract
     * @throws Mage_Core_Exception
     */
    public function setMessage($type, $message)
    {
        if (is_null($this->_helper)) {
            return $this;
        }
        
        if (is_null($this->_session)) {
            $this->_session = Mage::getSingleton('core/session');
        }

        switch (strtolower($type)) {
            case Mage_Core_Model_Message::ERROR :
                $this->_session->addError($this->_helper->__($message));
                throw Mage::exception('Mage_Core', $message);
            case Mage_Core_Model_Message::WARNING :
                $this->_session->addWarning($this->_helper->__($message));
                break;
            case Mage_Core_Model_Message::SUCCESS :
                $this->_session->addSuccess($this->_helper->__($message));
                break;
            default:
                $this->_session->addNotice($this->_helper->__($message));
                break;
        }

        return $this;
    }

    /**
     * Set session model
     * @param Mage_Core_Model_Session_Abstract $session
     * @return Blugento_DesignCustomiser_Model_File_Definition_File_Abstract
     */
    public function setSession(Mage_Core_Model_Session_Abstract $session)
    {
        $this->_session = $session;
        return $this;
    }
}
