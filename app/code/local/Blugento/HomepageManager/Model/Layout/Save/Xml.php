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

class Blugento_HomepageManager_Model_Layout_Save_Xml extends Varien_Simplexml_Config
    implements Blugento_HomepageManager_Model_Layout_Save_Interface
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
     * XML File Path
     * @var string
     */
    public $_xmlPath = '';

    /**
     * Get file path
     * @return string
     */
    public function getFile()
    {
        if (!empty($this->_xmlPath)) {
            return $this->_xmlPath;
        }

        $this->_setSkinFilePath();

        return $this->_xmlPath;
    }

    /**
     * Get skin dir xml path
     * @return bool|string
     */
    private function _setSkinFilePath()
    {
        try {
            $helper = Mage::helper('blugento_homepagemanager');

            $appEmulation = Mage::getSingleton('core/app_emulation');

            $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation(
                $helper->getLayoutDefinitionStore()
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
     * Save the collection data in file
     * @param Varien_Data_Collection $collection
     * @return Blugento_HomepageManager_Model_Layout_Save_Xml
     */
    public function save($collection, $items)
    {
        $xml = new DOMDocument( "1.0", "UTF-8" );
        $layout = $xml->appendChild($xml->createElement('layout'));
        $nodes = $layout->appendChild($xml->createElement('nodes'));

        foreach ($collection as $item) {
            $row = $nodes->appendChild($xml->createElement('row'));
            $row->appendChild($xml->createElement('id', $item['id']));
            $full_width = isset($item['full_width']) && $item['full_width'] ? 1 : 0;
            $row->appendChild($xml->createElement('full_width', $full_width));
            $cols = $row->appendChild($xml->createElement('cols'));

            foreach ($item['nodes'] as $node) {
                $col = $cols->appendChild($xml->createElement('col'));
                foreach ($node as $key => $value) {
                    $newCol = $col->appendChild($xml->createElement($key));
                    if (is_array($value)) {
                        $newCol->appendChild($xml->createCDATASection(json_encode($value)));
                    } else {
                        $newCol->appendChild($xml->createCDATASection($value));
                    }
                }
            }
        }

        $outputXML = $xml->saveXML();

        $stores = isset($items['stores']) ? $items['stores'] : array(0);

        $filename = $this->getFile();
        $xmlFilename = $this->_xmlFileName;
        foreach($stores as $storeId) {
            $storeFile = str_replace($xmlFilename, 'store_' . $storeId . '_' . $xmlFilename, $filename);
            $fileLink = fopen($storeFile, 'w');
            if ($fileLink !== false) {
                fwrite($fileLink, $outputXML);
                fclose($fileLink);
            }
        }

        return $this;
    }
    public function getXmlSkinPath()
    {
        return $this->_xmlSkinDir;
    }
    public function getXmlFilename()
    {
        return $this->_xmlFileName;
    }
}
