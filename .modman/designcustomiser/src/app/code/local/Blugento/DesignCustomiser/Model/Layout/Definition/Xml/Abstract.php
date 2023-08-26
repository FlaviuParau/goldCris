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

abstract class Blugento_DesignCustomiser_Model_Layout_Definition_Xml_Abstract
    extends Varien_Simplexml_Config
    implements Blugento_DesignCustomiser_Model_Layout_Definition_Interface
{
    /**
     * Variables array
     * @var array
     */
    protected $_variables = array();

    /**
     * Session model used to pass messages
     * @var Mage_Core_Model_Session_Abstract
     */
    protected $_session = null;
    
    /**
     * Helper class to get info about definition file and variable models
     * @var Mage_Core_Helper_Abstract 
     */
    protected $_helper = null;
    
    /**
     * Root node defintion file
     * @var string 
     */
    protected $_rootNode = '';

    /**
     * Constructor
     * Initializes XML for this configuration
     */
    public function __construct($sourceData = null) {
        parent::__construct($sourceData);
        $this->_setHelper();
        $this->_setRootNode();
    }
    
    /**
     * Set helper
     * @return Blugento_DesignCustomiser_Model_Layout_Definition_Xml_Abstract
     */
    abstract protected function _setHelper();
    
    /**
     * Set root node
     * @return Blugento_DesignCustomiser_Model_Layout_Definition_Xml_Abstract
     */
    abstract protected function _setRootNode();
    
    /**
     * Imports XML string
     *
     * @param  string $string
     * @return boolean
     */
    public function loadString($string)
    {
        if (is_string($string)) {
            $xml = simplexml_load_string($string, $this->_elementClass, LIBXML_NOCDATA);
            if ($xml instanceof Varien_Simplexml_Element) {
                $this->_xml = $xml;
                return true;
            }
        } else {
            Mage::logException(new Exception('"$string" parameter for simplexml_load_string is not a string'));
        }
        return false;
    }

    /**
     * Get variables
     * @return array of Varien_Simplexml_Element
     */
    public function getVariables()
    {
        if (count($this->_variables) || empty($this->_rootNode) || is_null($this->_helper)) {
            return $this->_variables;
        }

        try {
            $appEmulation = Mage::getSingleton('core/app_emulation');

            $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation(
                $this->_helper->getLayoutDefinitionStore()
            );

            $fileSpecsDefinitionXML = $this->_helper->getLayoutDefinitionFile();

            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

            if (empty($fileSpecsDefinitionXML)) {
                $this->setMessage('error', "No specification Layout file is set on the default theme.");
            }
            $this->loadFile($fileSpecsDefinitionXML);

            $totalChildren = $this->getNode($this->_rootNode)->children();

            if (!$totalChildren) {
                $this->setMessage('error', "No Layout variable definition in file.");
            }


            foreach ($this->getNode($this->_rootNode)->children() as $variable) {
                if ($variable instanceof Varien_Simplexml_Element) {
                    $this->_variables[] = $variable;
                }
            }

        } catch (Exception $e) {
            $this->_variables = array();
            Mage::logException($e);
        }

        return $this->_variables;
    }

    /**
     * Get variable type from XML node variable
     * @param Varien_Simplexml_Element $variable
     * @return string
     */
    public function getVariableType($variable)
    {
        if (is_null($this->_helper)) {
            return '';
        }
        
        $type = '';

        try {
            if ($variable instanceof Varien_Simplexml_Element) {
                $type = $variable->type;
            }
        } catch (Exception $e) {
            $type = '';
            Mage::logException($e);
        }

        return $this->_helper->getLayoutAllowedVariableType($type);
    }

    /**
     * Set message error for display
     * @param string $type
     * @param string $message
     * @return Blugento_DesignCustomiser_Model_Layout_Definition_Xml_Abstract
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
     * @return Blugento_DesignCustomiser_Model_Layout_Definition_Xml_Abstract
     */
    public function setSession(Mage_Core_Model_Session_Abstract $session)
    {
        $this->_session = $session;
        return $this;
    }
}
