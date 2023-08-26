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

abstract class Blugento_HomepageManager_Model_Layout_Definition_Xml_Abstract
    extends Varien_Simplexml_Config
    implements Blugento_HomepageManager_Model_Layout_Definition_Interface
{
    /**
     * XML file name
     * @var string
     */
    private $_xmlFileName = 'homepage-layout.xml';

    /**
     * XML relative directory in skin theme
     * @var string
     */
    private $_xmlSkinDir = 'homepage';

    /**
     * Items array
     * @var array
     */
    protected $_items = array();

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
     * Root node definition file
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
     * @return Blugento_HomepageManager_Model_Layout_Definition_Xml_Abstract
     */
    abstract protected function _setHelper();
    
    /**
     * Set root node
     * @return Blugento_HomepageManager_Model_Layout_Definition_Xml_Abstract
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
     * @param mixed $storeId
     * @return array of Varien_Simplexml_Element
     */
    public function getItems($storeId = 0)
    {
        if (count($this->_items) || empty($this->_rootNode) || is_null($this->_helper)) {
            return $this->_items;
        }

        try {
            $helper = Mage::helper('blugento_homepagemanager');

            $appEmulation = Mage::getSingleton('core/app_emulation');

            $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation(
                $this->_helper->getLayoutDefinitionStore()
            );

            if (is_array($storeId)) {
                $storeId = $storeId[0];
            }
            $storeId = intval($storeId);

            $userDir = $helper->getUserDirectoryName();

            // Load store specific definition file
            $fileSkinPath = $userDir . DS . $this ->_xmlSkinDir . DS . 'store_' . $storeId . '_' . $this->_xmlFileName;
            $fileSpecsDefinitionXML = Mage::getDesign()->validateFile($fileSkinPath,  array('_type' => 'skin'));

            if (empty($fileSpecsDefinitionXML)) {
                if ($storeId > 0) {
                    // Load global store definition file
                    $storeId = 0;
                    $fileSkinPath = $userDir . DS . $this->_xmlSkinDir . DS . 'store_' . $storeId . '_' . $this->_xmlFileName;
                    $fileSpecsDefinitionXML = Mage::getDesign()->validateFile($fileSkinPath, array('_type' => 'skin'));

                    if (empty($fileSpecsDefinitionXML)) {
                        // Load default (old) file
                        $fileSkinPath = $userDir . DS . $this->_xmlSkinDir . DS . $this->_xmlFileName;
                        $fileSpecsDefinitionXML = Mage::getDesign()->validateFile($fileSkinPath, array('_type' => 'skin'));

                        if (empty($fileSpecsDefinitionXML)) {
                            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
                            $this->setMessage('error', "No specification Layout file is set on the default theme");
                            $this->_items = array();
                            return array();
                        }
                    }
                }
            }

            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

            $this->loadFile($fileSpecsDefinitionXML);

            return $this->getNode();

        } catch (Exception $e) {
            $this->_items = array();
            Mage::logException($e);
        }

        return $this->_items;
    }

    /**
     * Set message error for display
     * @param string $type
     * @param string $message
     * @return Blugento_HomepageManager_Model_Layout_Definition_Xml_Abstract
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
     * @return Blugento_HomepageManager_Model_Layout_Definition_Xml_Abstract
     */
    public function setSession(Mage_Core_Model_Session_Abstract $session)
    {
        $this->_session = $session;
        return $this;
    }
}
