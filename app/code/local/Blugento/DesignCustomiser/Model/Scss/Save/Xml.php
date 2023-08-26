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

class Blugento_DesignCustomiser_Model_Scss_Save_Xml extends Varien_Simplexml_Config
    implements Blugento_DesignCustomiser_Model_Scss_Save_Interface
{

    /**
     * XML file name
     * @var string
     */
    private $_xmlFileName = 'variable.xml';

    /**
     * XML relative directory in skin theme
     * @var string
     */
    private $_xmlSkinDir = 'scss';

    /**
     * XML File Path
     * @var string
     */
    public $_xmlPath = '';

    /**
     * Load XML data
     * @var bool
     */
    private $_loadedXMLData = false;

    /**
     * Apply preset template or user defined values
     * @var null|string
     */
    private $_applyTemplate = null;

    /**
     * Get file path
     * @return string
     */
    public function getFile()
    {
        if (!empty($this->_xmlPath)) {
            return $this->_xmlPath;
        }

        if ($this->_applyTemplate) {
            $this->_setSkinPresetsFilePath();
        } else {
            $this->_setSkinFilePath();
        }

        return $this->_xmlPath;
    }

    /**
     * Set the applied template name
     * @param string $templateName
     * @return Blugento_DesignCustomiser_Model_Scss_Save_Xml
     */
    public function setApplyTemplate($templateName)
    {
        $this->_applyTemplate = $templateName;
        $this->_setSkinPresetsFilePath();
        $this->_loadedXMLData = false;

        return $this;
    }

    /**
     * Unset the applied template
     * @return Blugento_DesignCustomiser_Model_Scss_Save_Xml
     */
    public function unsetApplyTemplate()
    {
        $this->_applyTemplate = null;
        $this->_setSkinFilePath();
        $this->_loadedXMLData = false;

        return $this;
    }

    /**
     * Get the apply template flag
     * @return string|null
     */
    public function getApplyTemplate()
    {
        return $this->_applyTemplate;
    }

    /**
     * Get skin dir xml path
     * @return bool|string
     */
    private function _setSkinFilePath()
    {
        try {
            $helper = Mage::helper('blugento_designcustomiser');

            $appEmulation = Mage::getSingleton('core/app_emulation');

            $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation(
                $helper->getScssDefinitionStore()
            );

            $fileSkinPath = $helper->getUserDirectoryName() . DS . $this ->_xmlSkinDir . DS . $this->_xmlFileName;

            $this->_xmlPath = Mage::getDesign()->validateFile($fileSkinPath,  array('_type' => 'skin'));

            if (!$this->_xmlPath) {
                $dir = Mage::getDesign()->getSkinBaseDir() . DS . $helper->getUserDirectoryName();

                if (!is_dir($dir)) {
                    @mkdir($dir, 0777);
                    $dir .= DS . $this->_xmlSkinDir;
                    @mkdir($dir, 0777);
                } else {
                    $dir .= DS . $this->_xmlSkinDir;
                    if (!is_dir($dir)) {
                        @mkdir($dir, 0777);
                    }
                }

                $this->_xmlPath = Mage::getDesign()->getSkinBaseDir() . DS . $fileSkinPath;
                $fileHandle = fopen($this->_xmlPath, 'a');
                fclose($fileHandle);
            }

            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }

    /**
     * Get skin dir xml path
     * @return bool|string
     */
    private function _setSkinPresetsFilePath()
    {
        try {
            $helper = Mage::helper('blugento_designcustomiser');
            $appEmulation = Mage::getSingleton('core/app_emulation');

            $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation(
                $helper->getScssDefinitionStore()
            );

            $templateName = $this->_applyTemplate !== null ? $this->_applyTemplate : 'default';

            $fileSkinPath = $helper->getTemplateDefinitionPath($templateName) . DS . 'scss' . DS . $this->_xmlFileName;

            $this->_xmlPath = Mage::getDesign()->validateFile($fileSkinPath, array('_type' => 'skin'));

            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

            if (!$this->_xmlPath) {
                // presets file doesn't exist, fallback to existing settings
                $this->unsetApplyTemplate();
                return $this->_setSkinFilePath();
            }

        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }

    /**
     * Save the collection data in file
     * @param Varien_Data_Collection $collection
     * @return Blugento_DesignCustomiser_Model_Scss_Save_Xml
     */
    public function save(Varien_Data_Collection $collection) 
    {
        $this->_getXmlData();

        if (is_null($this->_xml)) {
            return $this;
        }

        foreach ($collection as $item) {
            $this->_setVariableValue($item);
        }

        $dom = new DOMDocument("1.0");
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($this->_xml->asXML());

        $outputXML = $dom->saveXML($dom->documentElement);

        $outputXML =  '<?xml version="1.0" encoding="UTF-8"?>'."\n".$outputXML;

        $fileLink = fopen($this->getFile(), 'w');
        if(!$fileLink){
            
        }else{
            fwrite($fileLink, $outputXML);
            fclose($fileLink);
        }

        return $this;
    }

    /**
     * Get Variable Value
     * @param Blugento_DesignCustomiser_Model_Scss_Variable_Abstract $variable
     * @return string
     */
    public function getVariableValue(Blugento_DesignCustomiser_Model_Scss_Variable_Abstract $variable)
    {
        $value = '';

        $this->_getXmlData();

        $variableId = $variable->getId();

        if ($this->_xml->children() && $this->_xml->$variableId && $this->_xml->$variableId->value) {
            $value = (string)$this->_xml->$variableId->value;
        }

        return $value;
    }

    /**
     * Get Variable Value
     * @param string $variable
     * @return string
     */
    public function getVariableValueById($variableId)
    {
        $value = '';

        $this->_getXmlData();

        if ($this->_xml->children() && $this->_xml->$variableId && $this->_xml->$variableId->value) {
            $value = (string)$this->_xml->$variableId->value;
        }

        return $value;
    }

    /**
     * Get xml data
     * @return $this
     */
    private function _getXmlData()
    {
        if ($this->_loadedXMLData) {
            return $this;
        }

        $file = $this->getFile();

        try {

            if (!$this->loadFile($file) && !($this->_xml instanceof Varien_Simplexml_Element)) {
                $xml = simplexml_load_string("<scss></scss>", 'Varien_Simplexml_Element');

                if ($xml instanceof Varien_Simplexml_Element) {
                    $this->_xml = $xml;
                }
            } elseif (!$this->_xml->children() && $this->_xml->getName() != 'scss') {
                $this->_xml->addChild('scss');
            }

            $this->_loadedXMLData = true;
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }

    /**
     * @param Blugento_DesignCustomiser_Model_Scss_Variable_Abstract $variable
     * @return $this
     */
    private function _setVariableValue(Blugento_DesignCustomiser_Model_Scss_Variable_Abstract $variable)
    {
        $scss = $this->_xml;
        $variableId = $variable->getId();

        if ($scss->children() && $scss->$variableId) {
            if (!($scss->$variableId->value)) {
                $scss->$variableId->addChild('value', $variable->getSaveValue());
            } elseif ($variable->getSaveValue()) {
                $scss->$variableId->value[0] = $variable->getSaveValue();
            } elseif (!$variable->getSaveValue()) {
                if ($variable->getAllowNull()) {
                    $scss->$variableId->value[0] = '';
                } else {
                    unset($scss->$variableId[0]);
                }
            }
        } else {
            $variableNode = $scss->addChild($variableId);
            $variableNode->addChild('value', $variable->getSaveValue());
        }

        return $this;
    }
}
